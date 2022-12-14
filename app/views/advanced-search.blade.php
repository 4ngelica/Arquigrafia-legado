@extends('layouts.default')

@section('head')

<title>Arquigrafia - Seu universo de imagens de arquitetura</title>

<!-- ISOTOPE -->
<script src="{{ URL::to("/") }}/js/jquery.isotope.min.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/panel.js"></script>

<!-- AUTOCOMPLETE -->
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.core.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.tags.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/styletags.css" />

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.core.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.tags.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.autocomplete.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.suggestions.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.filter.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tags-autocomplete.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.ajax.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tag-list.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tag-autocomplete-part.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/city-autocomplete.js" charset="utf-8"></script>

<link rel="stylesheet" href="{{ URL::to("/") }}/css/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />
<!-- pages -->
<script src="{{ URL::to('/js/searchPagination.js') }}"></script>
<script src="{{ URL::to('/js/albums-covers.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to('/css/tabs.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/album.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/checkbox-edition.css" />
<script>
    var paginators = {
      add: {
        currentPage: {{ $page}},//1,
        maxPage: {{ $maxPage }},
        url: '{{ $url }}',
        loadedPages: [1],
        selectedItems: 0,
        searchQuery: '',
        selected_photos: 0,
      }
    };
    var coverPage = 1;
    var covers_counter = 0;
    var update = null;

  </script>
@stop

