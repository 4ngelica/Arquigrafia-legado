@foreach ($suggestions as $suggestion)
  <tr id="suggestion_{{ $suggestion['suggestion']->id }}" class="suggestion">
    <!-- Image -->
    <td>
      <a href="/photos/{{ $suggestion['suggestion']->photo->id }}" target="_blank">
        <img class="suggestion_photo" src="/arquigrafia-images/{{ $suggestion['suggestion']->photo->id }}_home.jpg" />
      </a>
    </td>
    <!-- Photo Name -->
    <td>{{ $suggestion['suggestion']->photo->name }}</td>
    <!-- Field -->
    <td>{{ $suggestion['field_name'] }}</td>
    <!-- Current Data -->
    @if ($suggestion['suggestion']->photo[$suggestion['field']])
      <td class="table-suggestion-collumn">{{ $suggestion['suggestion']->photo[$suggestion['field']] }}</td>
    @else
      <td>-----</td>
    @endif
    <!-- Suggested data -->
    @if ($suggestion['suggestion']->text)
      <td class="table-suggestion-collumn">{{ $suggestion['suggestion']->text }}</td>
    @else
      <td>-----</td>
    @endif
    <td>
      <div class="new-message">
        <a class="create-chat-link" data-val="{{$suggestion['suggestion']->user['id']}}" href="#">
          {{ $suggestion['suggestion']->user['name'] }}
        </a>
      </div>
    </td>
    <td>
      <div class="suggestion-button thumbs-up">
        <!-- Form for THUMBS UP -->
        <form id="send-thumbs-up-form" method="post">
          <input name="suggestion_id" value="{{ $suggestion['suggestion']->id }}" type="hidden"/>
          <input name="operation" value="accepted" type="hidden"/>
          <a class="thumbs-link" href="javascript:{}">Aceitar</a>
        </form>
      </div>
      <div class="suggestion-button thumbs-down">
        <!-- Form for THUMBS DOWN -->
        <form id="send-thumbs-down-form" method="post">
          <input name="suggestion_id" value="{{ $suggestion['suggestion']->id }}" type="hidden"/>
          <input name="operation" value="rejected" type="hidden"/>
          <a class="thumbs-link" href="javascript:{}">Rejeitar</a>
        </form>
      </div>
    </td>
  </tr>
@endforeach
