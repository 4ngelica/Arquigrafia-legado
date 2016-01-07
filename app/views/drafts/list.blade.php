@extends ('layouts.default')

@section ('head')
  <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
@stop

@section ('content')
  <div class="container">
    <a href="{{ URL::previous() }}" class="row">Voltar para a página anterior</a>
    <br>
    <h1 class="row">Informações prontas para upload ({{ $drafts->count() }})</h1>
    @foreach($drafts as $draft)
      <a href="{{ URL::to('/drafts/' . $draft->id) }}">
        <div class="three columns draft row">
          <ul>
            <li><strong>Título:</strong> {{ $draft->name }}</li>
            <li><strong>Suporte:</strong> {{ $draft->support }}</li>
            <li><strong>Tombo:</strong> {{ $draft->tombo }}</li>
            <li><strong>Suporte:</strong> {{ $draft->support }}</li>
            <li><strong>Caracterização:</strong> {{ $draft->characterization }}</li>
            <li><strong>...</strong></li>
          </ul>
        </div>
      </a>

    @endforeach
  </div>
@stop