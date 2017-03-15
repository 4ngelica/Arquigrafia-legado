@extends('layouts.default')

@section('head')
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> -->
	<title>Chat - Arquigrafia</title>
    <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/checkbox.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/chat/chat.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/jquery.fancybox.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="{{ URL::to("/") }}/css/searchable-option-list/sol.css" />
    <script src="https://js.pusher.com/3.2/pusher.min.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/photo.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/chat/chat.js"></script>
    <script type="text/javascript" src="{{ URL::to("/") }}/js/searchable-option-list/sol.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.6/handlebars.min.js"></script>
    <script type="text/javascript">
        // Defining variables pushed from PHP
        var userID = {{ $user_id }};
        var userName = "{{ $user_name }}";
				var currentMessages = [];
				var currentChats = {{ json_encode($data) }};
				var currentChat;
				var usersSearch = []; // Users shown on search
				var sol; // Selectable options list
				console.log("Current Chats", currentChats);
    </script>

@stop

@section('content')
<div class="container sidebar-chat">
	<div class="row">
		<div class="five columns alpha">
			<div class="wrapper-flex">
				<h2>Mensagens</h2>
				<div class="new-message"><a href="#" onclick="pressedNewChat();">NOVA CONVERSA</a></div>
			</div>
			<div id="select-users-container">
				<select id="select-users" name="character" multiple="multiple"></select>
				<button id="btn-create-chat">OK</button>
			</div>
			<hr>

			<div id="chat-items">
				<!-- HANDLEBARS WILL RENDER CHAT ITENS HERE -->
			</div>
		</div>

		<div class="seven columns omega main-chat">
			<div class="header-main-chat" id="chat-header">
				<!-- WILL RENDER HERE THE CURRENT CHAT USER -->
			</div>
			<hr>
			<div id="chat" class="chat_box_wrapper" style="opacity: 1; display: block; transform: translateX(0px);">
			  <div class="chat_box touchscroll chat_box_colors_a" id="chat-messages">
					<!-- HANDLEBARS WILL RENDER THE MESSAGES HERE -->
			  </div>
			</div>
			<div class="chat_submit_box">
			    <div class="uk-input-group">
				    <div class="gurdeep-chat-box">
				    	<textarea placeholder="Digite sua mensagem aqui" id="message-input" name="submit_message" class="md-input"></textarea>
				    </div>
			    </div>
			    <input id="send-message" type="submit" class="submit-button" value=""></input>
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

<!-- HANDLEBARS TEMPLATES -->
<script id="chat-item-template" type="text/x-handlebars-template">
	<div id="chat-item-@{{chatIndex}}" class="wrapper-single-conversation" onclick="pressedChat(@{{ chatIndex }});">
		<div class="single-avatar">
			<a href=""><img class="avatar-image" src="@{{ avatarURL }}" /></a>
		</div>

		<div class="single-name">
			<h3>@{{ chatName }}</h3>
			<p>@{{ lastMessage }}</p>
		</div>
	</div>
</script>

<script id="message-right-block-template" type="text/x-handlebars-template">
	<div class="chat_message_wrapper chat_message_right">
		<div class="chat_user_avatar">
		<a href="" target="_blank" >
			<img src="@{{ avatarURL }}" class="md-user-image">
		</a>
		</div>
		<ul class="chat_message">
			@{{#each messages as |message|}}
			<li>
				<p>@{{ message.body }} @{{#if @last}}<span class="chat_message_time">@{{ ../hours }}</span>@{{/if}}</p>
			</li>
		  @{{/each}}
    </ul>
	</div>
</script>

<script id="message-left-block-template" type="text/x-handlebars-template">
  <div class="chat_message_wrapper">
    <div class="chat_user_avatar">
      <a href="" target="_blank" >
        <img src="@{{ avatarURL }}" class="md-user-image">
      </a>
    </div>
    <ul class="chat_message">
			@{{#each messages as |message|}}
				<li>
					<p>@{{ message.body }} @{{#if @last}}<span class="chat_message_time">@{{ ../hours }}</span>@{{/if}}</p>
				</li>
		  @{{/each}}
    </ul>
  </div>
</script>

@stop
