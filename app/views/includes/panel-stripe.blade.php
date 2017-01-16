<div id="panel" class="stripe">

	@foreach($photos as $photo)
		<div class="item h2">
			@if ($photo->type == "video")
			  <div id="iconeVideo" style="position: absolute; 
			  background: url( '{{asset('img/icone-big.png')}}' ) center center no-repeat; z-index: 1; 
			  background-size: contain; left: 50%; top: 50%; width: 80%; height: 80%; max-width: 113px; 
			  max-height: 113px; transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); 
			  -ms-transform: translate(-50%, -50%);"></div>
			@endif
			<div class="layer" data-depth="0.2">
				<a href='{{ URL::to("/photos/{$photo->id}") }}'>
					<?php
							if($photo->type == 'video'){
						        $micropath = $photo->nome_arquivo;
						        $path = $photo->nome_arquivo;
						    }else{
						          $micropath = '/arquigrafia-images/'. $photo->id . '_micro.jpg';
						          $path = '/arquigrafia-images/'. $photo->id . '_home.jpg'; 
						    }
				    ?>
					<img src="{{ asset( $micropath ) }}" data-src="{{ asset( $path ) }}" title="{{ $photo->name }}">
				</a>
				<div class="item-title">
					<p>{{ $photo->name }}</p>
					@if (Auth::check() && !Session::has('institutionId'))
						<a id="title_plus_button" class="title_plus" href="{{ URL::to('/albums/get/list/' . $photo->id)}}" title="Adicionar aos meus álbuns"></a>
					@endif
					
					@if (Auth::check() && ((Auth::id() == $photo->user_id && !isset($photo->institution_id) && !Session::has('institutionId')) ||
					 ( Session::has('institutionId') && Session::get('institutionId') == $photo->institution_id) ) )
							@if ( isset($album) )
								<a id="title_delete_button" class="title_delete photo" href="{{ URL::to('/albums/' . $album->id . '/photos/' . $photo->id . '/remove') }}" title="Excluir imagem"></a>
							@else
								<a id="title_delete_button" class="title_delete photo" href="{{ URL::to('/photos/' . $photo->id) }}" title="Excluir imagem"></a>
							@endif
					@endif
					
					@if (Auth::check() && $photo->institution_id !="" && Session::get('institutionId') == $photo->institution_id)
					<a id="title_edit_button" href="{{ URL::to('/institutions/' . $photo->id . '/form/edit')}}" title="Editar imagem"></a>
					@elseif (Auth::check() && Auth::id() == $photo->user_id &&  !Session::has('institutionId') && !isset($photo->institution_id)  )
					<a id="title_edit_button" href="{{ URL::to('/photos/' . $photo->id . '/edit')}}" title="Editar imagem"></a>
					@endif
				</div>
			</div>

		</div>

	@endforeach

</div>
<div class="panel-back"></div>
<div class="panel-next"></div>