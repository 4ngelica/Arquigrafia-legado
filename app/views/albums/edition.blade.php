@extends('layouts.default')

@section('head')
  <title>Arquigrafia - tabs</title>
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/css/tabs.css') }}">
  <script src="{{ URL::to('/js/albums-covers.js') }}"></script>
  <script src="{{ URL::to('/js/album-add-photos.js') }}"></script>
  <script src="{{ URL::to('/js/album.js') }}"></script>
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/checkbox.css" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/album.css" />
  <script>
    var currentPage = 1;
    var rmCurrentPage = 1;
    var maxPage = {{ $maxPage }};
    var rmMaxPage = {{ $rmMaxPage }};
    var loadedPages = [1];
    var rmLoadedPages = [1];
    var url = '{{ $url }}';
    var rmUrl = '{{ $rmUrl }}';
    var coverPage = 1;
    var maxCoverPage = {{ ceil($album->photos->count() / 48) }};
    var album = {{ $album->id }};
    var covers_counter = 0;
    $(document).ready(function() {
      $("#add-container").hide();
    });
  </script>
@stop

@section('content')
  <div class="container">
    <div class="twelve columns">
      <h1 id="album_title">Edição de {{ $album->title }}</h1>
      <p>* Os campos a seguir são obrigatórios.</p>
      </p>      
    </div>
    <div class="twelve columns">
      <div class="tabs">
        <ul class="tab-links">
          <li><a href="#album_images">Imagens do álbum</a></li>
          <li class="active"><a href="#album_info">Informações do álbum</a></li>
          <li><a href="#add_images">Adicionar imagens</a></li>
        </ul>
        <div class="tab-content">
          <div id="album_images" class="tab">
          <?php 
            $photos = $album_photos;
            $type = 'rm';
          ?>
          
          </div>
          <div id="album_info" class="tab active">
            {{ Form::open(array('url' => '/albums/' . $album->id . '/update/info', 'method' => 'post')) }}
              <div class="eleven columns">
                <div class="five columns">
                  <div class="four columns center">
                    <p><label for="cover_img">Capa do álbum</label></p>
                    <div class="img_container"> 
                      @if( isset($album->cover_id) )
                        <img id="cover-img" src="{{ URL::to('/arquigrafia-images/' . $album->cover_id . '_view.jpg') }}">
                      @endif
                      <?php $photos = $album_photos; ?>
                      @if ($photos->count() > 0)
                        <span><a class="cover_btn" href="#">Alterar capa</a></span>
                      @endif
                    </div>
                    <a class="cover_btn" href="#">Alterar capa</a>
                    {{ Form::hidden('_cover', $album->cover_id, ['id' => '_cover']) }}
                  </div>
                </div>
                <div id="info" class="five columns">
                  <div class="four columns"><p>{{ Form::label('title', 'Título*') }}</p></div>
                  <div class="four columns">
                    <p>{{ Form::text('title', $album->title) }} <br>
                      <div class="error"></div>
                    </p>
                  </div>
                  <div class="four columns"><p>{{ Form::label('description', 'Descrição') }}</p></div>
                  <div class="four columns">
                    <p>{{ Form::textarea('description', $album->description) }}</p>
                  </div>
                  <div class="four columns">
                    <p>{{ Form::submit('ATUALIZAR', array('class' => 'btn')) }}</p>
                  </div>
                </div>
              </div>
            {{ Form::close() }}
          </div>
          <div id="add_images" class="tab">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="mask"></div>
  <div id="form_window" class="form window">
    <a class="close" href="#" title="FECHAR">Fechar</a>
    <div id="covers_registration"></div>
  </div>
  <div class="success"></div>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.tabs .tab-links a').on('click', function(e) {
        var currentAttrValue = $(this).attr('href');
        $('.tabs ' + currentAttrValue).fadeIn('slow').siblings().hide();
        $(this).parent('li').addClass('active').siblings().removeClass('active');
        e.preventDefault();
      });
    });
  </script>
@stop