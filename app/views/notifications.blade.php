@extends('layouts.default')

@section('head')

<title>Arquigrafia - Notificações</title>

@stop

@section('content')
		<?php

		$user_id = Auth::user()->id;
		$user = User::find($user_id);
		$unreadNotifications = $user->notifications()->unread()->get();

		?>

		<ul>
			<li>{{ "You have notifications!" }}</li>
    		@foreach($user->notifications as $notification)
    		<li>{{ $notification->render() }}</li>
    		@endforeach
		</ul>

@stop