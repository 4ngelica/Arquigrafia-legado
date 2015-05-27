<img src="{{ URL::to('/arquigrafia-images/' . $photo->id . '_view.jpg') }}" width="200">
<p>Nome: {{ $photo->name }}</p>
@if(isset($photo->dataUpload))
	<p>Upload: {{ Photo::translate($photo->dataUpload) }}</p>
@endif
