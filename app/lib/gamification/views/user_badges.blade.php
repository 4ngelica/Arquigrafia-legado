<div class="container row">
  <div class="twelve columns">
    <hgroup class="profile_block_title">
      <h3><i class="badges"></i>
        @if( $user->equal(Auth::user()) )
          Meus troféus
        @else
          Troféus
        @endif
      </h3>
    </hgroup>
    <div class="profile_box">
      @if ( !$user->badges->count() )
        <p>Não há nenhuma troféu ainda.</p>
      @else
        @foreach($user->badges as $b)
          <div class="one column">
            <a href="{{ URL::to('/badges/' . $b->id) }}">
              <img src="{{ asset('/img/badges/' . $b->image) }}" height="60" width="60"
                title="{{ $b->name }}">
            </a>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</div>