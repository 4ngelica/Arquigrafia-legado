<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Olá, {{$name}}</h2>

        <div>
             
            Para finalizar o seu cadastro no Arquigrafia, por favor, acesse o link abaixo para confirmar o seu endereço de e-mail.<br/>  
            {{ URL::to('users/verify/' . $verifyCode) }}<br>

            Atenciosamente,
            Equipe do Arquigrafia.  

        </div>

    </body>
</html>