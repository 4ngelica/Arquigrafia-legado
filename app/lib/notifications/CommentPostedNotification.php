<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 
use User;

class CommentPostedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'comment_posted';

	public function render() {
        return array($this->getTypes(), $this->getSender(), $this->getPhotoID(), $this->getDate(), $this->getTime(), $this->getSenderID());
    }

    public function getDate() {
        $created_at = $this->created_at;
        list($date, $time) = explode(" ", $created_at);
        list($year, $month, $day) = explode("-", $date);
        return $day . "/" . $month . "/" . $year;
    }

    public function getTime() {
        $created_at = $this->created_at;
        list($date, $time) = explode(" ", $created_at);
        list($hour, $minutes, $seconds) = explode(":", $time);
        return $hour . ":" . $minutes;
    }

    public function getTypes() {
        return $this->type;
    }

    public function getPhotoID() {
        return $this->object_id;
    }

    public function getObjectType() {
        return $this->object_type;
    }

    public function getSenderID() {
        return $this->sender_id;
    }

    public function getSender() {
        return User::find($this->sender_id)->name;
    }
}

?>