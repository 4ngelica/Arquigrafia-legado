  @extends('layouts.default')

  @section('head')

  <title>Arquigrafia - {{ $photos->name }}</title>

  <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />
  <script type="text/javascript" src="{{ URL::to("/") }}/js/progressbar.js"></script>
  <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4fdf62121c50304d"></script>
  <!-- Google Maps API -->
  <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript">
  $(document).ready(function(){

    //MAP AND GEOREFERENCING CREATION AND SETTING
    var geocoder;
    var map;

    function initialize() {
      var street = "{{ $photos->street }}";
      var district = "{{ $photos->district }}";
      var city = "{{ $photos->city }}";
      var state = "{{ $photos->state }}";
      var country = "{{ $photos->country }}";
      var address;
      if (street) address = street + "," + district + "," + city + "-" + state + "," + country;
      else if (district) address = district + "," + city + "-" + state + "," + country;
      else address = city + "-" + state + "," + country;
      console.log(address);

      geocoder = new google.maps.Geocoder();

      var latlng = new google.maps.LatLng(-34.397, 150.644);
      var myOptions = {
        zoom: 15,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }

      map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          // map.fitBounds(results[0].geometry.bounds);
          var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
          });
        } else {
          console.log("Geocode was not successful for the following reason: " + status);
        }
      });
    }

    initialize();

  });
  </script>
  <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
  <script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
  <script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>
