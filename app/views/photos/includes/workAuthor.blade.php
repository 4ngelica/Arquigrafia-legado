<!doctype html>
<html>
<head>
  <meta charset="utf8">
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/style.old.css" />
 </head>
<body>
   <?php $type_field = 'fourUpload';
   		 $first_column = 'oneUpload';
       $text_area_field = 'sevenUpload';
       $size_area_author = 'author_area_size';
   ?>
   @include('photos.includes.workAuthor_include')  
  
  <style>
    div.container {
      margin-top: 5px;
    }

  </style>
</body>
</html>