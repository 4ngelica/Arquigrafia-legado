@extends('layouts.default')

@section('head')
   <title>
      Arquigrafia - Esqueceu senha
   </title>   
@stop

@section('content')

   <div class="container">
      <div class="registration">
         <!-- LOGIN RECUPERAR SENHA -->
         @if($message == false)
         <div class="three columns offset-by-four">
            <h1>Recuperação de senha</h1>            
            {{ Form::open() }}

               <p>{{ Form::label('forgot', 'Esqueceu sua senha?') }}</p>
               <br>               
               <div class="two columns omega">{{ Form::text('email', '', array('class'=>'right','placeholder'=>'Insira seu e-mail') ) }}</div>
               <br>               
               <p>{{ Form::submit("Alterar Senha",array('class'=>'btn right')) }}</p>                           
               
            {{ Form::close() }}

            <p>&nbsp;</p>                       
         </div>  
         @endif 

          @if($message == true)
         <div class="three columns offset-by-four">
            <br><br>
            <h1>Recuperação de senha</h1> 
               <p> Car@ usuário,<br/>
                  Um email foi enviado a {{$email}} com as intruções para ingressar ao Sistema do Arquigrafia.</br>
                  Por favor, verifique seu email.</br>
                  Se você não recebi o email em um o dois minutos, 
                  tente reenviar as instruções ou verificar na lista de seu
                  spam.

               </p>
               <p>&nbsp;</p> 
               <p>&nbsp;</p> 
               <p>&nbsp;</p> 
         </div> 
         @endif  

      </div>
   </div>

   <div id="mask"></div>

@stop