<?php
namespace Gameplay\Actions;

use Gameplay\Exception\SecurityException;
use Gameplay\Framework\ContentTransport;
use Gameplay\Model\UserEntity;
use Gameplay\Panel\Action;
use Gameplay\Panel\PortAction;
use Gameplay\Panel\SectorResources;
use Gameplay\Panel\SectorShips;
use General\Controls;

class Message {

    /**
     * @param int $messageID
     * @return bool
     * @throws SecurityException
     */
    static public function sDelete($messageID) {

        global $userID;

        $tMessage = new \Gameplay\Model\Message($messageID);

        if ($tMessage->Author != $userID && $tMessage->Receiver != $userID) {
            throw new SecurityException ( );
        }

        ContentTransport::getInstance()->addNotification( 'success', '{T:messageDeleted}');
        $tQuery = "DELETE FROM messages WHERE MessageID='{$messageID}' LIMIT 1";
        \Database\Controller::getInstance()->execute ( $tQuery );

        \phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey(\Gameplay\Model\Message::UNREAD_CACHE_KEY, $userID));

        \messageRegistry::sRender ();

        return true;
    }

    /**
     * @param int $messageID
     * @return bool
     * @throws SecurityException
     */
    static public function sGetDetail($messageID) {

        global $userID;

        SectorShips::getInstance()->hide ();
        SectorResources::getInstance()->hide ();
        PortAction::getInstance()->clear();

        $tMessage = new \Gameplay\Model\Message($messageID);

        if ($tMessage->Author != $userID && $tMessage->Receiver != $userID) {
            throw new SecurityException();
        }

        $sRetVal = "<h1>{T:messageDetail}</h1>";
        $sRetVal .= "<table class=\"table table-striped table-condensed\">";

        if (!empty($tMessage->Author)) {
            $sRetVal .= "<tr>";
            $sRetVal .= "<th style='width: 10em;'>{T:author}</th>";
            $sRetVal .= "<td style='cursor: pointer;' onclick=\"Playpulsar.gameplay.execute('shipExamine',null,null,'{$tMessage->Author}');\">" . $tMessage->SenderName . "</th>";
            $sRetVal .= "</tr>";
        }

        $sRetVal .= "<tr>";
        $sRetVal .= "<th>{T:date}</th>";
        $sRetVal .= "<td>" . \General\Formater::formatDateTime($tMessage->CreateTime) . "</td>";
        $sRetVal .= "</tr>";
        $sRetVal .= "<tr>";
        $sRetVal .= "<td colspan='2'>" . $tMessage->Text . "</td>";
        $sRetVal .= "</tr>";

        $sRetVal .= "</table>";

        $sRetVal .= Controls::bootstrapButton ( '{T:close}', "Playpulsar.gameplay.execute('showMessages');");

        if (!empty($tMessage->Author)) {
            $sRetVal .= Controls::bootstrapButton ( '{T:reply}', "Playpulsar.gameplay.execute('sendMessage',null,null,'{$tMessage->Author}');", 'btn-success');
        }

        $sRetVal .= Controls::bootstrapButton ( '{T:delete}', "Playpulsar.gameplay.execute('deleteMessage',null,null,'{$tMessage->MessageID}');", 'btn-danger');

        Action::getInstance()->add($sRetVal);

        $tQuery = "UPDATE messages SET Received='yes' WHERE MessageID={$messageID}";
        \Database\Controller::getInstance()->execute ( $tQuery );

        \phpCache\Factory::getInstance()->create()->clear(new \phpCache\CacheKey(\Gameplay\Model\Message::UNREAD_CACHE_KEY, $userID));

        return true;
    }

    /**
     * @param int $author
     * @param int $receiver
     * @param string $text
     */
    static public function sSendExecute($author, $receiver, $text) {

        \Gameplay\Model\Message::sInsert($author, $receiver, $text);

        ContentTransport::getInstance()->addNotification( 'success', '{T:messageSent}');
        \messageRegistry::sRender ();
    }

    /**
     * @param int $author
     * @param int $receiver
     */
    static public function sSend(/** @noinspection PhpUnusedParameterInspection */
        $author, $receiver) {

        SectorShips::getInstance()->hide ();
        SectorResources::getInstance()->hide ();
        PortAction::getInstance()->clear();

        $sRetVal = "<h1>{T:newMessage}</h1>";

        $sRetVal .= "<table class=\"table table-striped table-condensed\">";

        $otheruserParameters = new UserEntity($receiver);

        $sRetVal .= "<tr>";
        $sRetVal .= "<th style=\"width: 20em;\">{T:receiver}</th>";
        $sRetVal .= "<td>{$otheruserParameters->Name}</td>";
        $sRetVal .= "</tr>";

        $sRetVal .= "<tr>";
        $sRetVal .= "<th>{T:text}</th>";
        $sRetVal .= "<th>" . Controls::renderInput ( 'textarea', '', 'msgText', 'msgText', 1024 ) . "</th>";
        $sRetVal .= "</tr>";

        $sRetVal .= '</table>';
        $sRetVal .= "<div class=\"closeButton\" onClick=\"Playpulsar.gameplay.execute('sendMessageExecute',null,$('#msgText').val(),'{$receiver}');\">{T:send}</div>";

        Action::getInstance()->add($sRetVal);
    }
} 