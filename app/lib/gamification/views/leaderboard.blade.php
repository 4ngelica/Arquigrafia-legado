@extends('layouts.default')

@section('head')
  <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
@stop

@section('content')
  @if (Session::get('message'))
    <div class="container">
      <div class="twelve columns">
        <div class="message">{{ Session::get('message') }}</div>
      </div>
    </div>
  @endif
  <div class="container">
    <div class="twelve columns">
      <div id="leaderboard" class="ten columns offset-by-one">
        <h1>Quadro dos Maiores Colaboradores</h1>
        <p>
          Ordenar por número de {{ Form::select('score_type',
            [ 
              'points' => 'pontos',
              'uploads' => 'uploads',
              'evaluations' => 'avaliações'
            ], $score_type) }}
        </p>
        <table class="form-table row" width="100%" cellspacing="0" cellpadding="0">
          <thead>
            <tr>
              <th>Posição</th>
              <th colspan="2">Colaborador</th>
              <th>Pontuação</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users->getCollection()->sortByDesc($score_type) as $user)
              @if ( $count % 2 == 0)
                <tr class="even">
              @else
                <tr>
              @endif
                <td><h2>{{ $count++ }}</h2></td>
                <td class="image">
                  <a href="{{ URL::to('/users/' . $user->id) }}">
                    @if ( ! empty($user->photo) )
                      <img src="{{ asset($user->photo) }}"
                        class="user_photo_thumbnail">
                    @else
                      <img src="{{ asset("img/avatar-60.png") }}"
                        class="user_photo_thumbnail">
                    @endif
                  </a>
                </td>
                <td class="name">
                  <p>{{ link_to('/users/' . $user->id, $user->name) }}</p>
                </td>
                <td><p class="score">{{ $user->$score_type }}</p></td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="eight columns">
          @if ($users->getCurrentPage() <= 1)
            <a id="less" href="#" class="disabled less-than" onclick="return false;"> &lt; </a>
          @else
            <a id="less"
              href="{{ URL::to('/leaderboard?type=' . $score_type . '&page=' . ($users->getCurrentPage() - 1)) }}"
              class="less-than"> &lt; </a>
          @endif
          &nbsp;
          {{ Form::text('page', $users->getCurrentPage(), array('style' => 'width: 30px;')) }}
          / {{ $users->getLastPage() }}
          &nbsp;
          @if ($users->getCurrentPage() >= $users->getLastPage())
            <a id="greater" href="#" class="disabled greater-than" onclick="return false;"> &gt; </a>
          @else
            <a id="greater"
              href="{{ URL::to('/leaderboard?type=' . $score_type . '&page=' . ($users->getCurrentPage() + 1)) }}"
              class="greater-than"> &gt; </a>
          @endif
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    var paginator = {
      current_page: {{ $users->getCurrentPage() }},
      max_page: {{ $users->getLastPage() }},
      score_type: '{{ $score_type }}',
      number_items: {{ $users->count() }},
      url: '{{ URL::to('/leaderboard') }}'
    };
  </script>
  <script type="text/javascript" src={{ asset('/js/leaderboard.js') }}></script>
@stop