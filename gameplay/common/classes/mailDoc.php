<?php

/**
 * Klasa wysyłająca mejle
 *
 */

class mailDoc {
  
  /**
   * Załączniki
   *
   * @var array()
   */
  
  protected $attachments = array ();
  
  /**
   * Załączniki do maila - style, obrazki
   *
   * @var array()
   */
  
  protected $inlineAttachments = array ();
  
  /**
   * Treść HTML
   *
   * @var HTML
   */
  
  protected $html = '';
  
  /**
   * Treść tekstowa
   *
   * @var string
   */
  
  protected $text = '';
  
  /**
   * Adres From
   *
   * @var string
   */
  
  protected $from;
  
  /**
   * Temat mejla
   *
   * @var string
   */
  
  protected $subject;

  /**
   * Tworzy obiekt
   *
   * @param string $subject Temat mejla
   * @param string $from Adres from
   */
  
  function __construct($subject, $from = null) {

    global $config;
    
    if ($from == null) {
      
      $this->from = $config ['smtp'] ['from'];
    
    }
    
    $this->attachments = array ();
    
    $this->inlineAttachments = array ();
    
    $this->subject = $subject;
    
    $this->mailID = uniqid ( 'soflinemail', true ) . '@' . preg_replace ( '!^.*@!', '', $from );
  
  }

  /*
   * Ustawia treść HTML
   * 
   * @param HTML $html HTML
   */
  
  function setHTML($html) {

    $this->html = $html;
  
  }

  /*
   * Ustawia treść tekstową
   * 
   * @param string $text Treść tekstowa 
   */
  
  function setText($text) {

    $this->text = $text;
  
  }

  /*
   * Dodaj plik jako załącznik
   * 
   * @param string $attachName Nazwa załącznika
   * @param string $fileName Nazwa pliku do załączenia
   */
  
  function attachFile($attachName, $fileName) {

    $data = file_get_contents ( $fileName );
    
    $this->attachData ( $attachName, $data );
  
  }

  /*
   * Dodaj zmienną jako załącznik
   * 
   * @param string $attachName Nazwa załącznika
   * @param string $data Dane do załączenia
   */
  
  function attachData($attachName, $data) {

    $this->attachments [$attachName] = $data;
  
  }

  /*
   * Dodaj plik jako załącznik do maila
   * 
   * @param string $attachName Nazwa załącznika
   * @param string $fileName Nazwa pliku do załączenia
   */
  
  function attachInlineFile($attachName, $fileName) {

    $outf = '';
    
    if (preg_match ( '!.png$!', $fileName )) {
      
      $outf = tempnam ( sys_get_temp_dir (), 'mailimg' );
      
      $im = imagecreatefrompng ( $fileName );
      
      imagejpeg ( $im, $outf );
      
      $attachName = preg_replace ( '!.png$!', '.jpg', $attachName );
    
    }
    
    $data = file_get_contents ( $fileName );
    
    if (! empty ( $outf )) {
      
      @unlink ( $outf );
    
    }
    
    return $this->attachInlineData ( $attachName, $data );
  
  }

  /*
   * Dodaj zmienną jako załącznik do maila
   * 
   * @param string $attachName Nazwa załącznika
   * @param string $data Dane do załączenia
   */
  
  function attachInlineData($attachName, $data) {

    $this->inlineAttachments [$attachName] = $data;
    
    return 'ai' . (count ( $this->inlineAttachments ) - 1) . $this->mailID;
  
  }

  /*
   * Wysyła mejla
   * 
   * @param string $to Odbiorca
   */
  
