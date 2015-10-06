<!doctype html>
<html>
<head>
  <meta charset="utf8">
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.old.css" />
 <!-- <script type="text/javascript" src="{{ URL::to('/js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ URL::to('/js/inputmask.min.js') }}"></script>
  <script type="text/javascript" src="{{ URL::to('/js/jquery.inputmask.min.js') }}"></script>
  <script type="text/javascript" src="{{ URL::to('/js/photo.js') }}"></script>
  <script src="{{ URL::to('/js/jquery.tooltipster.min.js') }}"></script>-->
</head>
<body>
  <!-- <div class="container">-->
    <?php $date_field = 'dataWork' ?>
    <!--<div class="twelve columns"> -->
    <div class="eight columns">
    <div class="six columns" style="padding: 0 0 0 -3px;">
    Indique apenas o século e década que tenha certeza.
    Nenhum campo é obrigatório.<br>  
    <span style="font-size:9px">O Arquigrafia preza pela qualidade da informação e preferimos que o campo fique sem a data a conter informações incorretas.</span>          
      <p>
        <!--<div class="four columns">
        <label for="{{ $date_field }}">
         Indique apenas o século e década que tenha certeza.
          Nenhum campo é obrigatório.<br>
         <span style="font-size:9px">O Arquigrafia preza pela qualidade da informação e preferimos que o campo fique sem a data a conter informações incorretas.</span>          
        </label>
       </div> -->
        <!--<img src="{{ URL::to('/img/Help-14.png') }}" class="date_help"/>-->
      </p>
    </div>
  </div>
    @include('photos.includes.dateWork_include')
     
    
 <!-- </div> -->
  <style>
    div.container {
      margin-top: 5px;
    }

  </style>
</body>
</html>