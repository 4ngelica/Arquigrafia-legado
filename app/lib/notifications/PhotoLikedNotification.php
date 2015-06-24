<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 

class PhotoLikedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'photo_liked';

    public function render() {
        return 'this is a photo_liked notification';
    }
}

?>