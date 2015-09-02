@extends('layouts.default')

@section('head')

<title>Arquigrafia - Seu universo de imagens de arquitetura</title>

<!-- ISOTOPE -->
<script src="{{ URL::to("/") }}/js/jquery.isotope.min.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/panel.js"></script>
<!--Pickers -->
<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->

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

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
{{-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> --}}
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/textext.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/textext.css" />




@stop

@section('content')

    <!--   MEIO DO SITE - ÁREA DE NAVEGAÇÃO   -->
    <div id="content">
    	
      <div class="container">
        <div id="search_result" class="twelve columns row">
          
          <!--@if(true)
            
            <h1>Resultado da busca avançada</h1> -->           
            
            <?php // if ( count($photos) < 1 ) { ?>
              <!-- <p>Não encontramos nenhuma imagem.</p> -->
            <?php //} else { ?>
              <!-- <p>Foram encontradas {{ count($photos) }} imagens.</p> -->
            <?php //} ?>
            
          <!--@else
            
            <h1>Busca avançada</h1>
            
          @endif-->

          <h1>Busca avançada</h1>          
          <div class="twelve columns alpha">
          <p>Apenas os campos que forem preenchidos abaixo serão considerados na busca, para trazer as imagens que correspondam a todos os critérios informados.</p>
          </div>
          
        </div>
        
        {{ Form::open(array('url' => 'search/more', 'method' => 'get')) }}
        <div class="twelve columns row">
        
          <div class="four columns alpha row">
            <h3>Descrição</h3>
            <p>{{ Form::label('name', 'Título da imagem:') }} {{ Form::text('name', Input::get("name") ) }}</p>
            <p>{{ Form::label('description', 'Descrição da imagem:') }} 
             <!--< {{ Form::text('description', Input::get("description") ) }}-->
            <textarea name="description" id="description" cols="35" rows="3" >{{Input::get("description")}}</textarea>  
            </p>
              <!--<p>{{ Form::label('tag', 'Tag:') }} {{ Form::text('tag', Input::get("tag") ) }}</p>  -->

            <p>{{ Form::label('tags_input', 'Tags*:') }}
                {{ Form::text('tags_input') }}
                <button class="btn" id="add_tag">Adicionar tag</button>
                 <br>                          
                <p style="font-size: 7pt">Máximo 5 tags</p>
                <textarea name="tags" id="tags" cols="35" rows="1" style="display: none;"></textarea>
                <div class="error"></div>      
            </p>



          </div>

          <div class="four columns row">
            <h3>Localização</h3>
            <!-- <p>{{ Form::label('country', 'País:') }} {{ Form::text('country', Input::get("country") ) }}</p>-->

           <p>{{ Form::label('country', 'País:') }} {{ Form::select('country', [ "Afeganistão"=>"Afeganistão", "África do Sul"=>"África do Sul", "Albânia"=>"Albânia", "Alemanha"=>"Alemanha", "América Samoa"=>"América Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Anguilla"=>"Anguilla", "Antartida"=>"Antartida", "Antigua"=>"Antigua", "Antigua e Barbuda"=>"Antigua e Barbuda", "Arábia Saudita"=>"Arábia Saudita", "Argentina"=>"Argentina", "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Barbados"=>"Barbados", "Bélgica"=>"Bélgica", "Belize"=>"Belize", "Bermuda"=>"Bermuda", "Bhutan"=>"Bhutan", "Bolívia"=>"Bolívia", "Botswana"=>"Botswana", "Brasil"=>"Brasil", "Brunei"=>"Brunei", "Bulgária"=>"Bulgária", "Burundi"=>"Burundi", "Cabo Verde"=>"Cabo Verde", "Camboja"=>"Camboja", "Canadá"=>"Canadá", "Chade"=>"Chade", "Chile"=>"Chile", "China"=>"China", "Cingapura"=>"Cingapura", "Colômbia"=>"Colômbia", "Djibouti"=>"Djibouti", "Dominicana"=>"Dominicana", "Emirados Árabes"=>"Emirados Árabes", "Equador"=>"Equador", "Espanha"=>"Espanha", "Estados Unidos"=>"Estados Unidos", "Fiji"=>"Fiji", "Filipinas"=>"Filipinas", "Finlândia"=>"Finlândia", "França"=>"França", "Gabão"=>"Gabão", "Gaza Strip"=>"Gaza Strip", "Ghana"=>"Ghana", "Gibraltar"=>"Gibraltar", "Granada"=>"Granada", "Grécia"=>"Grécia", "Guadalupe"=>"Guadalupe", "Guam"=>"Guam", "Guatemala"=>"Guatemala", "Guernsey"=>"Guernsey", "Guiana"=>"Guiana", "Guiana Francesa"=>"Guiana Francesa", "Haiti"=>"Haiti", "Holanda"=>"Holanda", "Honduras"=>"Honduras", "Hong Kong"=>"Hong Kong", "Hungria"=>"Hungria", "Ilha Cocos (Keeling)"=>"Ilha Cocos (Keeling)", "Ilha Cook"=>"Ilha Cook", "Ilha Marshall"=>"Ilha Marshall", "Ilha Norfolk"=>"Ilha Norfolk", "Ilhas Turcas e Caicos"=>"Ilhas Turcas e Caicos", "Ilhas Virgens"=>"Ilhas Virgens", "Índia"=>"Índia", "Indonésia"=>"Indonésia", "Inglaterra"=>"Inglaterra", "Irã"=>"Irã", "Iraque"=>"Iraque", "Irlanda"=>"Irlanda", "Irlanda do Norte"=>"Irlanda do Norte", "Islândia"=>"Islândia", "Israel"=>"Israel", "Itália"=>"Itália", "Iugoslávia"=>"Iugoslávia", "Jamaica"=>"Jamaica", "Japão"=>"Japão", "Jersey"=>"Jersey", "Kirgizstão"=>"Kirgizstão", "Kiribati"=>"Kiribati", "Kittsnev"=>"Kittsnev", "Kuwait"=>"Kuwait", "Laos"=>"Laos", "Lesotho"=>"Lesotho", "Líbano"=>"Líbano", "Líbia"=>"Líbia", "Liechtenstein"=>"Liechtenstein", "Luxemburgo"=>"Luxemburgo", "Maldivas"=>"Maldivas", "Malta"=>"Malta", "Marrocos"=>"Marrocos", "Mauritânia"=>"Mauritânia", "Mauritius"=>"Mauritius", "México"=>"México", "Moçambique"=>"Moçambique", "Mônaco"=>"Mônaco", "Mongólia"=>"Mongólia", "Namíbia"=>"Namíbia", "Nepal"=>"Nepal", "Netherlands Antilles"=>"Netherlands Antilles", "Nicarágua"=>"Nicarágua", "Nigéria"=>"Nigéria", "Noruega"=>"Noruega", "Nova Zelândia"=>"Nova Zelândia", "Omã"=>"Omã", "Panamá"=>"Panamá", "Paquistão"=>"Paquistão", "Paraguai"=>"Paraguai", "Peru"=>"Peru", "Polinésia Francesa"=>"Polinésia Francesa", "Polônia"=>"Polônia", "Portugal"=>"Portugal", "Qatar"=>"Qatar", "Quênia"=>"Quênia", "República Dominicana"=>"República Dominicana", "Romênia"=>"Romênia", "Rússia"=>"Rússia", "Santa Helena"=>"Santa Helena", "Santa Kitts e Nevis"=>"Santa Kitts e Nevis", "Santa Lúcia"=>"Santa Lúcia", "São Vicente"=>"São Vicente", "Singapura"=>"Singapura", "Síria"=>"Síria", "Spiemich"=>"Spiemich", "Sudão"=>"Sudão", "Suécia"=>"Suécia", "Suiça"=>"Suiça", "Suriname"=>"Suriname", "Swaziland"=>"Swaziland", "Tailândia"=>"Tailândia", "Taiwan"=>"Taiwan", "Tchecoslováquia"=>"Tchecoslováquia", "Tonga"=>"Tonga", "Trinidad e Tobago"=>"Trinidad e Tobago", "Turksccai"=>"Turksccai", "Turquia"=>"Turquia", "Tuvalu"=>"Tuvalu", "Uruguai"=>"Uruguai", "Vanuatu"=>"Vanuatu", "Wallis e Fortuna"=>"Wallis e Fortuna", "West Bank"=>"West Bank", "Yémen"=>"Yémen", "Zaire"=>"Zaire", "Zimbabwe"=>"Zimbabwe"],"Brasil") }}</p>
           
           <p>{{ Form::label('state', 'Estado:') }} {{ Form::select('state', [""=>"Escolha o Estado", "AC"=>"Acre", "AL"=>"Alagoas", "AM"=>"Amazonas", "AP"=>"Amapá", "BA"=>"Bahia", "CE"=>"Ceará", "DF"=>"Distrito Federal", "ES"=>"Espirito Santo", "GO"=>"Goiás", "MA"=>"Maranhão", "MG"=>"Minas Gerais", "MS"=>"Mato Grosso do Sul", "MT"=>"Mato Grosso", "PA"=>"Pará", "PB"=>"Paraíba", "PE"=>"Pernambuco", "PI"=>"Piauí", "PR"=>"Paraná", "RJ"=>"Rio de Janeiro", "RN"=>"Rio Grande do Norte", "RO"=>"Rondônia", "RR"=>"Roraima", "RS"=>"Rio Grande do Sul", "SC"=>"Santa Catarina", "SE"=>"Sergipe", "SP"=>"São Paulo", "TO"=>"Tocantins"],Input::get("state") ) }} </p>

            <p>{{ Form::label('city', 'Cidade:') }} {{ Form::text('city', Input::get("city") ) }}</p>
            <p>{{ Form::label('district', 'Bairro:') }} {{ Form::text('district', Input::get("district") ) }}</p>
            <p>{{ Form::label('street', 'Endereço:') }} {{ Form::text('street', Input::get("street") ) }}</p>
          </div>
                    <!-- 2015-05-06 msy begin, workAuthor -->
          <div class="four columns omega row">
            <h3>Arquitetura</h3>
            <p>{{ Form::label('workAuthor', 'Autor da obra:') }} {{ Form::text('workAuthor', Input::get("workAuthor") ) }}</p>
            <p>{{ Form::label('imageAuthor', 'Autor da imagem:') }} {{ Form::text('imageAuthor', Input::get("imageAuthor") ) }}</p>
            <p>{{ Form::label('workdate', 'Data da obra:') }} 
              {{ Form::text('workdate',Input::get("workdate"),array('id' => 'datePickerWorkDate')) }} </p>
            <p>{{ Form::label('dataCriacao', 'Data da imagem:') }} 
              {{ Form::text('dataCriacao',Input::get("dataCriacao"),array('id' => 'datePickerdataCriacao')) }} </p>
            <p>{{ Form::label('dataUpload', 'Data de upload:') }} 
              {{ Form::text('dataUpload',Input::get("dataUpload"),array('id' => 'datePickerdataUpload')) }} </p>
            <!--<p>{{ Form::label('workdate', 'Data da obra:') }} {{ Form::text('workdate', Input::get("workdate") ) }}</p>-->
            <!--<p>{{ Form::label('dataCriacao', 'Data da imagem:') }} {{ Form::text('dataCriacao', Input::get("dataCriacao") ) }}</p>-->
            <!--<p>{{ Form::label('dataUpload', 'Data de upload:') }} {{ Form::text('dataUpload', Input::get("dataUpload") ) }}</p>-->
            
          </div>
          <div class="twelve columns row">
            <div class="four columns alpha row">
              <br><br>
               <h3>Licença das imagens</h3>
                  <p>{{ Form::label('allowCommercialUses', 'Com uso comercial:') }} 
                     {{ Form::select('allowCommercialUses', [""=>"Escolha", "YES"=>"Sim", 
                     "NO"=>"Não"],Input::get("allowCommercialUses") ) }}
                  </p>
                   <p>{{ Form::label('allowModifications', 'Permitem alteração:') }} 
                      {{ Form::select('allowModifications', [""=>"Escolha", "YES"=>"Sim", 
                       "NO"=>"Não"],Input::get("allowModifications") ) }}

            </p>
            
                     
            </div>
          </div>  
          
          
          <!-- 2015-05-06 msy end -->
          <div class="six columns alpha row">
            <p>{{ Form::submit('BUSCAR', ['class'=>'btn']) }}</p>
          </div>
        
        </div>
        {{ Form::close() }}

        @if(count($photos) < 1)
         <p>Não encontramos nenhuma imagem.</p> 
        @elseif(count($photos) == 1) 
          <p>Foi encontrada {{ count($photos) }} imagem.</p>
        @else  
          <p>Foram encontradas {{ count($photos) }} imagens.</p> 
        @endif
      </div>
      
      
      

      <!--   PAINEL DE IMAGENS - GALERIA - CARROSSEL   -->  
      <div class="wrap">
        <div id="panel">
            
          @include('includes.panel')
          
        </div>
		<div class="panel-back"></div>
        <div class="panel-next"></div>
      </div>
      <!--   FIM - PAINEL DE IMAGENS  -->
	  
	        
    </div>
    <!--   FIM - MEIO DO SITE   -->
