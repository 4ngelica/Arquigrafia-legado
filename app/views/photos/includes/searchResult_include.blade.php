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
			<!-- <input type="checkbox" class="{{'ch_photo'}}" id="{{ 'photo_' . $photo->id }}"
				name="{{ 'photos_' . $type . '[]' }}" value="{{ $photo->id }}"> -->
			@if ($count % 6 < 3)
				<?php $position = 'right'; ?>
			@else
				<?php $position = 'left'; ?>
			@endif
			
			<a class="hovertext" href='{{ URL::to("/photos/{$photo->id}") }}' class="gallery_photo2" title="{{ $photo->name }}">
			<img 
			class="gallery_photo2" class="cls_image_search"  src="{{ URL::to('/arquigrafia-images/' . $photo->id . '_home.jpg') }}"
				class="img_photo {{ $position }}" data-id="{{ $photo->id }}"
				 >
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
