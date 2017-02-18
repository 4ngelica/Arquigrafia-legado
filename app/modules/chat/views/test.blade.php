@extends('layouts.default')

@section('head')

<title>Arquigrafia - Teste </title>
<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>
<script type='text/javascript'>

</script>
<script src="https://js.pusher.com/3.2/pusher.min.js"></script>

@stop

@section('content')
	<h1>{{ $output }}</h1>
	<p id="message">Heya</p>

	<form action="create" method="GET">
    <p>Criar Chat</p>
		<input type="number" name="participants" value="" placeholder="Participante">
		<input type="text" name="subject" value="test" placeholder="Assunto">
		<input type="submit">
	</form>

	<form action="" method="POST">
    <p>Mandar chat</p>
		<input type="number" name="thread_id" value="6" placeholder="Chat Id">
		<input type="text" name="message" value="" placeholder="Mensagem">
		<input type="submit">
	</form>
@stop