@extends('layouts.default')

@section('head')

<title>Arquigrafia - Fotos - Update</title>

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
<script type="text/javascript" src="{{ URL::to("/") }}/js/date-work.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/rotate.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/readURL.js" charset="utf-8"></script>

<link rel="stylesheet" href="{{ URL::to("/") }}/css/jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="{{ URL::to("/") }}/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />

@stop

@section('content')
<style>
  /* Style select*/

    fieldset {
      border: 0;
      margin: 0 0 0px -10px;
      font-size: 10px;
    }
    label {
      display: block;
      margin: 30px 0 0 0;
    }
    /*select {
      width: 150px;
    }*/
    .overflow {
      height: 350px;
    }



</style>

  <div class="container">

	<div id="registration">
      {{ Form::open(array('url'=>'photos/' . $photo->id, 'method' => 'put', 'files'=> true)) }}

      <div class="twelve columns row step-1">
      	<h1><span class="step-text">Edi????o de informa????es de {{$photo->name}}</span></h1>
        <div class="eleven columns alpha" id="media_type">

                  <br>
                  <div class="form-row">
                    <input type="radio" name="type" value="photo" id="type_photo" checked="checked"
                      {{$photo->type == 'photo' ? "checked" : ""}}>
                      {{$photo->type == NULL ? "checked" : ""}}
                    <label for="type_photo">Foto</label><br class="clear">
                  </div>
                  <div class="form-row">
                    <input type="radio" name="type" value="video" id="type_video" {{$photo->type == 'video' ? "checked" : ""}}>
                    <label for="type_video">V??deo</label><br class="clear" >
                  </div>
                </div>
        <div id="divPhoto" class="four columns alpha">
            <a class="fancybox" href="{{ URL::to("/arquigrafia-images")."/".$photo->id."_view.jpg" }}" >
            <img id="old_image" class="single_view_image" style="" src="{{ URL::to("/arquigrafia-images")."/".$photo->id."_view.jpg" }}" />
            </a>
          <div id="old_image_rotate">
            <br></br>
            <a class="btn left" onclick="Rotate(document.getElementById('old_image'), -Math.PI/2);">Girar 90?? para esquerda</a>
            <a class="btn right" onclick="Rotate(document.getElementById('old_image'), Math.PI/2);">Girar 90?? para direita</a>
          </div>
          <br></br>
          <img src="" id="preview_photo">
          <div id="image_rotate" style="display:none;">
            <br></br>
            <a class="btn left" onclick="Rotate(document.getElementById('preview_photo'), -Math.PI/2);">Girar 90?? para esquerda</a>
            <a class="btn right" onclick="Rotate(document.getElementById('preview_photo'), Math.PI/2);">Girar 90?? para direita</a>
          </div>

          <p>
            {{ Form::label('photo','Alterar imagem:') }}
            {{ Form::file('photo', array('id'=>'imageUpload', 'onchange' => 'readURL(this);')) }}
            <br></br>
            <div class="error">{{ $errors->first('photo') }}</div>
          </p>
        </div>

        <div id="divVideo" class="twelve columns alpha">
        </br>
        <div class="two columns alpha">{{ Form::label('video', 'Link do v??deo youtube ou vimeo:') }}</div>
        <div class="four columns alpha">
            <p>{{ Form::text('video', $video, array('id' => 'video','style'=>'width:280px')) }} <br>
             </p>
             <div class="error">{{ $errors->first('video') }}</div>
             <p>Ex. https://www.youtube.com/watch?v=XXXXXXXX ou <br>
                ou  https://vimeo.com/XXXXXXXX</p>
        </div>
    </div>


      </div>


      <div id="registration" class="twelve columns row step-2">


          <h4>Campos obrigat??rios (*)</h4>

          <div class="six columns alpha row">
        	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>

			  <div class="two columns alpha"><p>{{ Form::label('photo_name', 'T??tulo*:') }}</p></div>
				<div class="three columns">
        <p>{{ Form::text('photo_name', $photo->name) }} <br>
				<div class="error">{{ $errors->first('photo_name') }}</div>
        </p>
			  </div>
            </tr>
            <tr>

			  <div class="two columns alpha"><p>{{ Form::label('photo_imageAuthor', 'Autor(es) da imagem/video*:') }}</p></div>
				<div class="three columns">
        <p>{{ Form::text('photo_imageAuthor', $photo->imageAuthor) }} <br>
				  <div class="error">{{ $errors->first('photo_imageAuthor') }}</div>
        </p>
        <p>Separe os autores diferentes com ";"</p>
			  </div>
            </tr>
            <tr>

        <div class="two columns alpha"><p>{{ Form::label('photo_country', 'Pa??s*:') }}</p></div>
        <div class="two columns omega">

        <p>{{ Form::select('photo_country', [ "Afeganist??o"=>"Afeganist??o", "??frica do Sul"=>"??frica do Sul", "Alb??nia"=>"Alb??nia", "Alemanha"=>"Alemanha", "Am??rica Samoa"=>"Am??rica Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Ar??bia Saudita"=>"Ar??bia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "B??lgica"=>"B??lgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bol??via"=>"Bol??via", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulg??ria"=>"Bulg??ria", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canad??"=>"Canad??", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Col??mbia"=>"Col??mbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados ??rabes"=>"Emirados ??rabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finl??ndia"=>"Finl??ndia", "Fran??a"=>"Fran??a", "Gab??o"=>"Gab??o", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Gr??cia"=>"Gr??cia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "??ndia"=>"??ndia", "Indon??sia"=>"Indon??sia", "Inglaterra"=>"Inglaterra", "Ir??"=>"Ir??", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Isl??ndia"=>"Isl??ndia", "Israel"=>"Israel", "It??lia"=>"It??lia", "Iugosl??via"=>"Iugosl??via", "Jamaica"=>"Jamaica", "Jap??o"=>"Jap??o", "Jersey"=>"Jersey", "Kirgizst??o"=>"Kirgizst??o", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "L??bano"=>"L??bano", "L??bia"=>"L??bia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Maurit??nia"=>"Maurit??nia", "Mauritius"=>"Mauritius", "M??xico"=>"M??xico", "Mo??ambique"=>"Mo??ambique", "M??naco"=>"M??naco", "Mong??lia"=>"Mong??lia", "Nam??bia"=>"Nam??bia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicar??gua"=>"Nicar??gua", "Nig??ria"=>"Nig??ria", "Noruega"=>"Noruega", "Nova Zel??ndia"=>"Nova Zel??ndia", "Om??"=>"Om??", "Panam??"=>"Panam??", "Paquist??o"=>"Paquist??o", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polin??sia Francesa"=>"Polin??sia Francesa", "Pol??nia"=>"Pol??nia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Qu??nia"=>"Qu??nia", "Rep??blica Dominicana"=>"Rep??blica Dominicana", "Rom??nia"=>"Rom??nia", "R??ssia"=>"R??ssia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa L??cia"=>"Santa L??cia", "S??o Vicente"=>"S??o Vicente", "Singapura"=>"Singapura", "S??ria"=>"S??ria", "Spiemich"=>"Spiemich", "Sud??o"=>"Sud??o", "Su??cia"=>"Su??cia", "Sui??a"=>"Sui??a", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tail??ndia"=>"Tail??ndia", "Taiwan"=>"Taiwan", "Tchecoslov??quia"=>"Tchecoslov??quia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Y??men"=>"Y??men", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"], $photo->country != null ? $photo->country : "Brasil") }}<br>
          <div class="error">{{ $errors->first('photo_country') }} </div>
        </p>

        </div>
              </tr>
            <tr>

              <td>

                  <div class="two columns alpha"><p>{{ Form::label('tags_input', 'Tags*:') }}</p></div>
                  <div class="four columns omega">
                    <p>
                      {{ Form::text('tags_input') }}
                      <button class="btn" id="add_tag" style="font-size: 11px;">ADICIONAR TAG</button>
                      <br>
                      <div class="error">{{ $errors->first('tags') }}</div>
                    </p>
                  </div>
                  <div class="five columns alpha">
                    <textarea name="tags" id="tags" cols="40" rows="1" style="display: none;"></textarea>
                  </div>


              </td>
            </tr>
            <tr>

            </tr>
          </table>
          </div>

          <br class="clear">
          <h4>Campos complementares</h4>

          <div class="five columns alpha row">
          	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">

              <tr><td>

				<div class="two columns alpha"><p>{{ Form::label('photo_state', 'Estado:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::select('photo_state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amap??", "BA"=>"Bahia", "CE"=>"Cear??", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goi??s", "MA"=>"Maranh??o", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Par??", "PB"=>"Para??ba", "PE"=>"Pernambuco", "PI"=>"Piau??", "PR"=>"Paran??", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rond??nia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"S??o Paulo", "TO"=>"Tocantins"], $photo->state) }} <br>
				  <div class="error">{{ $errors->first('photo_state') }}</div>
        </p></td>
              </tr>
              <tr><td>

				<div class="two columns alpha"><p>{{ Form::label('photo_city', 'Cidade:') }}</p></div>
				<div class="two columns omega">
        <p>{{ Form::text('photo_city', $photo->city) }}<br>
				  <div class="error">{{ $errors->first('photo_city') }}</div>
        </p>
			  </div>
				</td>
              </tr>
              <tr><td>

				<div class="two columns alpha"><p>{{ Form::label('photo_district', 'Bairro:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::text('photo_district', $photo->district) }} <br>
				</p>
			  </div>
				</td>
              </tr>
              <tr><td>
				<div class="two columns alpha"><p>{{ Form::label('photo_street', 'Endere??o:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::text('photo_street', $photo->street) }} <br>
				</p>
			  </div></td>
              </tr>
              <tr> <td>
        <div class="two columns alpha"><p>{{ Form::label('photo_description', 'Descri????o:') }}</p></div>
        <div class="two columns omega">
        <p>{{ Form::textarea('photo_description', $photo->description,['size' => '26x8']) }} <br>
        </p>
        </div></td>
              </tr>

              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>

            </table>

          </div>

          <div class="seven columns omega row">
          	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr><td>
         <div class="oneUpload columns alpha"><p>{{ Form::label('photo_imageDate', 'Data da imagem:') }}</p></div>
         <div class="fivemidUpdateForm columns omega">
         @if (($photo->dataCriacao)!= null && $photo->imageDateType == "date")
          <p>{{ Form::text('photo_imageDate',date("d/m/Y",strtotime($photo->dataCriacao)),array('id' => 'datePickerImageDate','placeholder'=>'dd/mm/yyyy')) }}
         @else
          <p>{{ Form::text('photo_imageDate','',array('id' => 'datePickerImageDate','placeholder'=>'dd/mm/yyyy')) }}
         @endif
          <span class="space_txt_element">N??o sabe a data precisa?
                  <a onclick="date_visibility('date_img_inaccurate');" >Clique aqui.</a>
          </span>
          <br> <div id="error_image_date" class="error">{{ $errors->first('photo_imageDate') }}</div>
         </p>
         <p>
             <div id="date_img_inaccurate" style="display:none;">
                        @include('photos.includes.dateImage')
             </div>
             <label id="answer_date_image" class="resultDateWork"></label>
         </p>
        </div></td>
        </tr>

       <!-- <div class="oneUpload columns alpha"><p>{{ Form::label('photo_workAuthor', 'Autor da obra:') }}</p></div>-->

       <tr><td>Autor da obra: @include('photos.includes.workAuthor') </td></tr>

        <tr><td>
         <div class="oneUpload columns alpha"><p>{{ Form::label('photo_workDate', 'Ano de conclus??o da obra:') }}</p></div>
         <div class="fivemidUpdateForm columns omega">
         <p>
                   @include('photos.includes.dateList')
                    <span class="space_txt_element">N??o sabe a data precisa?
                      <a  onclick="date_visibility('otherDate');" >Clique aqui.</a> </span>

         </p>
         <p><div id="otherDate" style="display:none;">
                        @include('photos.includes.dateWork')
            </div>
            <label id="answer_date" class="resultDateWork"></label>
         </p>
        </div>
        </td>
        </tr>

            </table>
          </div>

          <div class="twelve columns omega row">
            <h4>Licen??a</h4>
            <p>
               Sou o autor da imagem ou possuo permiss??o expressa do autor para disponibiliz??-la no Arquigrafia.
               <br>

               <br>
               Escolho a licen??a <a href="http://creativecommons.org/licenses/?lang=pt_BR" id="creative_commons" target="_blank" style="text-decoration:underline; line-height:16px;">Creative Commons</a>, para publicar a imagem, com as seguintes permiss??es:
            </p>
					</div>

          <div class="four columns" id="creative_commons_left_form">
            Permitir o uso comercial da imagem?

            <br>
             <div class="form-row">
              <input type="radio" name="photo_allowCommercialUses" value="YES" id="photo_allowCommercialUsesYES" {{$photo->allowCommercialUses == 'YES' ? "checked" : ""}}>
              <label for="photo_allowCommercialUsesYES">Sim</label><br class="clear">
             </div>
             <div class="form-row">
              <input type="radio" name="photo_allowCommercialUses" value="NO" id="photo_allowCommercialUsesNO" {{$photo->allowCommercialUses == 'NO' ? "checked" : ""}}>
              <label for="photo_allowCommercialUsesNO">N??o</label><br class="clear">
             </div>

          </div>
          <div class="four columns" id="creative_commons_right_form">
            Permitir modifica????es em sua imagem?
            <br>
            <div class="form-row">
              <input type="radio" name="photo_allowModifications" value="YES" id="photo_allowModificationsYES" {{$photo->allowModifications == 'YES' ? "checked" : ""}}>
              <label for="photo_allowModificationsYES">Sim</label><br class="clear">
            </div>
           	<div class="form-row">
              <input type="radio" name="photo_allowModifications" value="YES_SA" id="photo_allowModificationsYES_SA" {{$photo->allowModifications == 'YES_SA' ? "checked" : ""}}>
              <label for="photo_allowModificationsYES_SA">Sim, contanto que os outros compartilhem de forma semelhante</label><br class="clear">
             </div>
           	<div class="form-row">
              <input type="radio" name="photo_allowModifications" value="NO" id="photo_allowModificationsNO" {{$photo->allowModifications == 'NO' ? "checked" : ""}}>
              <label for="photo_allowModificationsNO">N??o</label><br class="clear">
            </div>

          </div>

          <div class="twelve columns">
            <input name="enviar" type="submit" class="btn" value="ENVIAR">
            <a href="{{ URL::to('/photos/' . $photo->id) }}" class='btn'>VOLTAR</a>&nbsp;&nbsp;
          </div>

      </div>

      {{ Form::close() }}

	</div>

  </div>
  <script type="text/javascript">
    $(document).ready(function() {
      var typeSaved  = "{{ $type }}";
      var typeChecked  = "{{Input::old('type')}}";

      if(typeChecked == null || typeChecked == ""){
          if(typeSaved == "photo" ){
              document.getElementById('type_photo').checked = true;
              document.getElementById('video').value = "";
              $('#divVideo').hide();
              $('#divPhoto').show();
          }else{

              var elem = document.getElementById('old_image');
              elem.parentNode.removeChild(elem);
              document.getElementById('type_video').checked = true;
                  $('#divVideo').show();
                  $('#divPhoto').hide();
          }
      }else{
        //checkado

        if(typeChecked == "video" ){
                if(typeSaved == "video" ){
                    var elem = document.getElementById('old_image');
                    elem.parentNode.removeChild(elem);
                }
                document.getElementById('type_video').checked = true;
                $('#divVideo').show();
                $('#divPhoto').hide();
        }else{
            document.getElementById('type_photo').checked = true;
            document.getElementById('video').value = null;
            $('#divVideo').hide();
            $('#divPhoto').show();

        }
      }


      $('input[type=radio][name=type]').change(function(){
        if(this.value == "video"){
          $('#divVideo').show();
          $('#divPhoto').hide();
        } if(this.value == "photo") {
          $('#divVideo').hide();
          $('#divPhoto').show();
        }
    });
     $('#tags').textext({ plugins: 'tags' });

      @foreach($tags as $tag)
        $('#tags').textext()[0].tags().addTags([ {{ '"' . $tag . '"' }} ]);
      @endforeach

      $('#add_tag').click(function(e) {
        e.preventDefault();
        var tag = $('#tags_input').val();
        if (tag == '') return;
        $('#tags').textext()[0].tags().addTags([ tag ]);
        $('#tags_input').val('');
      });
      $('#tags_input').keypress(function(e) {
        var key = e.which || e.keyCode;
        if (key == 44 || key == 46 || key == 59) // key = , ou Key = . ou key = ;
          e.preventDefault();
      });

        $('#work_authors').textext({ plugins: 'tags' });

        @if(Input::old('work_authors')!= null)
            <?php $work_authors = explode ('","', Input::old('work_authors')); ?>
        @endif
        var string_author = "";
        @if (isset($work_authors) && $work_authors != null && !empty($work_authors))
            @foreach ( $work_authors as $work_author )
                string_author = '{{$work_author}}';
                string_author = string_author.replace('["',"");
                string_author = string_author.replace('"]',"");
                $('#work_authors').textext()[0].tags().addTags([ string_author ]);
            @endforeach
        @endif

        $('#add_work_authors').click(function(e) {
            e.preventDefault();
            authorsList();
        });
        $('#photo_workAuthor').keypress(function(e) {
            var key = e.which || e.keyCode;
            //alert("A" +key)
            if(key ==13)
               authorsList();

            if (key == 44 || key == 46 || key == 59) // key = , ou Key = . ou key = ;
                e.preventDefault();
        });
        function authorsList(){
            var authors = $('#photo_workAuthor').val();
            if (authors == '') return;
            $('#work_authors').textext()[0].tags().addTags([ authors ]);
            $('#photo_workAuthor').val('');
        }

    })

    @if($centuryImageInput != null || $centuryImageInput != "" )    //
      var centuryImageInput = "{{$centuryImageInput}}";//"{{Input::old('century')}}";
      showPeriodCenturyImage(centuryImageInput);
      retrieveCenturyImage(centuryImageInput);
      //get filter
      //filterDecadesOfCentury(centuryInput);
      //alert(centuryImageInput);
    @endif

    @if($decadeImageInput != null || $decadeImageInput!="" )
        var decadeImageInput = "{{$decadeImageInput}}";
        retrieveDecadeImage(decadeImageInput);
        getCenturyOfDecade(decadeImageInput,"imageDate");
       // alert("fueraForm"+decadeImageInput);
    @endif



    @if($dateYear != NULL)
      var dateYear = "{{$dateYear}}";
      retrieveYearDate(dateYear);
    @endif

    @if($centuryInput != null || $centuryInput != "" )    //
      var centuryInput = "{{$centuryInput}}";//"{{Input::old('century')}}";
      showPeriodCentury(centuryInput);
      retrieveCentury(centuryInput);
      //get filter
      //filterDecadesOfCentury(centuryInput);
      //alert(centuryInput);
    @endif

    @if($decadeInput != null || $decadeInput!="" )
        var decadeInput = "{{$decadeInput}}";
      retrieveDecade(decadeInput);
      getCenturyOfDecade(decadeInput,"workDate");
    @endif

    //window.onload = resultSelectDateWork;

     if("{{ $centuryImageInput }}" != "" || "{{ $decadeImageInput }}" != "" ){
                window.onload = resultSelectDateWork("date_img_inaccurate");
               // alert("iamge carga");
      }

     if("{{Input::old('century_image')}}" != "" || "{{Input::old('decade_select_image')}}" != "" ){
                window.onload = resultSelectDateWork("date_img_inaccurate");
                //alert("image por error o recuperac");
      }


     if("{{ $centuryInput }}" != "" || "{{ $decadeInput}} " != "" ){
                window.onload = resultSelectDateWork("otherDate");
              //  alert("other Decade-load");
        }

     if("{{Input::old('century')}}" != "" || "{{Input::old('decade_select')}}" != "" ){
                window.onload = resultSelectDateWork("otherDate");
              //  alert("other Decade-old");
        }

    $(function() {
    $( "#datePickerImageDate" ).datepicker({
      dateFormat:'dd/mm/yy'
    }
      );
    });

  </script>
@stop
