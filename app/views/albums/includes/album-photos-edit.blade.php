<?php $count = 0; ?>
<table id="{{ $type . '_page' . $page }}" class="page form-table" width="100%" border="0"
	cellspacing="0" cellpadding="0">
@foreach($photos as $photo)
	@if ($count % 6 == 0)
		<tr>
	@endif
	<td width="143" class="{{ $type }}">
		<div class="photo {{ $type }}">
			<input type="checkbox" class="{{'ch_photo'}}" id="{{ 'photo_' . $photo->id }}"
			name="{{ 'photos_' . $type . '[]' }}" value="{{ $photo->id }}">
			@if ($count % 6 < 3)
				<img src="{{ URL::to('/arquigrafia-images/' . $photo->id . '_home.jpg') }}"
					class="img_photo right-side" data-id="{{ $photo->id }}"
					title="{{ $photo->name }}">
			@else
				<img src="{{ URL::to('/arquigrafia-images/' . $photo->id . '_home.jpg') }}"
					class="img_photo left-side" data-id="{{ $photo->id }}"
					title="{{ $photo->name }}">
			@endif
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