  function send($to) {

    global $config;
    
    $smtp = new smtp_class ( );
    
    $smtp->host_name = $config ['smtp'] ['host']; /* ADRES SERWERA SMTP */
    
    $smtp->host_port = $config ['smtp'] ['port']; /* Change this variable to the port of the SMTP server to use, like 465 */
    
    $smtp->ssl = $config ['smtp'] ['ssl']; /* Change this variable if the SMTP server requires an secure connection using SSL */
    
    $smtp->localhost = "localhost"; /* Your computer address */
    
    $smtp->direct_delivery = 0; /* Set to 1 to deliver directly to the recepient SMTP server */
    
    $smtp->timeout = 30; /* Set to the number of seconds wait for a successful connection to the SMTP server */
    
    $smtp->data_timeout = 0; /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
	                                          Set to 0 to use the same defined in the timeout variable */
    
    $smtp->debug = 1; /* Set to 1 to output the communication with the SMTP server */
    
    $smtp->html_debug = 0; /* Set to 1 to format the debug output as HTML */
    
    $smtp->pop3_auth_host = ""; /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
    
    $smtp->user = $config ['smtp'] ['user']; /* NAZWA UZYTKOWNIKA SKRZYNKI (bez @ i domeny) */
    
    $smtp->realm = ""; /* Set to the authetication realm, usually the authentication user e-mail domain */
    
    $smtp->password = $config ['smtp'] ['pass']; /* HASLO DOSTEPU DO SKRZYNKI */
    
    $smtp->workstation = ""; /* Workstation name for NTLM authentication */
    
    $smtp->authentication_mechanism = ""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
	                                         Leave it empty to make the class negotiate if necessary */
    
    /*
	  if($smtp->direct_delivery) {
	    if(!function_exists("GetMXRR")) {
	      include("getmxrr.php");
	    }
	  }
*/
    
    $this->genMessage ();
    
    if ($smtp->SendMessage ( $this->from, 

    array ($to ), 

    array (

    "From: " . $config ['smtp'] ['reply'], 

    "To: $to", 

    "Subject: " . $this->subject, 

    "Date: " . strftime ( "%a, %d %b %Y %H:%M:%S %Z" ), 

    "MIME-Version: 1.0", 

    'Content-Type: multipart/mixed; boundary="' . $this->boundary1 . '"' )

    , $this->message )) {
      
      return true;
    
    }
    
    $this->error = $smtp->error;
    
    return false;
  
  }

  /**
   * Generuję treść mejla
   */
  
