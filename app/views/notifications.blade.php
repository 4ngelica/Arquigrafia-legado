@extends('layouts.default')

@section('head')

<title>Arquigrafia - Notificações</title>

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>

@stop

@section('content')
		
		<?php if (Auth::check()) {
        
        $user_id = Auth::user()->id;
		$user = User::find($user_id);
		$unreadNotifications = $user->notifications()->unread()->get();
		?>
		<ul>
			<li>{{ "You have notifications!" }}</li>
    		@foreach($user->notifications as $notification)
    		<li>{{ $notification->render() }}</li>
    		<?php $notification->read_at = "2015" ?>
    		@endforeach
    		<li>{{"Não lidas:"}}</li>
    		<li>{{$unreadNotifications}}</li>
		</ul>
 		<?php } else {
       		$home = new PagesController;
       		$home->home();
       		?>
       		<div style="margin: auto; width: 250px;">Faça o login para vizualizar suas notificações.</div>
        <?php } ?> 
@stop