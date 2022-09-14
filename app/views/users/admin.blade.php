@extends('layouts.default')

@section('head')

<title>Arquigrafia - Gerenciar usuários</title>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>

@stop

@section('content')

<div class="container">
<h1>Gerenciar usuários</h1>
@if(json_decode(Auth::user()->roles))
 @if(json_decode(Auth::user()->roles)[0]->pivot->role_id == 1)
<table>
  <tr>
    <th>Nome</th>
    <th>Papel</th>
    <th>Active</th>
  </tr>
    @foreach($users as $user)
      <tr>
        <td><?php echo link_to("/users/".$user->id, $user->name) ?></td>
        <td>
          <form class="" action="{{ '/admin/' . $user->id }}" method="post">
          <select class="" name="role_id">
            @if(json_decode($user->roles))
            <option value="2" <?php if(json_decode($user->roles)[0]->pivot->role_id == 2){echo('selected');} ?> >Usuario</option>
            <option value="1"<?php if(json_decode($user->roles)[0]->pivot->role_id == 1){echo('selected');} ?>>Administrador</option>
            <!-- <option value="4"  if(json_decode($user->roles)[0]->pivot->role_id == 4){echo('selected');} >Responsible</option> -->
            @else
            <option value="2" selected>Usuario</option>
            <option value="1">Administrador</option>
            @endif
          </select>
            <button id="btnSubmit" type="submit" name="button">Salvar</button>
          </form>
        </td>
        <td>
          {{$user->active}}
        </td>
        <td>
          @if($user->active == 'yes')
          <form class="" action="{{ '/admin/' . $user->id }}" method="post">
            {{ Form::hidden('_method', 'DELETE') }}
            <button type="submit" name="button" disabled>Excluir</button>
          </form>
          @else
          <form class="" action="{{ '/admin/' . $user->id }}" method="post">
            {{ Form::hidden('_method', 'DELETE') }}
            <button type="submit" name="button">Excluir</button>
          </form>
          @endif
        </td>
      </tr>
    @endforeach
</table>
{{$users->links()}}
</div>
@else
Acesso negado.
@endif
@endif
@stop
