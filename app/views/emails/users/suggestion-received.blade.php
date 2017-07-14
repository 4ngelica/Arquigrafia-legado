<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Olá, {{$name}}</h2>

        <div>

            Sua imagem acaba de ser analisada por um membro do Arquigrafia com sugestões para incluir novas informações sobre ela.<br>
            Por favor, acesse o link abaixo para visualizar as sugestões feitas.<br>
            {{ URL::to('users/' . $id . '/suggestions') }}<br>

            Atenciosamente,
            Equipe do Arquigrafia.

        </div>

    </body>
</html>
