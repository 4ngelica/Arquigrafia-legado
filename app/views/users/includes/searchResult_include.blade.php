<?php $count = 0;
	  $type = 'add';
?>

<table class="page form-table" width="100%" border="0"
	cellspacing="0" cellpadding="0">
  @foreach($users as $user)
		@if ($count % 8 == 0)
		<tr>
		@endif
		<td width="110">
      <div style="width: 100%; text-align: center;">
        <a href='{{ URL::to("/users/{$user->id}") }}'>
          @if ( !empty($user->photo) )
            <img  src="{{ asset($user->photo) }}"
              style="width: 100px; height: 100px;"/>
          @else
            <img src="{{ URL::to('/img/avatar-60.png') }}"
              style="width: 100px; height: 100px;"/>
          @endif
        </a>
      </div>
      <div style="width: 100%; text-align: center;">
        <span>{{ $user-> name }}</span>
      </div>
    </td>
  	@if ($count % 8 == 7)
  	</tr>
  	@endif
	<?php $count++ ?>
  @endforeach
  @if($count % 8 != 0)
  	@while($count % 8 != 0)
  		<td></td>
  		<?php $count++; ?>
  	@endwhile
  	</tr>
  @endif
</table>