@stop
@section('content')

  @if (Session::get('message'))
    <div class="container">
      <div class="twelve columns">
          <div class="message">{{ Session::get('message') }}</div>
      </div>
    </div>
  @endif

  <!--   MEIO DO SITE - ÁREA DE NAVEGAÇ?Ã?O   -->
  <div id="content" class="container">
    <!--   COLUNA ESQUERDA   -->
    <div class="eight columns">
      <!--   PAINEL DE VISUALIZACAO - SINGLE   -->
      <div id="single_view_block">
        <!--   NOME / STATUS DA FOTO   -->
        <div>
          <div class="four columns alpha">
            <h1>
              <a href="{{ URL::to("/search?q=".$photos->name)}}"> {{ $photos->name }} </a>
            </h1>
          </div>
          <div id="img_top_itens" class="four columns omega">
            <span class="right" title="{{ $commentsMessage }}">
              <i id="comments"></i><small>{{ $commentsCount }}</small>
            </span>
            <span class="right" title="{{ $photos->likes->count() }} pessoas curtiram essa imagem">
              <i id="likes"></i> <small>{{ $photos->likes->count() }}</small>
            </span>
            @if ( $owner->equal(Auth::user()) )
              <span class="right">
                <a id="delete_button" href="{{ URL::to('/photos/' . $photos->id) }}" title="Excluir imagem"></a>
              </span>
            @endif
            @if ( !empty($photos->dataUpload) )
              <span class="right">
                <small>Inserido em:</small>
                <a class="data_upload" href="{{ URL::to("/search?q=".$photos->dataUpload."&t=up") }}">
                  {{ Photo::formatDatePortugues($photos->dataUpload) }}
                </a>
              </span>
            @endif
          </div>
        </div>
        <!--   FIM - NOME / STATUS DA FOTO   -->

        <!--   FOTO   -->
        <a class="fancybox" href="{{ URL::to("/arquigrafia-images")."/".$photos->id."_view.jpg" }}"
          title="{{ $photos->name }}" >
          <img class="single_view_image" style=""
            src="{{ URL::to("/arquigrafia-images")."/".$photos->id."_view.jpg" }}" />
        </a>
      </div>

      <!--   BOX DE BOTOES DA IMAGEM   -->
      <div id="single_view_buttons_box">
        @if (Auth::check())
          <ul id="single_view_image_buttons">
            <li>
              <a href="{{ URL::to('/albums/get/list/' . $photos->id) }}" title="Adicione aos seus álbuns" id="plus"></a>
            </li>
            <li>
              <a href="{{ asset('photos/download/'.$photos->id) }}" title="Faça o download" id="download" target="_blank"></a>
            </li>
            <li>
              <a href="{{ URL::to('/photos/' . $photos->id . '/evaluate?f=sb' )}}" title="Avalie {{$architectureName}}" id="evaluate" ></a>
            </li>
            <!-- LIKE-->
            @if(is_null($photoliked))
              <li>
                <a href="{{ URL::to('/photos/' . $photos->id . '/like' ) }}" id="like_button" title="Curtir"></a>
              </li>
            @else
              <li>
                <a href="{{ URL::to('/photos/' . $photos->id . '/dislike' ) }}" id="like_button" class="dislike" title="Descurtir"></a>
              </li>
            @endif
          </ul>
        @else
          <div class="six columns alpha">
            Faça o <a href="{{ URL::to('/users/login') }}">login</a> para fazer o download e comentar as imagens.
          </div>
        @endif

        <ul id="single_view_social_network_buttons">
          <li><a href="#" class="google addthis_button_google_plusone_share"><span class="google"></span></a></li>
          <li><a href="#" class="facebook addthis_button_facebook"><span class="facebook"></span></a></li>
          <li><a href="#" class="twitter addthis_button_twitter"><span class="twitter"></span></a></li>
        </ul>
      </div>
      <!--   FIM - BOX DE BOTOES DA IMAGEM   -->

      <div class="tags">
        <h3>Tags:</h3>
        <p>
          @if (isset($tags))
            @foreach($tags as $tag)
              @if ($tag->id == $tags->last()->id)
                <a style="" href="{{ URL::to("/search?q=".$tag->name) }}">
                  {{ $tag->name }}
                </a>
              @else
                <a href="{{ URL::to("/search?q=".$tag->name) }}">
                  {{ $tag->name }}
                </a>,
              @endif
            @endforeach
          @endif
        </p>
      </div>

      <!--   BOX DE COMENTARIOS   -->
      <div id="comments_block" class="eight columns row alpha omega">
        <h3>Comentários</h3>
        @if(Auth::check()) 
          <div class="text_comment" >
            <small>
              Cada usuário é responsável por seus próprios comentários.
              O Arquigrafia não se responsabiliza pelos comentários postados,
              mas apenas por tornar indisponível no site o conteúdo considerado
              infringente ou danoso por determinação judicial (art.19 da Lei 12.965/14).
            </small>
          </div>
          <br>
        @endif
        <?php $comments = $photos->comments; ?>

        @if (!isset($comments))
          <p>Ninguém comentou sobre {{$architectureName}}. Seja o primeiro!</p>
        @endif

        @if (Auth::check())
          {{ Form::open(array('url' => "photos/{$photos->id}/comment")) }}
            <div class="column alpha omega row">
              @if (Auth::user()->photo != "")
                <img class="user_thumbnail" src="{{ asset(Auth::user()->photo); }}" />
              @else
                <img class="user_thumbnail" src="{{ URL::to("/") }}/img/avatar-48.png" width="48" height="48" />
              @endif
            </div>

            <div class="three columns row">
              <strong><a href="#" id="name">{{ Auth::user()->name }}</a></strong><br>
              Deixe seu comentário <br>
              {{ $errors->first('text') }}
              {{ Form::textarea('text', '', ['id'=>'comment_field']) }}
              {{ Form::hidden('user', $photos->id ) }}
              {{ Form::submit('COMENTAR', ['id'=>'comment_button','class'=>'cursor btn']) }}
            </div>
          {{ Form::close() }}
          <br class="clear">
        @else
          <p>Faça o <a href="{{ URL::to('/users/login') }}">Login</a> e comente sobre {{ $architectureName }}</p>
        @endif

        @if (isset($comments))
          @foreach($comments as $comment)
            <div class="clearfix">
              <div class="column alpha omega row">
                @if ($comment->user->photo != "")
                  <img class="user_thumbnail" src="{{ asset($comment->user->photo); }}" />
                @else
                  <img class="user_thumbnail" src="{{ URL::to("/") }}/img/avatar-48.png" width="48" height="48" />
                @endif
              </div>
              <div class="four columns omega row">
                <small id={{"$comment->id"}}>
                  {{ $comment->user->name }} - {{ $comment->created_at->format('d/m/Y h:m') }}
                  <img src="{{ URL::to("/") }}/img/commentNB.png" / ><small class='likes'>{{ $comment->likes->count() }}</small>
                </small>
                <p>{{ $comment->text }}</p>

                @if (Auth::check())
                  @if(!$comment->isLiked())
                    <p> <a href="{{ URL::to('/comments/' . $comment->id . '/like' ) }}" class='like_comment' >Curtir</a></p>
                  @else
                    <p> <a href="{{ URL::to('/comments/' . $comment->id . '/dislike' ) }}" class='like_comment' class='dislike'>Descurtir</a></p>
                  @endif
                @endif
              </div>
            </div>
          @endforeach
        @endif
      </div>
      <!-- FIM DO BOX DE COMENTARIOS -->
      <!-- msy Avaliação similar-->
      @if (count($similarPhotos) > 0)
        <div id="comments_block" class="eight columns row alpha omega">
          <hgroup class="profile_block_title">
            <h3>
              <img src="{{ asset("img/evaluate.png") }}" width="16" height="16"/>
              Imagens avaliadas com média similar
            </h3>
            <span>({{count($similarPhotos) }})
              @if(count($similarPhotos)>1)
                 Imagens
              @else
                 Imagem
              @endif
            </span>
          </hgroup>

          @foreach($similarPhotos as $k => $similarPhoto)
            @if($photos->id != $similarPhoto->id)
              <a  class="hovertext" href='{{"/photos/" . $similarPhoto->id . "/showSimilarAverage" }}'
                class="gallery_photo" title="{{ $similarPhoto->name }}">
                <img src="{{ URL::to("/arquigrafia-images/" . $similarPhoto->id . "_home.jpg") }}" class="gallery_photo" />
              </a>
              <!--
              <a href='{{"/photos/" . $similarPhoto->id . "/evaluate" }}' class="name">
                <div class="innerbox">{{ $similarPhoto->name }}</div>
              </a>-->
            @endif
          @endforeach
        </div>
      @endif
      <!-- -->
    </div>
    <!--   FIM - COLUNA ESQUERDA   -->
    <!--   SIDEBAR   -->
    <div id="sidebar" class="four columns">
      <!--   USUARIO   -->
      <div id="single_user" class="clearfix row">
        <a href="{{ URL::to("/users/".$owner->id) }}" id="user_name">
          @if ($owner->photo != "")
            <img id="single_view_user_thumbnail" src="{{ asset($owner->photo) }}" class="user_photo_thumbnail"/>
          @else
            <img id="single_view_user_thumbnail" src="{{ URL::to("/") }}/img/avatar-48.png"
              width="48" height="48" class="user_photo_thumbnail"/>
          @endif
        </a>
        <h1 id="single_view_owner_name"><a href="{{ URL::to("/users/".$owner->id) }}" id="name">{{ $owner->name }}</a></h1>
        @if ( $owner->equal(Auth::user()) )
          @if (!empty($follow) && $follow == true )
            <a href="{{ URL::to("/friends/follow/" . $owner->id) }}" id="single_view_contact_add">Seguir</a><br />
          @else
            <div>Seguindo</div>
          @endif
        @endif
      </div>
      <!--   FIM - USUARIO   -->

      <hgroup class="profile_block_title">
        <h3><i class="info"></i> Informações</h3>
          &nbsp; &nbsp;
        @if ($owner->equal(Auth::user()))
          <a href= '{{"/photos/" . $photos->id . "/edit" }}' title="Editar informações da imagem">
          <img src="{{ asset("img/edit.png") }}" width="16" height="16"/>
          </a>
        @endif
      </hgroup>

      @if ( $owner->equal(Auth::user()) )
        <div id="image_info_completion">
          <h4>Completude das informações:</h4>
          <div id="progressbar"></div>
          @if ($photos->information_completion < 100)
            <p>
              <a id="improve_image_data" href="{{ URL::to('/photos/' . $photos->id . '/edit') }}">Deseja melhorar as informações da imagem?</a>
            </p>
            <div id="information_input" class="four columns alpha omega row" >
              <p><a href="#" class="close">X</a></p>
              {{ Form::open( array('url' => 'photos/' . $photos->id . '/set/field') ) }}
                <div class="four columns alpha omega">
                  <h3>{{ $question }}</h3>
                  {{ Form::hidden('field', $field) }}
                  @if ($field == "description")
                    <textarea name="description"></textarea>
                  @else
                    {{ Form::text($field, null) }}
                  @endif
                </div>
                <input type="submit" class="btn" value="SALVAR">
                <button class="btn" id="skip_question">PULAR</button>
              {{ Form::close() }}
              <img class="loader" src="{{ URL::to('/img/ajax-loader.gif') }}" />
            </div>
          @endif
        </div>
      @endif
      <div id="description_container">
      @if ( !empty($photos->description) )
        <h4>Descrição:</h4>
        <p>{{ $photos->description }}</p>
      @endif
      </div>
      @if ( !empty($photos->collection) )
        <h4>Coleção:</h4>
        <p>{{ $photos->collection }}</p>
      @endif
      <div id="imageAuthor_container">
      @if ( !empty($photos->imageAuthor) )
        <h4>Autor da Imagem:</h4>
        <p>
          <a href="{{ URL::to("/search?q=".$photos->imageAuthor)}}">
            {{ $photos->imageAuthor }}
          </a>
        </p>
      @endif
      </div>
      <div id="dataCriacao_container">
      @if ( !empty($photos->dataCriacao) )
        <h4>Data da Imagem:</h4>
        <p>
          <a href="{{ URL::to("/search?q=".$photos->dataCriacao."&t=img") }}">
            {{ Photo::translate($photos->dataCriacao) }}
          </a>
        </p>
      @endif
      </div>
      <div id="workAuthor_container">
      @if ( !empty($photos->workAuthor) )
        <h4>Autor da Obra:</h4>
        <p>
          <a href="{{ URL::to("/search?q=".$photos->workAuthor) }}">
            {{ $photos->workAuthor }}
          </a>
        </p>
      @endif
      </div>
      <div id="workdate_container">
      @if ( !empty($photos->workdate) )
        <h4>Data da Obra:</h4>
        <p>
          <a href="{{ URL::to("/search?q=".$photos->workdate."&t=work") }}">
            {{ Photo::translate($photos->workdate) }}
          </a>
        </p>
      @endif
      </div>
      @if ( !empty($photos->street) || !empty($photos->city) ||
        !empty($photos->state) || !empty($photos->country) )
        <h4>Endereço:</h4>
        <p>
          @if (!empty($photos->street) && !empty($photos->city))
            <a href="{{ URL::to("/search?q=".$photos->street."&city=".$photos->city) }}">
              {{ $photos->street }},
            </a>
          @elseif (!empty($photos->street))
            <a href="{{ URL::to("/search?q=".$photos->street) }}">
              {{ $photos->street }}
            </a>
            <br />
          @endif

          @if (!empty($photos->city))
            <a href="{{ URL::to("/search?q=".$photos->city) }}">
              {{ $photos->city }}
            </a>
            <br />
          @endif

          @if (!empty($photos->state) && !empty($photos->country))
            <a href="{{ URL::to("/search?q=".$photos->state) }}">{{ $photos->state }}</a> - {{ $photos->country }}
          @elseif (!empty($photos->state))
            <a href="{{ URL::to("/search?q=".$photos->state) }}">{{ $photos->state }}</a>
          @else
            {{ $photos->country }}
          @endif
        </p>
      @endif

      <h4>Licença:</h4>
      <!--
      <a href="http://creativecommons.org/licenses/{{$license[0]}}/3.0/deed.pt_BR" target="_blank"
       title='O proprietário desta imagem "{{ucfirst($owner->name)}}" : "{{$license[1]}}"'>
      -->
      <a class="tooltip_license"
        href="http://creativecommons.org/licenses/{{$license[0]}}/3.0/deed.pt_BR" target="_blank" >
        <img src="{{ asset('img/ccIcons/'.$license[0].'88x31.png') }}" id="ccicons"
          alt="Creative Commons License" />
        <span>
          @if (Auth::check())
            @if( $owner->equal(Auth::user()) )
              <strong>O proprietário desta imagem "{{ ucfirst($owner->name) }}" </strong><br />
            @endif
          @else
            <strong>O proprietário desta imagem "{{ ucfirst($owner->name) }}" : </strong><br />
          @endif
          "{{ $license[1] }}"
        </span>
      </a>
      </br>

       <!-- GOOGLE MAPS -->
      <h4>Localização:</h4>
      <div id="map_canvas" class="single_view_map" style="width:300px; height:250px;"></div>
      </br>

      <!-- AVALIAÇÃO -->
      @if (Auth::check())
        <a href="{{ URL::to('/photos/' . $photos->id . '/evaluate?f=g' ) }}">
      @endif

      @if (empty($average))
        <h4>Avaliação:</h4>
        <img src="/img/GraficoFixo.png" />
      @else
        <h4>
          <center>Média de Avaliações d{{ $architectureName }} </center>
        </h4>
        <br>
        <div id="evaluation_average"></div>
      @endif
      
      @if (Auth::check())
        </a>
      @endif

      @if (Auth::check())
        @if (isset($userEvaluations) && !$userEvaluations->isEmpty())
          <a href='{{"/photos/" . $photos->id . "/evaluate?f=c" }}' title="Avaliar" id="evaluate_button"
          class="btn">
            Clique aqui para alterar sua avaliação
          </a> &nbsp;
        @else
          @if (empty($average))
            <a href='{{"/photos/" . $photos->id . "/evaluate?f=c" }}' title="Avaliar" id="evaluate_button"
            class="btn">
              Seja o primeiro a avaliar {{$architectureName}}
            </a> &nbsp;
          @else
            <a href='{{"/photos/" . $photos->id . "/evaluate?f=c" }}' title="Avaliar" id="evaluate_button"
            class="btn">
              Avalie você também {{$architectureName}}
            </a> &nbsp;
          @endif
        @endif
      @else
        @if (empty($average))
          <p>
            Faça o <a href="{{ URL::to('/users/login') }}">Login</a> 
            e seja o primeiro a avaliar {{ $architectureName }}
          </p>
        @else
          <p>
            Faça o <a href="{{ URL::to('/users/login') }}">Login</a>
            e avalie você também {{ $architectureName }}
          </p>
        @endif
      @endif
    <!--   FIM - SIDEBAR   -->
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
            <a class="btn close" href="#">Cancelar</a>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  <script src="http://code.highcharts.com/highcharts.js"></script>
  <script type="text/javascript">
    $(function () {
      var l1 = [
          @foreach($binomials as $binomial)
            '{{ $binomial->firstOption}}',
          @endforeach
      ];
      var l2 = [
          @foreach($binomials as $binomial)
            '{{ $binomial->secondOption }}',
          @endforeach
      ];
      $('#evaluation_average').highcharts({
          credits: {
              enabled: false,
          },
          chart: {
              marginRight: 80,
              width: 311,
              height: 300
          },
          title: {
              text: ''
          },
          tooltip: {
            formatter: function() {
            return ''+ l1[this.y] + '-' + l2[this.y] + ': <br>' + this.series.name + '= ' + this.x;
            },
            crosshairs: [true,true]
          },
          xAxis: {
              lineColor: '#000',
              min: 0,
              max: 100,
          },
          yAxis: [{
              lineColor: '#000',
              lineWidth: 1,
              tickAmount: {{$binomials->count()}},
              tickPositions: [
                <?php $count = 0?>
                @foreach($binomials as $binomial)
                  {{ $count }},
                  <?php $count++; ?>
                @endforeach
              ],
              title: {
                  text: ''
              },
              labels: {
                formatter: function() {
                  return l1[this.value];
                }
              }
          }, {
              lineWidth: 1,
              tickAmount: {{$binomials->count()}},
              tickPositions: [
                <?php $count = 0?>
                @foreach($binomials as $binomial)
                  {{ $count }},
                  <?php $count++; ?>
                @endforeach
              ],
              opposite: true,
              title: {
                  text: ''
              },
              labels: {
                formatter: function() {
                  return l2[this.value];
                }
              },
          }],

          series: [{
              <?php $count = 0; ?>
              data: [
                @foreach($average as $avg)
                  [{{ $avg->avgPosition }}, {{ $count }}],
                  <?php $count++ ?>
                @endforeach
              ],
              yAxis: 1,
              name: 'Média',
              marker: {
                symbol: 'circle',
                enabled: true
              },
              color: '#999999',
          }, {
              <?php $count = 0; ?>
              data: [
                @if(isset($userEvaluations) && !$userEvaluations->isEmpty())
                  @foreach($userEvaluations as $userEvaluation)
                    [{{ $userEvaluation->evaluationPosition }}, {{ $count }}],
                    <?php $count++ ?>
                  @endforeach
                @endif
              ],
              yAxis: 0,
              name: 'Sua avaliação',
              marker: {
                symbol: 'circle',
                enabled: true
              },
              color: '#000000',
          }]
      });
    });
  </script>
  <script type="text/javascript">
    var getFieldURL = "{{ $getFieldURL }}";
    var setFieldURL = "{{ $setFieldURL }}";
    var currentField = 1;
    var hasField = true;
    var progressbar = $('#progressbar').progressbar();
    progressbar.progress( {{ $photos->information_completion }} );
  </script>
  
@stop