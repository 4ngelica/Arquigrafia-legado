<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 

class CommentPostedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'comment_posted';

	public function render() {
        return array($this->getTypes(), $this->getSender(), $this->getObjectID(), $this->getDate(), $this->getTime());
    }

    public function getDate() {
        $created_at = $this->created_at;
        list($date, $time) = explode(" ", $created_at);
        return $date;
    }

    public function getTime() {
        $created_at = $this->created_at;
        list($date, $time) = explode(" ", $created_at);
        return $time;
    }

    public function getTypes() {
        return $this->type;
    }

    public function getObjectID() {
        return $this->object_id;
    }

    public function getObjectType() {
        return $this->object_type;
    }

    public function getSender() {
        return User::find($this->sender_id)->name;
    }
}

?>