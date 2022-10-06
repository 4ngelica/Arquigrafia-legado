@extends('layouts.default')

@section('head')
   <title>
      Arquigrafia - Entrar
   </title>
   <script type="text/javascript" src="{{ URL::to('/js/stoaLogin.js') }}"></script>
   <script type="text/javascript" src="{{ URL::to('/js/institutionLogin.js') }}"></script>
   <script type="text/javascript">
      var baseUrl = '{{ URL::to("/") }}';
   </script>
   <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
   <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
   <link rel="stylesheet" href="/css/landing.css">
@stop


@section('content')

  <div id="landing-carousel">
    <div class="carousel-cell" style="background-image: url('/img/landing/slide-bk-1.jpg');">
      <div class="container">
        <div class="six eight-xs columns offset-by-three offset-by-two-xs">
          <p>&nbsp;</p>
          <h2>Estamos em manutenção!
            Para garantir uma experiência melhor, o login e cadastro de novos usuários está pausado temporariamente.</h2>
            <p class="text-center"><a href="/home" class="btn">Home</a> &nbsp; <a href="mailto: arquigrafiabr@gmail.com" class="btn">Contato</a></p>
          <p>&nbsp;</p>
        </div>
      </div>
    </div>
  </div>

@stop

@section('scripts')
  <!-- <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script> -->
  <script type="text/javascript">
    var flkty = new Flickity('#landing-carousel', {
      cellAlign: 'left',
      contain: true
    });
  </script>
@stop
