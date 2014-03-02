<?php
namespace Gameplay\Model;

class Message extends CustomGet {

    protected $tableName = "messages";
    protected $tableID = "MessageID";
    protected $tableUseFields = null;
    protected $cacheExpire = 3600;

    /**
     * @var int
     */
    public $MessageID;

    /**
     * @var int
     */
    public $Author;

    /**
     * @var int
     */
    public $Receiver;

    /**
     * @var int
     */
    public $CreateTime;

    /**
     * @var string
     */
    public $Text;

    /**
     * @var string
     */
    public $SenderName;

    const UNREAD_CACHE_KEY = 'message::sGetUnreadCount';

    /**
     * @return bool
     */
    public function get() {

        $tResult = \Database\Controller::getInstance()->execute ( "
            SELECT
                messages.*,
                sender.Name AS SenderName
            FROM
                messages LEFT JOIN users AS sender ON sender.UserID=messages.Author
            WHERE
                MessageID='{$this->entryId}'
            LIMIT 1");
        while ($resultRow = \Database\Controller::getInstance()->fetch($tResult)) {
            $this->loadData($resultRow, false);
        }

        return true;
    }

    /**
     * @param int $userID
     * @return int
     */
    static public function sGetUnreadCount($userID) {

        $oCacheKey = new \phpCache\CacheKey(self::UNREAD_CACHE_KEY, $userID);
        $oCache    = \phpCache\Factory::getInstance()->create();

        if(!$oCache->check($oCacheKey)) {
            $tQuery = "SELECT COUNT(*) AS ILE FROM messages WHERE Receiver='{$userID}' AND Received='no'";
            $tQuery = \Database\Controller::getInstance()->execute ( $tQuery );
            $retVal = \Database\Controller::getInstance()->fetch ( $tQuery )->ILE;

            $oCache->set($oCacheKey, $retVal);
        } else {
            $retVal = $oCache->get($oCacheKey);
        }

        return $retVal;
    }

    /**
     * @param int $author
     * @param int $receiver
     * @param string $text
     * @return boolean
     * @since 2010-07-31
     */
    static public function sInsert($author, $receiver, $text) {

        $retVal = true;

        try {

            $text = \Database\Controller::getInstance()->quote($text);

            if ($author == null) {
                $author = "null";
            } else {
                $author = "'".$author."'";
            }

            $tQuery = "INSERT INTO messages(Author, Receiver, Text, CreateTime) VALUES({$author},'{$receiver}','{$text}','" . time () . "')";
            \Database\Controller::getInstance()->execute($tQuery);

            $oCacheKey = new \phpCache\CacheKey(self::UNREAD_CACHE_KEY, $receiver);
            $oCache    = \phpCache\Factory::getInstance()->create();

            $tVal = $oCache->get($oCacheKey);
            if (empty($tVal)) {
                $tVal = 0;
            }
            $tVal++;

            $oCache->set($oCacheKey, $tVal);

        } catch (\Exception $e) {
            $retVal = false;
        }

        return $retVal;
    }
}