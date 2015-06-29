@extends('layouts.default')

@section('head')

<title>Arquigrafia - Badges de {{ $user->name }}</title>

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />

<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>

@stop

@section('content')
		
	<?php if (Auth::check()) {
     
    $user = Auth::user();
	?>
    <h2 class="badges">Badges : </h2>
    @if ($user->badges == null)
        <p id="no-badges">no badges</p>
    @endif
	<ul class="badges-list">
    	@foreach($user->badges as $badge)
    	<li> <?php
    		$badge->render();
    		 ?></li>
    	@endforeach
	</ul>
 	<?php } ?> 
@stop