<script type="text/javascript">
      $(document).ready(function() {
      $('#tags').textext({ plugins: 'tags' });

      @if (isset($tags) || $tags!=null) 
        @foreach ( $tags as $tag )
          $('#tags').textext()[0].tags().addTags([ {{ '"' . $tag . '"' }} ]);          
        @endforeach
      @endif
      
      //var h = document.getElementsById('text-label').value;

//h.value = 100;
//alert(h);

      //var clicks = 0;
      $('#add_tag').click(function(e) {
        e.preventDefault(); 
        
        var sizeTags = $('#tags').textext()[0].tags()._formData.length; 

        var tag = $('#tags_input').val();
        if (tag == '') return;
        if(sizeTags < 5){        
          $('#tags').textext()[0].tags().addTags([ tag ]); 
          $('#tags_input').val('');
        }else{
          $('#tags_input').val('');
        }
         
      });

      $('#tags_input').keypress(function(e) {
        var key = e.which || e.keyCode;
        if (key == 44 || key == 46 || key == 59){ // key = , ou Key = . ou key = ;
          e.preventDefault();
          // clicks += 1;
          // alert(clicks);
        }
      });
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
      beforeShow: function(datePickerdataUpload)
        {          
          $(datePickerdataUpload).css({
            "position":"relative",
            "z-index":999999
          } );
        
        }
    });
    

    });
</script> 


@stop