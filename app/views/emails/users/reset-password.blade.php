<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Olá {{ $user->name }}</h2>

		<div>
			<!--Para alterar sua senha, é necessário que você ingresse no seu perfil e possa mudar sistema e possa
			To reset your password, complete this form: {{ URL::to('password/reset', array($token)) }}.<br/>
			This link will expire in {{ Config::get('auth.reminder.expire', 60) }} minutes.-->

			<p>Conforme a sua solicitação, segue uma senha temporal para ingressar ao Sistema de Arquigrafia.</p>
			<br/>
			<p>senha: {{$randomPassword}} </p>
			<p>Faz click aqui {{ URL::to('users/login') }} para ingressar novamente. </p>
			<p>Recomendamos fortemente, que apenas você ingresse no sistema de Arquigrafia, possa mudar sua senha.</p>
		</div>
	</body>
</html>
