@extends('layouts.default')

@section('head')

<title>Arquigrafia - Notificações de {{ $user->name }}</title>

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>

@stop

@section('content')
	<?php if (Auth::check()) {
     
    $user = Auth::user();
	$unreadNotifications = $user->notifications()->unread()->get();
	?>
	<h2 class="notifications">Suas notificações:</h2>
	@if ($user->notifications == null)
		<p id="no-notifications">Você não possui notificações.</p>
	@endif
	<ul class="notification-list">
    	@foreach($user->notifications as $notification)
    		<?php $info_array = $notification->render(); ?>
    		@if($info_array[0] == "photo_liked")
    			<li class="notes"><a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" curtiu sua " }} <a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{". $info_array[4], $info_array[3]."}}</li>
    		@elseif($info_array[0] == "comment_liked")
    			<li class="notes"><a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" curtiu seu comentário, na foto de ."}}</li>
    		@elseif($info_array[0] == "comment_posted")
    			<li class="notes"><a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" comentou sua foto."}}</li>
    		@endif
    	@endforeach
	</ul>
 	<?php } ?> 
@stop