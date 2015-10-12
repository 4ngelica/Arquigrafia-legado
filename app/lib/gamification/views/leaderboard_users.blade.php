
  @foreach($users->getCollection()->sortByDesc($score_type) as $user)
    @if ( $count % 2 == 0)
      <tr class="even">
    @else
      <tr>
    @endif
      <td><h2>{{ $count++ }}</h2></td>
      <td class="image">
        <a href="{{ URL::to('/users/' . $user->id) }}">
          @if ( ! empty($user->photo) )
            <img src="{{ asset($user->photo) }}"
              class="user_photo_thumbnail">
          @else
            <img src="{{ asset("img/avatar-60.png") }}"
              class="user_photo_thumbnail">
          @endif
        </a>
      </td>
      <td class="name">
        <p>{{ link_to('/users/' . $user->id, $user->name) }}</p>
      </td>
      <td><p class="score">{{ $user->$score_type }}</p></td>
    </tr>
  @endforeach
