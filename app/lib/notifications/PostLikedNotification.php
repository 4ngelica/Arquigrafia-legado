<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 

class PostLikedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'post_liked';

    public function render()
    {
        return 'this is a post_liked notification';
    }
}

?>