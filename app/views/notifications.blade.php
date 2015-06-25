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
	<ul>
    	@foreach($user->notifications as $notification)
    	<li>{{ $notification->render() }}</li>
    	@endforeach
	</ul>
 	<?php } ?> 
@stop