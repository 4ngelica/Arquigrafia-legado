@foreach ($suggestions as $suggestion)
  <tr id="suggestion_{{ $suggestion['suggestion']->id }}" class="suggestion">
    <td>{{ $suggestion['suggestion']->photo->name }}</td>
    <td>{{ $suggestion['field'] }}</td>
    <td></td>
    <td>{{ $suggestion['suggestion']->text }}</td>
    <td>
      <form method="post">
        <input name="suggestion_id" value="{{ $suggestion['suggestion']->id }}" type="hidden"/>
        <input name="operation" value="rejected" type="hidden"/>
        <input value="Rejeitar" type="submit"/>
      </form>
      <form method="post">
        <input name="suggestion_id" value="{{ $suggestion['suggestion']->id }}" type="hidden"/>
        <input name="operation" value="accepted" type="hidden"/>
        <input  value="Aceitar" type="submit"/>
      </form>
    </td>
  </tr>
@endforeach
