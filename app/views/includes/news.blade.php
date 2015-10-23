<?php 
  $i = rand(0,10);
  $news = Auth::user()->news; 
?>

@foreach($news as $info)

<?php 
  $i++;
  $size = 1; 
  if ($i%7 == 6) $size = 2;
  if ($i%21 == 6) $size = 3;  
?>
@if($info->news_type == 'new_photo')<!--Alguém que você segue inseriu uma foto-->
<div class="item h<?php echo $size; ?>">
	<div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/photos") . "/" . $info->object_id }}'>
		<?php 
		?>
		<img data-src={{"/arquigrafia-images/" . $info->object_id . "_home.jpg"}} title="{{ Photo::find($info->object_id)->name }}">
    </a>
    <div class="item-title">
      <p>{{User::find($info->sender_id)->name}} postou uma nova foto</p>
    </div>
	</div>
</div>
@elseif($info->news_type == 'commented_photo')<!--Alguém que você segue comentou uma foto-->
<div class="item h<?php echo $size; ?>">
  <div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/photos") . "/" . Comment::find($this->object_id)->photo_id . "#" . $info->object_id}}'>
    <?php 
    ?>
    <img data-src={{"/arquigrafia-images/" . Comment::find($this->object_id)->photo_id . "_home.jpg"}} title="{{ Photo::find(Comment::find($this->object_id)->photo_id)->name }}">
    </a>
    <div class="item-title">
      @if($info->data == null)
        <p>{{User::find($info->sender_id)->name}} comentou em uma foto</p>
      @else
      <?php 
        $users = explode(":", $info->data);
        $users_size = count($users) - 1;
      ?>
        <p>{{$users_size}} usuários comentaram em uma foto</p>
      @endif
    </div>
  </div>
</div>
@elseif($info->news_type == 'evaluated_photo')<!--Alguém que você segue avaliou uma foto-->
<div class="item h<?php echo $size; ?>">
  <div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/photos") . "/" . $info->object_id }}'>
    <?php 
    ?>
    <img data-src={{"/arquigrafia-images/" . $info->object_id . "_home.jpg"}} title="{{ Photo::find($info->object_id)->name }}">
    </a>
    <div class="item-title">
      @if($info->data == null)
        <p>{{User::find($info->sender_id)->name}} avaliou uma foto</p>
      @else
      <?php 
        $users = explode(":", $info->data);
        $users_size = count($users) - 1;
      ?>
        <p>{{$users_size}} usuários avaliaram uma foto</p>
      @endif
    </div>
  </div>
</div>
@elseif($info->news_type == 'new_profile_picture')<!--Alguém que você segue trocou a foto de perfil-->
<div class="item h<?php echo $size; ?>">
  <div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/users") . "/" . $info->object_id }}'>
    <?php 
    ?>
    @if(User::find($info->object_id)->photo != null)
      <img data-src={{"/arquigrafia-avatars/" . $info->object_id . ".jpg"}} title="{{ User::find($info->object_id)->name }}">
    @else
      <img data-src="{{ URL::to("/") }}/img/avatar-48.png" title="{{User::find($info->object_id)->name}}">
    @endif
    </a>
    <div class="item-title">
      <p>{{User::find($info->sender_id)->name}} trocou sua foto de perfil</p>
    </div>
  </div>
</div>
@elseif($info->news_type == 'edited_photo')<!--Alguém que você segue editou uma foto-->
<div class="item h<?php echo $size; ?>">
  <div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/photos") . "/" . $info->object_id }}'>
    <?php 
    ?>
    <img data-src={{"/arquigrafia-images/" . $info->object_id . "_home.jpg"}} title="{{ Photo::find($info->object_id)->name }}">
    </a>
    <div class="item-title">
      <p>{{User::find($info->sender_id)->name}} editou uma foto</p>
    </div>
  </div>
</div>
@elseif($info->news_type == 'edited_profile')<!--Alguém que você segue editou o perfil-->
<div class="item h<?php echo $size; ?>">
  <div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/users") . "/" . $info->object_id }}'>
    <?php 
    ?>
    @if(User::find($info->object_id)->photo != null)
      <img data-src={{"/arquigrafia-avatars/" . $info->object_id . ".jpg"}} title="{{ User::find($info->object_id)->name }}">
    @else
      <img data-src="{{ URL::to("/") }}/img/avatar-48.png" title="{{User::find($info->object_id)->name}}">
    @endif
    </a>
    <div class="item-title">
      <p>{{User::find($info->sender_id)->name}} editou seu perfil</p>
    </div>
  </div>
</div>
@elseif($info->news_type == 'highlight_of_the_week')<!--Destaque da semana-->

@elseif($info->news_type == 'liked_photo')<!--Alguém que você segue gostou de uma foto-->
<div class="item h<?php echo $size; ?>">
  <div class="layer" data-depth="0.2">
    <a href='{{ URL::to("/photos") . "/" . $info->object_id }}'>
    <?php 
    ?>
    <img data-src={{"/arquigrafia-images/" . $info->object_id . "_home.jpg"}} title="{{ Photo::find($info->object_id)->name }}">
    </a>
    <div class="item-title">
      @if($info->data == null)
        <p>{{User::find($info->sender_id)->name}} gostou de uma foto</p>
      @else
      <?php 
        $users = explode(":", $info->data);
        $users_size = count($users) - 1;
      ?>
        <p>{{$users_size}} usuários gostaram de uma foto</p>
      @endif
    </div>
  </div>
</div>
@endif


@endforeach