  protected function genMessage() {

    $crlf = chr ( 10 ) . chr ( 13 );
    
    $crlf = "\n";
    
    $mimes = array (

    "ez" => "application/andrew-inset", 

    "hqx" => "application/mac-binhex40", 

    "cpt" => "application/mac-compactpro", 

    "doc" => "application/msword", 

    "bin" => "application/octet-stream", 

    "dms" => "application/octet-stream", 

    "lha" => "application/octet-stream", 

    "lzh" => "application/octet-stream", 

    "exe" => "application/octet-stream", 

    "class" => "application/octet-stream", 

    "so" => "application/octet-stream", 

    "dll" => "application/octet-stream", 

    "oda" => "application/oda", 

    "pdf" => "application/pdf", 

    "ai" => "application/postscript", 

    "eps" => "application/postscript", 

    "ps" => "application/postscript", 

    "smi" => "application/smil", 

    "smil" => "application/smil", 

    "wbxml" => "application/vnd.wap.wbxml", 

    "wmlc" => "application/vnd.wap.wmlc", 

    "wmlsc" => "application/vnd.wap.wmlscriptc", 

    "bcpio" => "application/x-bcpio", 

    "vcd" => "application/x-cdlink", 

    "pgn" => "application/x-chess-pgn", 

    "cpio" => "application/x-cpio", 

    "csh" => "application/x-csh", 

    "dcr" => "application/x-director", 

    "dir" => "application/x-director", 

    "dxr" => "application/x-director", 

    "dvi" => "application/x-dvi", 

    "spl" => "application/x-futuresplash", 

    "gtar" => "application/x-gtar", 

    "hdf" => "application/x-hdf", 

    "js" => "application/x-javascript", 

    "skp" => "application/x-koan", 

    "skd" => "application/x-koan", 

    "skt" => "application/x-koan", 

    "skm" => "application/x-koan", 

    "latex" => "application/x-latex", 

    "nc" => "application/x-netcdf", 

    "cdf" => "application/x-netcdf", 

    "sh" => "application/x-sh", 

    "shar" => "application/x-shar", 

    "swf" => "application/x-shockwave-flash", 

    "sit" => "application/x-stuffit", 

    "sv4cpio" => "application/x-sv4cpio", 

    "sv4crc" => "application/x-sv4crc", 

    "tar" => "application/x-tar", 

    "tcl" => "application/x-tcl", 

    "tex" => "application/x-tex", 

    "texinfo" => "application/x-texinfo", 

    "texi" => "application/x-texinfo", 

    "t" => "application/x-troff", 

    "tr" => "application/x-troff", 

    "roff" => "application/x-troff", 

    "man" => "application/x-troff-man", 

    "me" => "application/x-troff-me", 

    "ms" => "application/x-troff-ms", 

    "ustar" => "application/x-ustar", 

    "src" => "application/x-wais-source", 

    "xhtml" => "application/xhtml+xml", 

    "xht" => "application/xhtml+xml", 

    "zip" => "application/zip", 

    "au" => "audio/basic", 

    "snd" => "audio/basic", 

    "mid" => "audio/midi", 

    "midi" => "audio/midi", 

    "kar" => "audio/midi", 

    "mpga" => "audio/mpeg", 

    "mp2" => "audio/mpeg", 

    "mp3" => "audio/mpeg", 

    "aif" => "audio/x-aiff", 

    "aiff" => "audio/x-aiff", 

    "aifc" => "audio/x-aiff", 

    "m3u" => "audio/x-mpegurl", 

    "ram" => "audio/x-pn-realaudio", 

    "rm" => "audio/x-pn-realaudio", 

    "rpm" => "audio/x-pn-realaudio-plugin", 

    "ra" => "audio/x-realaudio", 

    "wav" => "audio/x-wav", 

    "pdb" => "chemical/x-pdb", 

    "xyz" => "chemical/x-xyz", 

    "bmp" => "image/bmp", 

    "gif" => "image/gif", 

    "ief" => "image/ief", 

    "jpeg" => "image/jpeg", 

    "jpg" => "image/jpeg", 

    "jpe" => "image/jpeg", 

    "png" => "image/png", 

    "tiff" => "image/tiff", 

    "tif" => "image/tif", 

    "djvu" => "image/vnd.djvu", 

    "djv" => "image/vnd.djvu", 

    "wbmp" => "image/vnd.wap.wbmp", 

    "ras" => "image/x-cmu-raster", 

    "pnm" => "image/x-portable-anymap", 

    "pbm" => "image/x-portable-bitmap", 

    "pgm" => "image/x-portable-graymap", 

    "ppm" => "image/x-portable-pixmap", 

    "rgb" => "image/x-rgb", 

    "xbm" => "image/x-xbitmap", 

    "xpm" => "image/x-xpixmap", 

    "xwd" => "image/x-windowdump", 

    "igs" => "model/iges", 

    "iges" => "model/iges", 

    "msh" => "model/mesh", 

    "mesh" => "model/mesh", 

    "silo" => "model/mesh", 

    "wrl" => "model/vrml", 

    "vrml" => "model/vrml", 

    "css" => "text/css", 

    "html" => "text/html", 

    "htm" => "text/html", 

    "asc" => "text/plain", 

    "txt" => "text/plain", 

    "rtx" => "text/richtext", 

    "rtf" => "text/rtf", 

    "sgml" => "text/sgml", 

    "sgm" => "text/sgml", 

    "tsv" => "text/tab-seperated-values", 

    "wml" => "text/vnd.wap.wml", 

    "wmls" => "text/vnd.wap.wmlscript", 

    "etx" => "text/x-setext", 

    "xml" => "text/xml", 

    "xsl" => "text/xml", 

    "mpeg" => "video/mpeg", 

    "mpg" => "video/mpeg", 

    "mpe" => "video/mpeg", 

    "qt" => "video/quicktime", 

    "mov" => "video/quicktime", 

    "mxu" => "video/vnd.mpegurl", 

    "avi" => "video/x-msvideo", 

    "movie" => "video/x-sgi-movie", 

    "ice" => "x-conference-xcooltalk", 

    "csv" => "text/csv", 

    'xls' => 'application/vnd.ms-excel' )

    ;
    
    $this->boundary1 = 'SOFTLINEBOUNDARY1' . md5 ( uniqid ( time () ) );
    
    $this->boundary2 = 'SOFTLINEBOUNDARY2' . md5 ( uniqid ( time () ) );
    
    $this->boundary3 = 'SOFTLINEBOUNDARY3' . md5 ( uniqid ( time () ) );
    
    $this->message = '';
    
    $this->message .= 'This is a multi-part message in MIME format.' . $crlf . $crlf;
    
    $this->message .= '--' . $this->boundary1 . $crlf;
    
    $this->message .= 'Content-Type: multipart/alternative; boundary="' . $this->boundary2 . '"' . $crlf . $crlf;
    
    $this->message .= '--' . $this->boundary2 . $crlf;
    
    $this->message .= 'Content-Type: text/plain; charset=UTF-8' . $crlf;
    
    $this->message .= 'Content-Transfer-Encoding: 8bit' . $crlf . $crlf;
    
    $this->message .= $this->text . $crlf;
    
    if (! empty ( $this->html )) {
      
      $this->message .= '--' . $this->boundary2 . $crlf;
      
      //        $this->message .= '--' . $this->boundary1.$crlf;      

      $this->message .= 'Content-Type: multipart/related; boundary="' . $this->boundary3 . '"' . $crlf . $crlf;
      
      $this->message .= '--' . $this->boundary3 . $crlf;
      
      $this->message .= 'Content-Type: text/html; charset=UTF-8' . $crlf;
      
      $this->message .= 'Content-Transfer-Encoding: 8bit' . $crlf . $crlf;
      
      $this->message .= $this->html . $crlf;
      
      if (count ( $this->inlineAttachments ) > 0) {
        
        $cnt = 0;
        
        foreach ( $this->inlineAttachments as $attachName => $data ) {
          
          $ftype = 'application/octet-stream';
          
          $path_parts = pathinfo ( $attachName );
          
          if (isset ( $mimes [$path_parts ['extension']] )) {
            
            $ftype = $mimes [$path_parts ['extension']];
          
          }
          
          $this->message .= '--' . $this->boundary3 . $crlf;
          
          $this->message .= "Content-Type: {$ftype}; name=\"{$attachName}\"" . $crlf;
          
          $this->message .= "Content-Transfer-Encoding: base64" . $crlf;
          
          $this->message .= "Content-ID: <ai" . $cnt . $this->mailID . ">" . $crlf;
          
          //$this->message .= "Content-Disposition: inline; filename=\"{$attachName}\"".$crlf.$crlf;          

          $this->message .= chunk_split ( base64_encode ( $data ) ) . $crlf . $crlf;
          
          $cnt ++;
        
        }
      
      }
      
      $this->message .= '--' . $this->boundary3 . '--' . $crlf;
    
    }
    
    $this->message .= $crlf . '--' . $this->boundary2 . "--" . $crlf;
    
    foreach ( $this->attachments as $attachName => $data ) {
      
      $ftype = 'application/octet-stream';
      
      $path_parts = pathinfo ( $attachName );
      
      if (isset ( $mimes [$path_parts ['extension']] )) {
        
        $ftype = $mimes [$path_parts ['extension']];
      
      }
      
      $this->message .= '--' . $this->boundary1 . $crlf;
      
      $this->message .= "Content-Type: {$ftype}; name=\"{$attachName}\"" . $crlf;
      
      $this->message .= "Content-Transfer-Encoding: base64" . $crlf;
      
      $this->message .= "Content-Disposition: attachment; filename=\"{$attachName}\"" . $crlf . $crlf;
      
      $this->message .= chunk_split ( base64_encode ( $data ) ) . $crlf . $crlf;
    
    }
    
    $this->message .= '--' . $this->boundary1 . '--' . $crlf;
  
  }

}

?>
