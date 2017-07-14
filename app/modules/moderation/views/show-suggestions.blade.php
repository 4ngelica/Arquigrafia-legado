@extends ('layouts.default')

@section ('head')
  <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
@stop

@section ('content')
  <div class="container">
    <a href="{{ URL::previous() }}" class="row">Voltar para a página anterior</a>
    <br>
    <h1 class="row">Sugestões (<span class="suggestion_count">{{ count($suggestions) }}</span>)</h1>
    @include('suggestion-list')
  </div>
  <div id="mask"></div>
@stop
