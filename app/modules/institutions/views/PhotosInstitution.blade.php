@extends('layouts.default')

@section('head')
  <title>Arquigrafia - Seu universo de imagens de arquitetura</title>
  <script type="text/javascript" src="{{ URL::to("/") }}/js/panel.js"></script>
  <script src="{{ URL::to('/js/searchPagination.js') }}"></script> 
  <link rel="stylesheet" type="text/css" href="{{ URL::to('/css/tabs.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/album.css" />
  <link rel="stylesheet" type="text/css" href="{{ URL::to("/") }}/css/checkbox-edition.css" />
  <script src="{{ URL::to("/") }}/js/jquery.isotope.min.js"></script>
  <link rel="stylesheet" type="text/css" media="screen"
    href="{{ URL::to("/") }}/css/checkbox.css" />

<?php
$page = 1;
$maxPage = 100;
$url = null;
?>

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
    console.log({{$page}});
    var coverPage = 1;    
    var covers_counter = 0;    
    var update = null;
    
    
    
  </script>
@stop

@section('content')
  <div id="content">

  </div>

 
    <?php 
	use modules\institutions\controllers\InstitutionsController;
	
	class PhotosInstitution {
	

      public function search() {
		$photos = Photo::all();

        $photos = DB::table('photos')->paginate(15);
        return view('institution.photo', ['photos' => $photos]);
		$photosPages = Photo::paginatePhotosSearch($photos); 
        $photosTotal = $photosPages->getTotal();
        $maxPage = $photosPages->getLastPage(); 
	}
}
  
	?>



@stop
