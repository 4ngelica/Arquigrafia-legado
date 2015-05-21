@extends('layouts.default')

@section('head')

<title>Arquigrafia - Seu universo de imagens de arquitetura</title>

<!-- ISOTOPE -->
<script src="{{ URL::to("/") }}/js/jquery.isotope.min.js"></script>

<script type="text/javascript" src="{{ URL::to("/") }}/js/panel.js"></script>
<!--Pickers -->
<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

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
            <p>{{ Form::label('description', 'Descrição da imagem:') }} {{ Form::text('description', Input::get("description") ) }}</p>
            <p>{{ Form::label('imageAuthor', 'Autor da imagem:') }} {{ Form::text('imageAuthor', Input::get("imageAuthor") ) }}</p>
          </div>
          
          <div class="four columns row">
            <h3>Localização</h3>
            <p>{{ Form::label('city', 'Cidade:') }} {{ Form::text('city', Input::get("city") ) }}</p>
            <p>{{ Form::label('state', 'Estado:') }} {{ Form::text('state', Input::get("state") ) }}</p>
            <p>{{ Form::label('country', 'País:') }} {{ Form::text('country', Input::get("country") ) }}</p>
          </div>
          <!-- 2015-05-06 msy begin, workAuthor -->
          <div class="four columns omega row">
            <h3>Arquitetura</h3>
            <p>{{ Form::label('workAuthor', 'Arquiteto:') }} {{ Form::text('workAuthor', Input::get("workAuthor") ) }}</p>

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
//msy
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
      beforeShow: function(input, inst)
        {

        inst.dpDiv.css({marginTop: -65-input.offsetHeight + 'px', marginLeft: input.offsetWidth + 'px'});
        }
    });
    

    });
</script> 


@stop