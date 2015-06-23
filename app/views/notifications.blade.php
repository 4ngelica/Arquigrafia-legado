<?php

$user = User::find(2);

?>

<ul>
    @foreach($user->notifications as $notification)
    <li>{{ $notification->render() }}</li>
    @endforeach
</ul>