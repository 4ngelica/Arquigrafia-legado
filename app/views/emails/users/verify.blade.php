<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Bemvindo o Arquigrafia, {{$name}}</h2>

        <div>
             
            Você deu o primeiro passo para ser parte de Arquigrafia, assim nós queremos tomar um momento
            para pessoalmente dar a você as boas vindas. <br>

            Nós pensamos que Arquigrafia é um bom lugar para compartilhar imagens arquitetônicas, e é 
            excelente ter você a bordo!<br>

            Por favor siga o link abaixo para confirmar o seu endereço de e-mail.<br/>  
            {{ URL::to('users/verify/' . $verifyCode) }}.<br>

            Muito obrigado pelo seu tempo.        

        </div>

    </body>
</html>