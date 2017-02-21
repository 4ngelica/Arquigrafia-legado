@extends('layouts.default')

@section('head')
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> -->
	<title>Chat - Arquigrafia</title>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/chat/chat.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
    <script src="https://js.pusher.com/3.2/pusher.min.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/chat/chat.js"></script>
    <script type="text/javascript">
        // Defining variables pushed from PHP
        var userID = {{ $user_id }};
        var userName = "{{ $user_name }}";
    </script>

@stop

@section('content')
<div class="container sidebar-chat">
	<div class="row">
		<div class="five columns alpha">
			<div class="wrapper-flex">
				<h2>Mensagens</h2>
				<div class="new-message"><a href="">Nova conversa</a></div>
			</div>
			<hr>

			<div class="wrapper-single-conversation">
				<div class="single-avatar">
					<a href=""><img src="images/avatar.jpg" /></a>
				</div>

				<div class="single-name">
					<h3>Cynthia Franco</h3>
					<p>gostei muito dessa foto que voce postou ontem! estive la ontem e lem...</p>
				</div>
			</div>

			<div class="wrapper-single-conversation">
				<div class="single-avatar">
					<a href=""><img src="images/avatar.jpg" /></a>
				</div>

				<div class="single-name">
					<h3>Cynthia Franco</h3>
					<p>gostei muito dessa foto que voce postou ontem! estive la ontem e lem...</p>
				</div>
			</div>

			<div class="wrapper-single-conversation">
				<div class="single-avatar">
					<a href=""><img src="images/avatar.jpg" /></a>
				</div>

				<div class="single-name">
					<h3>Cynthia Franco</h3>
					<p>gostei muito dessa foto que voce postou ontem! estive la ontem e lem...</p>
				</div>
			</div>
		</div>

		<div class="seven columns omega main-chat">
		<div class="header-main-chat">
			<h2><a href="">Cinthia Franco</a></h2>
		</div>
		<hr>

<div id="chat" class="chat_box_wrapper" style="opacity: 1; display: block; transform: translateX(0px);">
    <div class="chat_box touchscroll chat_box_colors_a">

        <div class="chat_message_wrapper">
            <div class="chat_user_avatar">
                <a href="" target="_blank" >
                	<img src="images/avatar2.jpg" class="md-user-image">
                </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, eum? </p>
                </li>
                <li>
                    <p> Lorem ipsum dolor sit amet.<span class="chat_message_time">13:38</span> </p>
                </li>
            </ul>
        </div>


        <div class="chat_message_wrapper chat_message_right">
            <div class="chat_user_avatar">
            <a href="" target="_blank" >
                <img src="images/avatar.jpg" class="md-user-image">
            </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem delectus distinctio dolor earum est hic id impedit ipsum minima mollitia natus nulla perspiciatis quae quasi, quis recusandae, saepe, sunt totam.
                        <span class="chat_message_time">13:34</span>
                    </p>
                </li>
            </ul>
        </div>

        <div class="chat_message_wrapper">
            <div class="chat_user_avatar">
                <a href="" target="_blank" >
                	<img src="images/avatar2.jpg" class="md-user-image">
                </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, eum? </p>
                </li>
                <li>
                    <p> Lorem ipsum dolor sit amet.<span class="chat_message_time">13:38</span> </p>
                </li>
            </ul>
        </div>


        <div class="chat_message_wrapper chat_message_right">
            <div class="chat_user_avatar">
            <a href="" target="_blank" >
                <img src="images/avatar.jpg" class="md-user-image">
            </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem delectus distinctio dolor earum est hic id impedit ipsum minima mollitia natus nulla perspiciatis quae quasi, quis recusandae, saepe, sunt totam.
                        <span class="chat_message_time">13:34</span>
                    </p>
                </li>
            </ul>
        </div>


        <div class="chat_message_wrapper">
            <div class="chat_user_avatar">
                <a href="" target="_blank" >
                	<img src="images/avatar2.jpg" class="md-user-image">
                </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, eum? </p>
                </li>
                <li>
                    <p> Lorem ipsum dolor sit amet.<span class="chat_message_time">13:38</span> </p>
                </li>
            </ul>
        </div>


        <div class="chat_message_wrapper chat_message_right">
            <div class="chat_user_avatar">
            <a href="" target="_blank" >
                <img src="images/avatar.jpg" class="md-user-image">
            </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem delectus distinctio dolor earum est hic id impedit ipsum minima mollitia natus nulla perspiciatis quae quasi, quis recusandae, saepe, sunt totam.
                        <span class="chat_message_time">13:34</span>
                    </p>
                </li>
            </ul>
        </div>


        <div class="chat_message_wrapper">
            <div class="chat_user_avatar">
                <a href="" target="_blank" >
                	<img src="images/avatar2.jpg" class="md-user-image">
                </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio, eum? </p>
                </li>
                <li>
                    <p> Lorem ipsum dolor sit amet.<span class="chat_message_time">13:38</span> </p>
                </li>
            </ul>
        </div>


        <div class="chat_message_wrapper chat_message_right">
            <div class="chat_user_avatar">
            <a href="" target="_blank" >
                <img src="images/avatar.jpg" class="md-user-image">
            </a>
            </div>
            <ul class="chat_message">
                <li>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem delectus distinctio dolor earum est hic id impedit ipsum minima mollitia natus nulla perspiciatis quae quasi, quis recusandae, saepe, sunt totam.
                        <span class="chat_message_time">13:34</span>
                    </p>
                </li>
            </ul>
        </div>
          

    </div>
</div>

<div class="chat_submit_box">
    <div class="uk-input-group">
	    <div class="gurdeep-chat-box">
	    	<textarea placeholder="Digite sua mensagem aqui" id="submit_message" name="submit_message" class="md-input"></textarea>
	    </div>
    </div>
    <input type="submit" class="submit-button" value=""></input>
</div>

		</div>
	</div>
</div>


<script>
	$(function() {                       //run when the DOM is ready
  $(".wrapper-single-conversation").click(function() {  //use a class, since your ID gets mangled
    $(".wrapper-single-conversation.active").removeClass("active");
    $(this).addClass("active");      //add the class to the clicked element
  });
});
</script>

@stop