@section('content')
    @if (isset($message))
    <div class="container">
      <div class="row">
      <div class="twelve columns">
        <div class="message">{{ $message }}</div>
      </div>
      </div>
    </div>
  @endif
    <!--   MEIO DO SITE - ??REA DE NAVEGA????O   -->
    <div id="content">
      <div class="container">
        <div id="search_result" class="twelve columns row">
          <h1>Busca avan??ada</h1>
          <div class="twelve columns alpha">
            <p>
            Apenas os campos que forem preenchidos abaixo ser??o considerados na busca,
             para trazer as imagens que correspondam a todos os crit??rios informados.
            </p>
          </div>
        </div>
        {{ Form::open(array('url' => 'search/more', 'id'=>'advanceSearch' ,'method' => 'get')) }}
          <div class="eight columns omega row">
            <div class="twelve columns alpha row">
              <div class="six columns">
                <h3>Descri????o</h3>
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('name', 'T??tulo da imagem:') }}
                    </td>
                    <td>
                      {{ Form::text('name', Input::get("name"), array('style' => "width: 90%")) }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      {{ Form::label('description', 'Descri????o da imagem:') }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      {{ Form::textarea('description', Input::get("description"),
                        array('style' => "width: 95.5%", 'rows' => 3)) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('tags_input', 'Tags*:') }}
                      <p style="font-size: 7pt">M??ximo 5 tags</p>
                    </td>
                    <td>
                      <div class="two columns alpha" style="width: 95% !important;">
                        {{ Form::text('tags_input', null, array('style' => "width: 95%")) }} <br>
                        <p>
                          <button class="btn right" id="add_tag">ADICIONAR TAG</button>
                        </p>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <textarea name="tags" id="tags" cols="35" rows="1" style="display: none;"></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="error"></div>
                    </td>
                  </tr>
                </table>
              </div>

              <div class="six columns">
                <h3>Arquitetura</h3>
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">

                  <tr>
                    <td>
                      {{ Form::label('workdate', 'Data da obra:') }}
                    </td>
                    <td>
                      {{ Form::text('workdate',Input::get("workdate"),
                        array('id' => 'datePickerWorkDate')) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('dataCriacao', 'Data da imagem:') }}
                    </td>
                    <td>
                      {{ Form::text('dataCriacao',Input::get("dataCriacao"),
                        array('id' => 'datePickerdataCriacao')) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('dataUpload', 'Data de upload:') }}
                    </td>
                    <td>
                      {{ Form::text('dataUpload',Input::get("dataUpload"),
                        array('id' => 'datePickerdataUpload')) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('tombo', 'N??mero de Tombo:') }}
                    </td>
                    <td>
                      {{ Form::text('tombo', Input::get("tombo") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('imageAuthor', 'Autor da imagem:') }}
                    </td>
                    <td>
                      {{ Form::text('imageAuthor', Input::get("imageAuthor") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('workAuthor', 'Autor da obra:') }}
                      <p style="font-size: 7pt">M??ximo 3 Autores</p>
                    </td>
                    <td>


                        <div class="two columns alpha" style="width: 88% !important;">
                        {{ Form::text('workAuthor', null , array('style' => "width: 88%")) }} <br>
                          <p>
                            <button class="btn right" id="add_author">ADICIONAR AUTOR</button>
                          </p>
                        </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <textarea name="workAuthor_area" id="workAuthor_area" cols="41" rows="1" style="display: none;"></textarea>
                    </td>
                  </tr>

                </table>
              </div>
              <!-- 2015-05-06 msy end -->
            </div>
            <div class="twelve columns alpha row">
              <div class="six columns" style="margin-right: 15px;">
                <h3>Localiza????o</h3>
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('country', 'Pa??s:') }}
                    </td>
                    <td>
                      {{ Form::select('country', [ "Afeganist??o"=>"Afeganist??o", "??frica do Sul"=>"??frica do Sul", "Alb??nia"=>"Alb??nia", "Alemanha"=>"Alemanha", "Am??rica Samoa"=>"Am??rica Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Ar??bia Saudita"=>"Ar??bia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "B??lgica"=>"B??lgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bol??via"=>"Bol??via", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulg??ria"=>"Bulg??ria", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canad??"=>"Canad??", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Col??mbia"=>"Col??mbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados ??rabes"=>"Emirados ??rabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finl??ndia"=>"Finl??ndia", "Fran??a"=>"Fran??a", "Gab??o"=>"Gab??o", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Gr??cia"=>"Gr??cia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "??ndia"=>"??ndia", "Indon??sia"=>"Indon??sia", "Inglaterra"=>"Inglaterra", "Ir??"=>"Ir??", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Isl??ndia"=>"Isl??ndia", "Israel"=>"Israel", "It??lia"=>"It??lia", "Iugosl??via"=>"Iugosl??via", "Jamaica"=>"Jamaica", "Jap??o"=>"Jap??o", "Jersey"=>"Jersey", "Kirgizst??o"=>"Kirgizst??o", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "L??bano"=>"L??bano", "L??bia"=>"L??bia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Maurit??nia"=>"Maurit??nia", "Mauritius"=>"Mauritius", "M??xico"=>"M??xico", "Mo??ambique"=>"Mo??ambique", "M??naco"=>"M??naco", "Mong??lia"=>"Mong??lia", "Nam??bia"=>"Nam??bia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicar??gua"=>"Nicar??gua", "Nig??ria"=>"Nig??ria", "Noruega"=>"Noruega", "Nova Zel??ndia"=>"Nova Zel??ndia", "Om??"=>"Om??", "Panam??"=>"Panam??", "Paquist??o"=>"Paquist??o", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polin??sia Francesa"=>"Polin??sia Francesa", "Pol??nia"=>"Pol??nia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Qu??nia"=>"Qu??nia", "Rep??blica Dominicana"=>"Rep??blica Dominicana", "Rom??nia"=>"Rom??nia", "R??ssia"=>"R??ssia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa L??cia"=>"Santa L??cia", "S??o Vicente"=>"S??o Vicente", "Singapura"=>"Singapura", "S??ria"=>"S??ria", "Spiemich"=>"Spiemich", "Sud??o"=>"Sud??o", "Su??cia"=>"Su??cia", "Sui??a"=>"Sui??a", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tail??ndia"=>"Tail??ndia", "Taiwan"=>"Taiwan", "Tchecoslov??quia"=>"Tchecoslov??quia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Y??men"=>"Y??men", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"],"Brasil", array('style' => "width: 168px")) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('state', 'Estado:') }}
                    </td>
                    <td>
                      {{ Form::select('state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amap??", "BA"=>"Bahia", "CE"=>"Cear??", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goi??s", "MA"=>"Maranh??o", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Par??", "PB"=>"Para??ba", "PE"=>"Pernambuco", "PI"=>"Piau??", "PR"=>"Paran??", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rond??nia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"S??o Paulo", "TO"=>"Tocantins"], Input::get("state"), array('style' => "width: 168px") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('city', 'Cidade:') }}
                    </td>
                    <td>
                      {{ Form::text('city', Input::get("city"), array('style' => "width: 160px") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('district', 'Bairro:') }}
                    </td>
                    <td>
                      {{ Form::text('district', Input::get("district"), array('style' => "width: 160px") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::hidden('typeSearch', $typeSearch, array('id'  => 'typeSearch') ) }}
                      {{ Form::label('street', 'Endere??o:') }}
                    </td>
                    <td>
                      {{ Form::text('street', Input::get("street"), array('style' => "width: 160px") ) }}
                    </td>
                  </tr>
                </table>
              </div>
              <div class="four columns" style="margin-right: 15px; margin-left: 15px;">
                <h3>Licen??a das imagens</h3>
                <table class="form-table" width="80%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('allowCommercialUses', 'Com uso comercial:') }}
                    </td>
                    <td>
                      {{ Form::select('allowCommercialUses',
                        [""=>"Escolha", "YES"=>"Sim", "NO"=>"N??o"],
                        Input::get("allowCommercialUses") ) }}
                    </td>
                  </tr>
                  <tr>
                    <td>
                      {{ Form::label('allowModifications', 'Permitem altera????o:') }}
                    </td>
                    <td>
                      {{ Form::select('allowModifications',
                        [""=>"Escolha", "YES"=>"Sim", "NO"=>"N??o"],
                        Input::get("allowModifications") ) }}
                    </td>
                  </tr>
                </table>

                <h3>Acervos</h3>
                <table class="form-table" width="80%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                      {{ Form::label('institution', 'Institui????es:') }}&nbsp;
                    </td>
                    <td>
                      @if(!is_null($institutions))
                        {{ Form::select('institution_id', array(""=>"Escolha") + $institutions , Input::get('institution_id')) }}
                      @else
                        {{ Form::select('institution_id', [""=>"Escolha o acervo institutional"], "",array('class' => 'left')) }}
                      @endif
                    </td>
                  </tr>
                </table>

              </div>
            </div>
            <div class="six columns alpha row">
              <p>{{ Form::submit('BUSCAR', ['class'=>'btn']) }}</p>
            </div>
          </div>
          <div class="four columns omega row">
            <div class="twelve columns">
              <h3>Interpreta????o das imagens</h3>
              {{ Form::checkbox('binomial_check', 1, false) }}
              {{ Form::label('binomial_check', 'Utilizar caracter??sticas da imagem na pesquisa') }}
              <br><br>
              <div id="binomial_container" class="four columns omega row hidden" style="margin-left: 15px;">
                <p style="text-align: justify">
                  Ao indicar valores nos bin??mios abaixo,
                  voc?? far?? uma busca por imagens que possuem resultados semelhantes,
                  considerando um intervalo de 5 pontos acima
                  e abaixo do valor que voc?? selecionar.
                </p>
                <br>
                <?php $count = $binomials->count() - 1; ?>
                @foreach($binomials->reverse() as $binomial)
                  <?php $diff = $binomial->defaultValue ?>
                  <p>
                    <table border="0" width="230">
                      <tr>
                        <td width="110">
                            {{ $binomial->firstOption }}
                            (<output for="fader{{ $binomial->id }}"
                              id="leftBinomialValue{{ $binomial->id }}">
                              {{ 100 - $diff }}
                            </output>%)
                        </td>
                        <td align="right">
                            {{ $binomial->secondOption }}
                            (<output for="fader{{ $binomial->id }}"
                              id="rightBinomialValue{{ $binomial->id }}">
                              {{ $diff }}
                            </output>%)
                        </td>
                      </tr>
                    </table>
                    {{ Form::input('range', 'value-'.$binomial->id, $diff,
                      [ 'min' => '0',
                        'max' => '100',
                        'oninput' => 'outputUpdate(' . $binomial->id . ', value)',
                        'disabled' => true,
                        'class' => 'binomial_value' ])
                    }}
                  </p>
                  <?php $count-- ?>
                @endforeach
              </div>
            </div>
          </div>
        {{ Form::close() }}

      </div>
      {{ Form::hidden('pgVisited', $pageVisited, array('id'  => 'pgVisited') ) }}
      {{ Form::hidden('pageCurrent1', $page, array('id'  => 'pageCurrent1') ) }}
      {{ Form::hidden('urlType', "advance", array('id'  => 'urlType') ) }}

      @if (count($photos))
        <!--   PAINEL DE IMAGENS - GALERIA - CARROSSEL   -->
        <!--<div class="wrap">
          <div id="panel">
            include('includes.panel')
          </div>
  		    <div class="panel-back"></div>
          <div class="panel-next"></div>
        </div> -->
      <!--   FIM - PAINEL DE IMAGENS  -->

      @include('includes.result-search')
    @endif
    </div>
    <!--   FIM - MEIO DO SITE   -->
    <script type="text/javascript">
      $(document).ready(function() {
        $('input[name="binomial_check"]').click(function(e) {
          if ( $(this).prop('checked') ) {
            $('#binomial_container').removeClass('hidden');
            $('.binomial_value').prop('disabled', false);
          } else {
            $('.binomial_value').prop('disabled', true);
            $('#binomial_container').addClass('hidden');
          }
        });

        @if ( isset($authorsArea) )
          @foreach ( $authorsArea as $author )
            $('#workAuthor_area').textext()[0].tags().addTags([ {{ '"' . $author . '"' }} ]);
          @endforeach
        @endif


        $('#tags').textext({ plugins: 'tags' });
        @if ( isset($tags) )
          @foreach ( $tags as $tag )
            $('#tags').textext()[0].tags().addTags([ {{ '"' . $tag . '"' }} ]);
          @endforeach
        @endif



      });


      $(function() {
        $( "#datePickerWorkDate" ).datepicker({
          dateFormat:'dd/mm/yy',
          keyboardNavigation: true,
          orientation: "bottom right"
        });
        $( "#datePickerdataCriacao" ).datepicker({
          dateFormat:'dd/mm/yy',
          keyboardNavigation: true,
          orientation: "bottom right"
        });
        $( "#datePickerdataUpload" ).datepicker({
          dateFormat:'dd/mm/yy',
          beforeShow: function(datePickerdataUpload) {
            $(datePickerdataUpload).css({
              "position":"relative",
              "z-index":999999
            });
          }
        });
      });

      function outputUpdate(binomio, val) {
        var left, right;
        left = document.querySelector('#leftBinomialValue'+binomio);
        right = document.querySelector('#rightBinomialValue'+binomio);
        left.value = 100 - val;
        right.value = val;
      }
    </script>
@stop
