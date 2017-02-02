@extends('layouts.default')

@section('head')

<title>Arquigrafia - Teste </title>
<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />
<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>
<script type='text/javascript'>

</script>
@stop

@section('content')
	<h1>{{ $output }}</h1>
@stop