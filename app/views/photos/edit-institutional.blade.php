@extends('layouts.default')

@section('head')

<title>Arquigrafia - Fotos - Update</title>


<!--<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.plugin.tags.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/styletags.css" />
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.core.css" />



<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.core.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.tags.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.autocomplete.js" charset="utf-8"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.plugin.ajax.js" charset="utf-8"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/tags-autocomplete.js" charset="utf-8"></script>
<!--<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tag-list.js" charset="utf-8"></script>
<script type="text/javascript" src="{{ URL::to("/") }}/js/tag-autocomplete-part.js" charset="utf-8"></script>

<style>
  .ui-autocomplete {
    max-height: 100px;
    font-size: 12px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 100px;
  }
  </style>

@stop

@section('content')

  <div class="container">
  
	<div id="registration">      
      {{ Form::open(array('url'=>'photos/'.$photo->id.'/update/Institutional', 'method' => 'put', 'files'=> true)) }}           
  
      <div class="twelve columns row step-1">
      	<h1><span class="step-text">Edição de informações da imagem {{$photo->name}}</span></h1>
        
        <div class="four columns alpha">
          

          <p><a class="fancybox" href="{{ URL::to("/arquigrafia-images")."/".$photo->id."_view.jpg" }}" >
            <img class="single_view_image" style="" src="{{ URL::to("/arquigrafia-images")."/".$photo->id."_view.jpg" }}" />
            </a>
          </p>
          <br>

           <p>{{ Form::label('photo','Alterar imagem:') }} 
          {{ Form::file('photo', array('id'=>'imageUpload', 'onchange' => 'readURL(this);')) }}
              <div class="error">{{ $errors->first('photo') }}</div>
          </p>
           <br>
           <img src="" id="preview_photo">
           <br>
        </div>   

      </div> 

      
      <div id="registration" class="twelve columns row step-2">         
      	          
          
          <h4>Campos obrigatórios (*)</h4>
          <br class="clear">
         <!-- <h4>Campos complementares</h4>-->
          
 
          
          <div class="eight columns alpha row">
          	<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('support', 'Suporte*:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>{{ Form::text('support', $photo->support) }} <br>
                      <div class="error">{{ $errors->first('support') }}</div>
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('tomboTxt', 'Tombo*:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>{{ Form::text('tombo', $photo->tombo ) }} <br>
                      <div class="error">{{ $errors->first('tombo') }}</div>
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('subjectTxt', 'Assunto*:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>{{ Form::text('subject', $photo->subject) }} <br>
                      <div class="error">{{ $errors->first('subject') }}</div>
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('hygieneDateTxt', 'Data de higienização:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>
                    @if (($photo->hygieneDate)!= null )                    
                      {{ Form::text('hygieneDate',date("d/m/Y",strtotime($photo->hygieneDate)),array('id' => 'datePickerHygieneDate','placeholder'=>'DD/MM/AAAA')) }} 
                    @else
                      {{ Form::text('hygieneDate','',array('id' => 'datePickerHygieneDate','placeholder'=>'DD/MM/AAAA')) }} 
                    @endif  
                      <br>
                      <div class="error">{{ $errors->first('hygieneDate') }}</div>
                    </p> 

                  </div>
                </td>
              </tr>
                            
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('backupDateTxt', 'Data de backup:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>
                      @if (($photo->backupDate)!= null )
                      {{ Form::text('backupDate',date("d/m/Y",strtotime($photo->backupDate)),array('id' => 'datePickerBackupDate','placeholder'=>'DD/MM/AAAA')) }} 
                      @else
                      {{ Form::text('backupDate','',array('id' => 'datePickerBackupDate','placeholder'=>'DD/MM/AAAA')) }}
                      @endif
                      <br>
                      <div class="error">{{ $errors->first('backupDate') }}</div>
                    </p>
                  </div>
                </td>
              </tr>

              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('characterizationTxt', 'Caracterização*:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>{{ Form::text('characterization',$photo->characterization ) }} <br>
                      <div class="error">{{ $errors->first('characterization') }}</div>
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('userResponsibleTxt', 'Usuário Responsável:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>{{ Form::text('userResponsible', $user->name,['readonly']) }} <br>
                      <div class="error">{{ $errors->first('userResponsible') }}</div>
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('name', 'Título*:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>{{ Form::text('name', $photo->name) }} <br>
                      <div class="error">{{ $errors->first('name') }}</div>
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha">
                    <p>{{ Form::label('description', 'Descrição:') }}</p>
                  </div>
                  <div class="three columns omega">
                    <p>
                     
                      {{ Form::textarea('description', $photo->description) }}<br>
                     
                    </p>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="two columns alpha"><p>{{ Form::label('tags_input', 'Tags*:') }}</p></div>
                  <div class="three columns">
                    <p><div style="max-width:180px;">
                      {{ Form::text('tags_input',null,array('id' => 'tags_input','style'=>'width: 200px; height:15px; border:solid 1px #ccc')) }}
                       </div>
                      
                      <br>
                      <div class="error">{{ $errors->first('tagsArea') }}</div>
                    </p>
                  </div>
                  <div>
                    <button class="btn" id="add_tag" style="font-size: 11px;">ADICIONAR TAG</button>
                  </div>
                  <div class="five columns alpha">

                    <textarea name="tagsArea" id="tagsArea" cols="79" rows="2" style="display: none;">
                    </textarea>
                  </div>                  
                </td>
              </tr>

                
              
              <tr>
                <td>
                  <br/><br/>
                  <div class="two columns alpha"><p>{{ Form::label('workAuthor', 'Autor da obra:') }}</p></div>
                  <div class="ui-widget two columns">
                    <p>

                      {{ Form::text('workAuthor', $workAuthorInput, array('id' => 'workAuthor', 'placeholder' => 'SOBRENOME, nome','style'=>'height:15px; width:290px; font-size:11px; border:solid 1px #ccc')) }}
                        
                                             
                      <br>
                      <div class="error">{{ $errors->first('workAuthor') }}</div>
                    </p>
                  </div>               
                </td>
              </tr>
              <tr> <td>              
                <div class="two columns alpha"><p>{{ Form::label('workDate', 'Data da obra:') }}</p></div>
                 <div class="two columns omega">
                 @if (($photo->workdate)!= null )
                  <p>{{ Form::text('workDate',date("d/m/Y",strtotime($photo->workdate)),array('id' => 'datePickerWorkDate','placeholder'=>'dd/mm/yyyy')) }}
                 @else
                  <p>{{ Form::text('workDate','',array('id' => 'datePickerWorkDate','placeholder'=>'dd/mm/yyyy')) }} 
                 @endif  
                  <br> <div class="error">{{ $errors->first('workDate') }}</div>
                    </p>   
                </div>
              </td>
            </tr>
            </table>
          </div>
          <br class="clear">
          <div class="five columns alpha row">
            <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <div class="two columns alpha"><p>{{ Form::label('country', 'País*:') }}</p></div>
                <div class="two columns omega">
                    <p>{{ Form::select('country', [ "Afeganistão"=>"Afeganistão", "África do Sul"=>"África do Sul", "Albânia"=>"Albânia", "Alemanha"=>"Alemanha", "América Samoa"=>"América Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Arábia Saudita"=>"Arábia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "Bélgica"=>"Bélgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bolívia"=>"Bolívia", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulgária"=>"Bulgária", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canadá"=>"Canadá", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Colômbia"=>"Colômbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados Árabes"=>"Emirados Árabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finlândia"=>"Finlândia", "França"=>"França", "Gabão"=>"Gabão", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Grécia"=>"Grécia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "Índia"=>"Índia", "Indonésia"=>"Indonésia", "Inglaterra"=>"Inglaterra", "Irã"=>"Irã", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Islândia"=>"Islândia", "Israel"=>"Israel", "Itália"=>"Itália", "Iugoslávia"=>"Iugoslávia", "Jamaica"=>"Jamaica", "Japão"=>"Japão", "Jersey"=>"Jersey", "Kirgizstão"=>"Kirgizstão", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "Líbano"=>"Líbano", "Líbia"=>"Líbia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Mauritânia"=>"Mauritânia", "Mauritius"=>"Mauritius", "México"=>"México", "Moçambique"=>"Moçambique", "Mônaco"=>"Mônaco", "Mongólia"=>"Mongólia", "Namíbia"=>"Namíbia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicarágua"=>"Nicarágua", "Nigéria"=>"Nigéria", "Noruega"=>"Noruega", "Nova Zelândia"=>"Nova Zelândia", "Omã"=>"Omã", "Panamá"=>"Panamá", "Paquistão"=>"Paquistão", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polinésia Francesa"=>"Polinésia Francesa", "Polônia"=>"Polônia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Quênia"=>"Quênia", "República Dominicana"=>"República Dominicana", "Romênia"=>"Romênia", "Rússia"=>"Rússia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa Lúcia"=>"Santa Lúcia", "São Vicente"=>"São Vicente", "Singapura"=>"Singapura", "Síria"=>"Síria", "Spiemich"=>"Spiemich", "Sudão"=>"Sudão", "Suécia"=>"Suécia", "Suiça"=>"Suiça", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tailândia"=>"Tailândia", "Taiwan"=>"Taiwan", "Tchecoslováquia"=>"Tchecoslováquia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Yémen"=>"Yémen", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"], $photo->country != null ? $photo->country : "Brasil") }}<br>
                        <div class="error">{{ $errors->first('country') }} </div>
                    </p>
                </div>
              </tr>
              <tr>
                <div class="two columns alpha"><p>{{ Form::label('state', 'Estado:') }}</p></div>
                <div class="two columns omega">
                <p>{{ Form::select('state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá", "BA"=>"Bahia", "CE"=>"Ceará", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goiás", "MA"=>"Maranhão", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Pará", "PB"=>"Paraíba", "PE"=>"Pernambuco", "PI"=>"Piauí", "PR"=>"Paraná", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rondônia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"São Paulo", "TO"=>"Tocantins"], $photo->state) }} <br>
                  <div class="error">{{ $errors->first('state') }}</div>
                </p>
              </tr>
              <tr>
                <div class="two columns alpha"><p>{{ Form::label('city', 'Cidade:') }}</p></div>
                <div class="two columns omega">
                  <p>{{ Form::text('city', $photo->city) }}<br>                    
                  </p>
                </div>
              </tr>
              
              <tr>
                <div class="two columns alpha"><p>{{ Form::label('street', 'Endereço:') }}</p></div>
                <div class="two columns omega">
                  <p>{{ Form::text('street', $photo->street) }} <br>
                  </p>
                </div>
              </tr>

              <tr>
                
                  <div class="two columns alpha"><p>{{ Form::label('imageAuthor', 'Autor da imagem*:') }}</p></div>
                  <div class="two columns omega">
                    <p>
                      {{ Form::text('imageAuthor', $institution->name) }} 
                       <br>
                      <div class="error">{{ $errors->first('imageAuthor') }}</div>
                    </p>
                  </div>
              </tr>
              <tr>                
                    <div class="two columns alpha"><p>{{ Form::label('imageDate', 'Data da imagem:') }}</p></div>
                    <div class="two columns omega">
                      @if (($photo->dataCriacao)!= null )
                         <p>{{ Form::text('imageDate',date("d/m/Y",strtotime($photo->dataCriacao)),array('id' => 'datePickerImageDate','placeholder'=>'DD/MM/AAAA')) }} 
                      @else
                         <p>{{ Form::text('imageDate','',array('id' => 'datePickerImageDate','placeholder'=>'DD/MM/AAAA')) }} 
                      @endif    
                      <br> <div class="error">{{ $errors->first('imageDate') }}</div>
                      </p>       
                      </div>
             </tr>
              <tr>
                <div class="two columns alpha"><p>{{ Form::label('observation', 'Observações:') }}</p></div>
                <div class="two columns omega">
                  <p>
                    {{ Form::textarea('observation',$photo->observation) }} <br>
                  </p>
                </div>
              </tr>

              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
          </div>  
           <div class="twelve columns omega row">
            <h4>Licença</h4>
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
              <input type="radio" name="allowCommercialUses" value="YES" id="allowCommercialUses" {{$photo->allowCommercialUses == 'YES' ? "checked" : ""}}>
              <label for="allowCommercialUses">Sim</label><br class="clear">
             </div>
             <div class="form-row">
              <input type="radio" name="allowCommercialUses" value="NO" id="allowCommercialUses" {{$photo->allowCommercialUses == 'NO' ? "checked" : ""}}>
              <label for="allowCommercialUses">Não</label><br class="clear">
             </div>
          </div>
          <div class="four columns" id="creative_commons_right_form">
            Permitir modificações em sua imagem?
            <br>
            <div class="form-row">
              <input type="radio" name="allowModifications" value="YES" id="allowModifications" {{$photo->allowModifications == 'YES' ? "checked" : ""}}>
              <label for="question_3-5">Sim</label><br class="clear">
            </div>
            <div class="form-row">
              <input type="radio" name="allowModifications" value="YES_SA" id="allowModifications" {{$photo->allowModifications == 'YES_SA' ? "checked" : ""}}>
              <label for="question_3-5">Sim, contanto que os outros compartilhem de forma semelhante</label><br class="clear">
             </div>
            <div class="form-row">
              <input type="radio" name="allowModifications" value="NO" id="allowModifications" {{$photo->allowModifications == 'NO' ? "checked" : ""}}>
              <label for="question_3-5">Não</label><br class="clear">
            </div>
          </div>
          <div class="twelve columns">
            <input name="enviar" type="submit" class="btn" value="ENVIAR">
            <a href="{{ URL::to('/photos/' . $photo->id) }}" class='btn'>VOLTAR</a>&nbsp;&nbsp;
            <!--<input type="button" id="btnOpenDialogRepopulate" value="ENVIAR" class="btn">-->
            <div id="dialog-confirm" title=" "></div>
          </div>        
      </div>
      
      {{ Form::close() }}
	  
	</div>

  </div>
  <script type="text/javascript">
  $(document).ready(function() {
    /* Methods to be called when all html document be ready */
    showTags({{json_encode($tagsArea)}},$('#tagsArea'),$('#tags_input'));
    
   });
  </script>
@stop