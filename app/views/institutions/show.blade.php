@extends('/layouts.default')

@section('head')
  <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
  <script src="{{ URL::to("/") }}/js/jquery.isotope.min.js"></script>
  <script type="text/javascript" src="{{ URL::to("/") }}/js/panel.js"></script>
  <link rel="stylesheet" type="text/css" media="screen"
    href="{{ URL::to("/") }}/css/checkbox.css" />
@stop

@section('content')

  @if (Session::get('message'))
      <div class="container">
        <div class="twelve columns">
            <div class="message">{{ Session::get('message') }}</div>
        </div>
      </div>
  @endif

    <!--   HEADER DO USUÁRIO   -->
  <div class="container">
    <div id="user_header" class="twelve columns">
      @if ( !empty($institution->photo) )
        <img class="avatar" src="{{ asset($institution->photo) }}"
          class="user_photo_thumbnail"/>
      @else
        <img class="avatar" src="{{ asset("img/avatar-institution.png") }}"
          width="60" height="60" class="user_photo_thumbnail"/>
      @endif
      <div class="info">
        <h1>{{ $institution->name}}</h1>
        @if ( !empty($user->city) )
          <p>{{ $user->city }}</p>
        @endif
        {{--
        @if (Auth::check() && $user->id != Auth::user()->id)
          @if ( !empty($follow) )
            <a href="{{ URL::to("/friends/follow/" . $user->id) }}"
              id="single_view_contact_add">Seguir</a><br />
          @else
            <div id="unfollow-button">
              <a href="{{ URL::to("/friends/unfollow/" . $user->id) }}">
                <p class="label success new-label"><span>Seguindo</span></p>
              </a>
            </div>
          @endif
        @endif
        --}}
      </div>
      <div class="count">Imagens compartilhadas({{ count($photos) }})</div>
    </div>
  </div>
  <!-- GALERIA DO USUÁRIO -->
  <div id="user_gallery">
    @if($photos->count() > 0)
      <div class="wrap">
        @include('includes.panel-stripe')
      </div>
    @else
      <div class="container row">
        <div class="six columns">
          <p>
            Não há imagens.
          </p>
        </div>
      </div>
    @endif
  </div>
  <br>
  <br>
  <!-- USUÁRIO -->
  <div class="container row">
    <div class="four columns">
      <hgroup class="profile_block_title">
        <h3><i class="profile"></i>Informações</h3>
      </hgroup>
      <ul>
        @if ( !empty($institution->name) )
          <li><strong>Nome: </strong>{{ $institution->name}}</li>
        @endif
        @if ( !empty($institution->email) )
          <li><strong>E-mail: </strong>{{ $institution->email }}</li>
        @endif
        @if ( !empty($institution->country) )
          <li><strong>País: </strong>{{ $institution->country }}</li>
        @endif
        @if ( !empty($institution->state) )
          <li><strong>Estado: </strong>{{ $institution->state }}</li>
        @endif
        @if ( !empty($institution->city) )
          <li><strong>Cidade: </strong>{{ $institution->city }}</li>
        @endif
        @if ( !empty($institution->site) )
          <li>
            <strong>Site pessoal: </strong>
            <a href="{{ $institution->site }}">{{ $institution->site }}</a>
          </li>
        @endif
      </ul>
    </div>
  {{--
    <div class="four columns">
      <hgroup class="profile_block_title">
        <h3><i class="follow"></i>
          Seguidores ({{$institution->followers->count()}})
        </h3>
        <!--<a href="#" id="small" class="profile_block_link">Ver todos</a>-->
      </hgroup>
      <!--   BOX - AMIGOS   -->
      <div class="profile_box">
        @foreach($institution->followers as $follower)
          <a href= {{ '/users/' .  $follower->id }} >
            @if ($follower->photo != "")
              <img width="40" height="40" class="avatar" src="{{ asset($follower->photo) }}" class="user_photo_thumbnail"/>
            @else
              <img width="40" height="40" class="avatar"
              src="{{ asset("img/avatar-60.png") }}" width="60" height="60" class="user_photo_thumbnail"/>
            @endif
          </a>
        @endforeach
      </div>
    </div>
  --}}
  </div>

    <!--   MODAL   -->
  <div id="mask"></div>
  <div id="form_window" class="form window">
    <a class="close" href="#" title="FECHAR">Fechar</a>
    <div id="registration"></div>
  </div>
  <div id="confirmation_window" class="window">
    <div id="registration_delete">
      <p></p>
      {{ Form::open(array('url' => '', 'method' => 'delete')) }}
        <div id="registration_buttons">
              <input type="submit" class="btn" value="Confirmar" />
          <a class="btn close" href="#" >Cancelar</a>
        </div>
      {{ Form::close() }}
    </div>
  </div>

@stop
