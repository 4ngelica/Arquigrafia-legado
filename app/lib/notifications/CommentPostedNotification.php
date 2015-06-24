<?php
namespace lib\notifications;

use \Tricki\Notification\Models\Notification; 

class CommentPostedNotification extends \Tricki\Notification\Models\Notification
{
    public static $type = 'comment_posted';

    public function render()
    {
        return 'this is a comment_posted notification';
    }
}

?>