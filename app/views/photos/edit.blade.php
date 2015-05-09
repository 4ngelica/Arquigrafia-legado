@extends('layouts.default')

@section('head')

<title>Arquigrafia - Fotos - Update</title>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
@stop

@section('content')

  <div class="container">
  
	<div id="registration">      
      {{ Form::open(array('url'=>'photos/' . $photo->id, 'method' => 'put', 'files'=> true)) }}           
      
      <div class="twelve columns row step-1">
      	<h1><span class="step-text">Edição de informações da imagem {{$photo->name}}</span></h1>
        
        <div class="four columns alpha">
          

          <p><a class="fancybox" href="{{ URL::to("/arquigrafia-images")."/".$photo->id."_view.jpg" }}" >
            <img class="single_view_image" style="" src="{{ URL::to("/arquigrafia-images")."/".$photo->id."_view.jpg" }}" />
            </a>
          </p>
          <br>

           <p>{{ Form::label('photo','Alterar imagem:') }} 
          {{ Form::file('photo', array('id'=>'imageUpload', 'onchange' => 'readURL(this);')) }}</p>
           <br>
           <img src="" id="preview_photo">
           <br>
        </div>  

        <script type="text/javascript">
          function readURL(input) {
            $("#preview_photo").hide();
            if (input.files && input.files[0]) {
              var reader = new FileReader();
              reader.onload = function (e) {
                $('#preview_photo')
                  .attr('src', e.target.result)
                  .width(600);
                  $("#preview_photo").show();
              };
              reader.readAsDataURL(input.files[0]);
            }
          }
       </script>      
           

      </div> 

      
      <div id="registration" class="twelve columns row step-2">         
      	          
          <p>(*) Campos obrigatórios.</p>
          <br />
          
          <div class="six columns alpha row">
        	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              
			  <div class="two columns alpha omega"><p>{{ Form::label('photo_name', 'Título*:') }}</p></div>
				<div class="three columns alpha">				
        <p>{{ Form::text('photo_name', $photo->name) }} <br>
				<div class="error">{{ $errors->first('photo_name') }}</div>
        </p>
			  </div>			   
            </tr>
            <tr>
             
			  <div class="two columns alpha omega"><p>{{ Form::label('photo_imageAuthor', 'Autor da imagem*:') }}</p></div>
				<div class="three columns alpha">				
        <p>{{ Form::text('photo_imageAuthor', $photo->imageAuthor) }} <br>
				  <div class="error">{{ $errors->first('photo_imageAuthor') }}</div>
        </p>
			  </div>
            </tr>
            <tr>
              
              <td>
              	
                  <div class="two columns alpha omega"><p>{{ Form::label('tags_input', 'Tags*:') }}</p></div>
                  <div class="two columns alpha">
                    <p>
                      {{ Form::text('tags_input') }} <br>
                      <div class="error">{{ $errors->first('tags') }}</div>
                    </p>
                  </div>
                  <div class="two columns alpha">
                    <button class="btn alpha" id="add_tag">Adicionar tag</button>
                  </div>
                  <div class="five columns alpha">
                    <textarea name="tags" id="tags" cols="60" rows="1" style="display: none;"></textarea>
                  </div>

               
              </td>
            </tr>
          </table>
          </div>
          
          <br class="clear">
          
          <div class="five columns alpha row">
          	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>

				<div class="two columns alpha"><p>{{ Form::label('photo_country', 'País*:') }}</p></div>
				<div class="two columns omega">
				
				<p>{{ Form::select('photo_country', [ "Afeganistão"=>"Afeganistão", "África do Sul"=>"África do Sul", "Albânia"=>"Albânia", "Alemanha"=>"Alemanha", "América Samoa"=>"América Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Arábia Saudita"=>"Arábia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "Bélgica"=>"Bélgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bolívia"=>"Bolívia", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulgária"=>"Bulgária", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canadá"=>"Canadá", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Colômbia"=>"Colômbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados Árabes"=>"Emirados Árabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finlândia"=>"Finlândia", "França"=>"França", "Gabão"=>"Gabão", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Grécia"=>"Grécia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "Índia"=>"Índia", "Indonésia"=>"Indonésia", "Inglaterra"=>"Inglaterra", "Irã"=>"Irã", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Islândia"=>"Islândia", "Israel"=>"Israel", "Itália"=>"Itália", "Iugoslávia"=>"Iugoslávia", "Jamaica"=>"Jamaica", "Japão"=>"Japão", "Jersey"=>"Jersey", "Kirgizstão"=>"Kirgizstão", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "Líbano"=>"Líbano", "Líbia"=>"Líbia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Mauritânia"=>"Mauritânia", "Mauritius"=>"Mauritius", "México"=>"México", "Moçambique"=>"Moçambique", "Mônaco"=>"Mônaco", "Mongólia"=>"Mongólia", "Namíbia"=>"Namíbia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicarágua"=>"Nicarágua", "Nigéria"=>"Nigéria", "Noruega"=>"Noruega", "Nova Zelândia"=>"Nova Zelândia", "Omã"=>"Omã", "Panamá"=>"Panamá", "Paquistão"=>"Paquistão", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polinésia Francesa"=>"Polinésia Francesa", "Polônia"=>"Polônia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Quênia"=>"Quênia", "República Dominicana"=>"República Dominicana", "Romênia"=>"Romênia", "Rússia"=>"Rússia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa Lúcia"=>"Santa Lúcia", "São Vicente"=>"São Vicente", "Singapura"=>"Singapura", "Síria"=>"Síria", "Spiemich"=>"Spiemich", "Sudão"=>"Sudão", "Suécia"=>"Suécia", "Suiça"=>"Suiça", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tailândia"=>"Tailândia", "Taiwan"=>"Taiwan", "Tchecoslováquia"=>"Tchecoslováquia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Yémen"=>"Yémen", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"], $photo->country != null ? $photo->country : "Brasil") }}<br>
				  <div class="error">{{ $errors->first('photo_country') }} </div>
        </p>
				
			  </div>
              </tr>
              <tr>

				<div class="two columns alpha"><p>{{ Form::label('photo_state', 'Estado*:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::select('photo_state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá", "BA"=>"Bahia", "CE"=>"Ceará", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goiás", "MA"=>"Maranhão", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Pará", "PB"=>"Paraíba", "PE"=>"Pernambuco", "PI"=>"Piauí", "PR"=>"Paraná", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rondônia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"São Paulo", "TO"=>"Tocantins"], $photo->state) }} <br>
				  <div class="error">{{ $errors->first('photo_state') }}</div>
        </p>
              </tr>
              <tr>
                
				<div class="two columns alpha"><p>{{ Form::label('photo_city', 'Cidade*:') }}</p></div>
				<div class="two columns omega">				
        <p>{{ Form::text('photo_city', $photo->city) }}<br>
				  <div class="error">{{ $errors->first('photo_city') }}</div>
        </p>
			  </div>
				
              </tr>
              <tr>
               
				<div class="two columns alpha"><p>{{ Form::label('photo_district', 'Bairro:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::text('photo_district', $photo->district) }} <br>
				</p>
			  </div>
				
				
              </tr>
              <tr>
                
				<div class="two columns alpha"><p>{{ Form::label('photo_street', 'Endereço:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::text('photo_street', $photo->street) }} <br>
				</p>
			  </div>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>

            </table>
          	
          </div>
          
          <div class="five columns omega row">
          	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
         
        <!-- <tr>       
				<div class="two columns alpha"><p>{{ Form::label('photo_imageDate', 'Data da imagem:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::text('photo_imageDate', $photo->dataCriacao) }} <br>
				</p>
			  </div>
        </tr>-->

        <tr>                
         <div class="two columns alpha"><p>{{ Form::label('photo_imageDate', 'Data da imagem:') }}</p></div>
         <div class="two columns omega">
         @if (($photo->dataCriacao)!= null )
          <p>{{ Form::text('photo_imageDate',date("d/m/Y",strtotime($photo->dataCriacao)),array('id' => 'datePickerImageDate','placeholder'=>'dd/mm/yyyy')) }} 
         @else
          <p>{{ Form::text('photo_imageDate','',array('id' => 'datePickerImageDate','placeholder'=>'dd/mm/yyyy')) }} 
         @endif  
         <br> <div class="error">{{ $errors->first('photo_imageDate') }}</div>
         </p>       
        </div>
        </tr>


              <tr>
                
				<div class="two columns alpha"><p>{{ Form::label('photo_workAuthor', 'Autor da obra:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::text('photo_workAuthor', $photo->workAuthor) }} <br>
				</p>
			  </div>
        </tr>	

        <tr>                
         <div class="two columns alpha"><p>{{ Form::label('photo_workDate', 'Data da obra:') }}</p></div>
         <div class="two columns omega">
         @if (($photo->workdate)!= null )
          <p>{{ Form::text('photo_workDate',date("d/m/Y",strtotime($photo->workdate)),array('id' => 'datePickerWorkDate','placeholder'=>'dd/mm/yyyy')) }} 
         @else
          <p>{{ Form::text('photo_workDate','',array('id' => 'datePickerWorkDate','placeholder'=>'dd/mm/yyyy')) }} 
         @endif  
         <br> <div class="error">{{ $errors->first('photo_workDate') }}</div>
         </p>       
        </div>
        </tr>
        
        <tr>        
				<div class="two columns alpha"><p>{{ Form::label('photo_description', 'Descrição:') }}</p></div>
				<div class="two columns omega">
				<p>{{ Form::textarea('photo_description', $photo->description) }} <br>
				</p>
			  </div>
              </tr>
            </table>
          </div>
        	
          <div class="twelve columns omega row">
            <p> 
               Sou o autor da imagem ou possuo permissão expressa do autor para disponibilizá-la no Arquigrafia. 
               <br>
               
               <br>
               Escolho a licença <a href="http://creativecommons.org/licenses/?lang=pt_BR" id="creative_commons" target="_blank" style="text-decoration:underline; line-height:16px;">Creative Commons</a>, para publicar a imagem, com as seguintes permissões:
            </p>          
					</div>
           
          <div class="four columns" id="creative_commons_left_form">
            Permitir o uso comercial da imagem?

            <br>
             <div class="form-row">
              <input type="radio" name="photo_allowCommercialUses" value="YES" id="photo_allowCommercialUses" {{$photo->allowCommercialUses == 'YES' ? "checked" : ""}}>
              <label for="photo_allowCommercialUses">Sim</label><br class="clear">
             </div>
             <div class="form-row">
              <input type="radio" name="photo_allowCommercialUses" value="NO" id="photo_allowCommercialUses" {{$photo->allowCommercialUses == 'NO' ? "checked" : ""}}>
              <label for="photo_allowCommercialUses">Não</label><br class="clear">
             </div>
            
          </div>
          <div class="four columns" id="creative_commons_right_form">
            Permitir modificações em sua imagem?
            <br>
            <div class="form-row">
              <input type="radio" name="photo_allowModifications" value="YES" id="photo_allowModifications" {{$photo->allowModifications == 'YES' ? "checked" : ""}}>
              <label for="question_3-5">Sim</label><br class="clear">
            </div>
           	<div class="form-row">
              <input type="radio" name="photo_allowModifications" value="YES_SA" id="photo_allowModifications" {{$photo->allowModifications == 'YES_SA' ? "checked" : ""}}>
              <label for="question_3-5">Sim, contanto que os outros compartilhem de forma semelhante</label><br class="clear">
             </div>
           	<div class="form-row">
              <input type="radio" name="photo_allowModifications" value="NO" id="photo_allowModifications" {{$photo->allowModifications == 'NO' ? "checked" : ""}}>
              <label for="question_3-5">Não</label><br class="clear">
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
        if (key == 44 || key == 59) // key = , ou key = ;
          e.preventDefault();
      });
    })
    
//msy
    $(function() {
    $( "#datePickerWorkDate" ).datepicker({
      dateFormat:'dd/mm/yy'
    }
      );
    $( "#datePickerImageDate" ).datepicker({
      dateFormat:'dd/mm/yy'
    }
      );
    });

  </script>
@stop