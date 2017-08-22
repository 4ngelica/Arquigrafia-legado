@extends ('layouts.default')

@section ('head')
  <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
  <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/suggestions/suggestions-list.css" />

  <script type="text/javascript">
  // Missing fields and questions (to show on Modal)
  var suggestions = {{ json_encode($suggestions) }};
  </script>
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