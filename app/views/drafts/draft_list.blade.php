@foreach ($drafts as $draft)
  <tr id="draft_{{ $draft->id }}" class="draft">
    <td>{{ $draft->tombo }}</td>
    <td>{{ $draft->support }}</td>
    <td>{{ $draft->name }}</td>
    <td><a href="{{ URL::to('/drafts/' . $draft->id) }}">completar</a></td>
    <td><a href="#" data-draft="{{ $draft->id }}" class="delete_draft">excluir</a></td>
  </tr>
@endforeach