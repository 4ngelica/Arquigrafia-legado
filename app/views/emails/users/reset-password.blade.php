<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h3>Olá {{ $user->name }}</h3>

		<div>
			<p>Conforme a sua solicitação, segue uma senha temporária para acessar o Arquigrafia.</p>
			<br/>
			<p>Senha: {{$randomPassword}} </p>
			<p>Por favor, clique aqui {{ URL::to('users/login') }} para acessar o sistema com sua nova senha. </p>
			<p>Recomendamos fortemente que você altere a sua senha após o login. </p>
			<p>Se você encontrar algum problema para acessar com a nova senha envie um e-mail para: arquigrafiabr@gmail.com </p>

			<p>A equipe do projeto Arquigrafia agradece a sua preferência!</p>

		</div>
	</body>
</html>
