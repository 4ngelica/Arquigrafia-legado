<!doctype html>
<html>
<head>
  <meta charset="utf8">
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.old.css" />
 </head>
<body>
   <?php $type_field = 'fourUploadInst';
   		 $first_column = 'two';
   		 $text_area_field = 'sevenUploadInst';
   		 $size_area_author = 'author_area_size_int';
   ?>
   @include('photos.includes.workAuthor_include')  
  
  <style>
    div.container {
      margin-top: 5px;
    }

  </style>
</body>
</html>