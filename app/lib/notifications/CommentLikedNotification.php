<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 
use User;
use Comment;

class CommentLikedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'comment_liked';

    public function render() {
        return array($this->getTypes(), $this->getSender(), $this->getPhotoID(), $this->getDate(), $this->getTime(), $this->getSenderID());
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

    public function getCommentID() {
        return $this->object_id;
    }

    public function getPhotoID() {
        $comment = Comment::find($this->object_id);
        return $comment->photo_id;
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