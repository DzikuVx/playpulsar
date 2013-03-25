<?php

namespace Portal\Rss;

class Creator {
  /**
   * RSS file name
   *
   * @var string
   */
  protected $fileName = '';

  /**
   * Content
   *
   * @var string
   */
  protected $content = '';


  static function sFormatLink() {

    return 'rss/news_' . $_REQUEST ['language'] . '.xml';

  }

  /**
   * Constructor
   *
   * @param string $fileName
   * @param string $title
   * @param string $link
   * @param string $description
   */
  function __construct($fileName, $title, $link, $description = '') {

    $this->fileName = $fileName;

    $this->content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $this->content .= "<rss version=\"2.0\" \n xmlns:dc=\"http://purl.org/dc/elements/1.1/\" \n xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\" \n xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" \n xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" \n xmlns:admin=\"http://webns.net/mvcb/\" \n>\n";
    $this->content .= "<channel>\n";
    $this->content .= "<title>" . $title . "</title>\n";
    $this->content .= "<link>" . $link . "</link>\n";
    $this->content .= "<description>" . $description . "</description>\n";
    $this->content .= "<dc:language>en-us</dc:language>\n";
    $data = date ( "Y-m-d" );
    $godzina = date ( "H:i:s" );
    $this->content .= "<dc:date>$data" . "T" . "$godzina+01:00</dc:date>\n";
    $this->content .= "<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>\n";
    $this->content .= "<sy:updatePeriod>hourly</sy:updatePeriod>\n";
    $this->content .= "<sy:updateFrequency>1</sy:updateFrequency>\n";
  }

  /**
   * Insert RSS element
   *
   * @param int $id
   * @param string $title
   * @param string $link
   * @param int $time
   * @param string $fullText
   * @param int $size
   */
  public function put($id, $title, $link, $time, $fullText, $size = 255) {

    global $config;

    $title = htmlspecialchars ( $title, ENT_QUOTES );
    $shortText = htmlspecialchars ( strip_tags ( substr ( $fullText, 0, $size ) ) );

    $this->content .= "<item>\n";
    $this->content .= "<title>" . $title . "</title>\n";
    $this->content .= "<link>" . $config ['pageUrl'] . $link . "</link>\n";
    $this->content .= "<description>" . $shortText . "</description>\n";
    $this->content .= "<content:encoded><![CDATA[" . $fullText . "]]></content:encoded>\n";
    $this->content .= "<guid isPermaLink=\"false\">news-$id@" . $config ['pageUrl'] . "</guid>\n";
    $this->content .= "<dc:subject>News</dc:subject>\n";
    $data = date ( "Y-m-d", $time );
    $godzina = date ( "H:i:s", $time );
    $this->content .= "<dc:date>$data" . "T" . "$godzina+01:00</dc:date>\n";
    $this->content .= "</item>\n";
  }

  /**
   * Close RSS file
   *
   */
  public function close() {

    $this->content .= "</channel>\n";
    $this->content .= "</rss>";
    $tFile = fopen ( $this->fileName, 'w' );
    fputs ( $tFile, $this->content );
    fclose ( $tFile );
    unset ( $this );
  }

}