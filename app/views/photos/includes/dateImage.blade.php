<!doctype html>
<html>
<head>
  <meta charset="utf8">
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.old.css" />
</head>
<body>
  <div style="padding-bottom:20px">
    <div class="text_area_data">
    Indique apenas o século e década que tenha certeza.
    Nenhum campo é obrigatório.<br>  
    <span style="font-size:10px">O Arquigrafia preza pela qualidade da informação e preferimos que o campo fique sem a data a conter informações incorretas.</span>          
    </div>  
  
      <?php $date_field = '_image'; ?>
   
      @include('photos.includes.dateWork_include')
  </div>   
  <style>
    div.container {
      margin-top: 5px;
    }

  </style>
</body>
</html>