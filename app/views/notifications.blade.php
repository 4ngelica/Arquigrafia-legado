@extends('layouts.default')

@section('head')

<title>Arquigrafia - Notificações de {{ $user->name }}</title>

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>

@stop

@section('content')
    <div id="content" class="container">
	<?php
        if (Auth::check()) {
            $user = Auth::user();
            $notes_size = count($user->notifications);
	        $unreadNotifications = $user->notifications()->unread()->get();
            $readNotifications = $user->notifications()->read()->get();
	?>
	<h2 class="notifications">Suas notificações:</h2>
	@if ($user->notifications->isEmpty())
		<p id="no-notifications">Você não possui notificações.</p>
	@else
	<ul>
    	@foreach($user->notifications->reverse() as $notification)
    		<?php
                $info_array = $notification->render(); 
            ?>
    		@if($info_array[0] == "photo_liked")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
    			<div id={{$notification->id}} class="notes<?php if($notification->read_at == null) echo ' not-read'?>" >
                    <li>
                        <div class="read-button" title="Marcar como lida" onclick="markRead(this);"></div>
                        <a href={{"photos/" . $info_array[2]}}><img class="mini" src={{"/arquigrafia-images/" . $info_array[2] . "_original.jpg"}}></a>
                        <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" curtiu sua " }} <a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}</br>
                        <p class="date">{{"$info_array[3], às $info_array[4]."}}</p>
                        <a class="link-block" href={{"photos/" . $info_array[2]}}></a>
                    </li>
                </div>
    		@elseif($info_array[0] == "comment_liked")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
    			<div id={{$notification->id}} class="notes<?php if($notification->read_at == null) echo ' not-read'?>">
                    <li>
                        <div class="read-button" title="Marcar como lida" onclick="markRead(this);"></div>
                        <a href={{"photos/" . $info_array[2]}}><img class="mini" src={{"/arquigrafia-images/" . $info_array[2] . "_original.jpg"}}></a>
                        <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" curtiu seu "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentário"}}</a>{{", na "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{" de "}}<a href={{"users/" . $info_array[6]}}>{{$info_array[7]}}</a>{{"."}}</br>
                        <p class="date">{{"$info_array[3], às $info_array[4]."}}</p>
                        <a class="link-block" href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}></a>
                    </li>
                </div>
    		@elseif($info_array[0] == "comment_posted")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
    			<div id={{$notification->id}} class="notes<?php if($notification->read_at == null) echo ' not-read'?>">
                    <li>
                        <div class="read-button" title="Marcar como lida"  onclick="markRead(this);"></div>
                        <a href={{"photos/" . $info_array[2]}}><img class="mini" src={{"/arquigrafia-images/" . $info_array[2] . "_original.jpg"}}></a>
                        <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" comentou sua "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}</br>
                        <p class="date">{{"$info_array[3], às $info_array[4]."}}</p>
                        <a class="link-block" href={{"photos/" . $info_array[2]}}><!--quando clickar, notificação será marcada como vista--></a>
                    </li>
                </div>
    		@endif
    	@endforeach
        <script>
            function markRead(object) {
                object.parentElement.parentElement.className = "notes";
                var id = object.parentElement.parentElement.id;
                var url = "/markRead/".concat(id);
                $.get(url)
                    .done(function( data ) {
                    var bubble = document.getElementById("bubble");
                    var noteIcon = document.getElementById("notification");
                    if (data != 1) noteIcon.title = "Você tem " + data + " notificações não lidas";
                    else noteIcon.title = "Você tem " + data + " notificação não lida";
                    if (data > 0) bubble.innerHTML = data;
                    else bubble.style.display = "none";     
                });
            }
        </script>
	</ul>
    @endif
 	<?php } ?> 
    </div>
@stop