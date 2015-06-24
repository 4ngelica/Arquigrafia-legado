<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 

class CommentLikedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'comment_liked';

    public function render()
    {
        return 'this is a comment_liked notification';
    }
}

?>