<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 
use User;

class PhotoLikedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'photo_liked';

    public function render() {
    	$created_at = $this->created_at;//object
    	$object_type = $this->object_type;//string
    	$object_id = $this->object_id;//integer
    	$sender_id = $this->sender_id;//integer
    	list($date, $time) = explode(" ", $created_at);//strings
    	$sender = User::find($sender_id)->name;
        return $sender . " " . "gostou da sua foto";
    }
}

?>