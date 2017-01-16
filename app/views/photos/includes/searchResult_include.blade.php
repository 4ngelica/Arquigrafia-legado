<?php $count = 0; 
	  $type = 'add';
?>

<table id="{{ $type . '_page' . $page }}" class="page form-table" width="100%" border="0"
	cellspacing="0" cellpadding="0">	
	@foreach($photos as $photo)
		@if ($count % 6 == 0)
		<tr>
		@endif
		<td width="143" class="{{ $type }}">
			<div style=" width: 155px; height: 110px;position: relative;" >
			@if ($photo->type == "video")
			  <div id="iconeVideo" style="position: absolute; 
			  background: url( '{{asset('img/icone-big.png')}}' ) center center no-repeat; z-index: 1; 
			  background-size: contain; left: 50%; top: 50%; width: 80%; height: 80%; max-width: 113px; 
			  max-height: 113px; transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); 
			  -ms-transform: translate(-50%, -50%);"></div>
			@endif
			<!-- <input type="checkbox" class="{{'ch_photo'}}" id="{{ 'photo_' . $photo->id }}"
				name="{{ 'photos_' . $type . '[]' }}" value="{{ $photo->id }}"> -->
			@if ($count % 6 < 3)
				<?php $position = 'right'; ?>
			@else
				<?php $position = 'left'; ?>
			@endif
			<!-- <p>{{-- $page --}}</p> -->
			<input type="hidden" id="pageCur" value="{{$page}}">
			<a class="hovertext" href='{{ URL::to("/photos/{$photo->id}") }}' class="gallery_photo2" title="{{ $photo->name }}">
			@if ($photo->type == "video")
			<img 
				class="gallery_photo2" class="cls_image_search"  src="{{ $photo->nome_arquivo }}"
				class="img_photo {{ $position }}" data-id="{{ $photo->id }}">
			@else
				<img 
				class="gallery_photo2" class="cls_image_search"  src="{{ URL::to('/arquigrafia-images/' . $photo->id . '_home.jpg') }}"
					class="img_photo {{ $position }}" data-id="{{ $photo->id }}"
					 >
			@endif
			</a>	

		    
		</div>
	</td>
	@if ($count % 6 == 5)
		</tr>
	@endif
	<?php $count++ ?>
@endforeach
	@if($count % 6 != 0)
		@while($count % 6 != 0)
			<td></td>
			<?php $count++; ?>
		@endwhile
		</tr>
	@endif
</table>
