// JavaScript Document

/* ONREADY */
$(document).ready(function(){
	
		$('.window .close').click(function (e) {
			e.preventDefault();
			$('#mask').fadeOut();
			$('.window').fadeOut('fast');
		});		

		$('#mask').click(function () {
			$(this).fadeOut();
			$('.window').fadeOut('fast');
		});
			
		$('#printer_icon').click(function() {
		  window.print();
		  return false;
		});

		$('#delete_button').click(function(e){
			e.preventDefault();
			$('#registration_delete form').attr('action', this.href);
			if ($(this).hasClass('album'))
				$('#registration_delete p').html('Tem certeza que deseja excluir este álbum?');
			else
				$('#registration_delete p').html('A imagem pode estar avaliada,tem certeza que deseja excluir esta imagem?');
			$('#mask').fadeIn('fast');
			$('#confirmation_window').fadeIn('slow');

		});

		$('#delete_photo').click(function(e){
			alert(this.href);
			e.preventDefault();
			$('#registration_delete form').attr('action', this.href);
			$('#registration_delete p').html('A imagem pode estar avaliada por outros usuários,tem certeza que deseja excluir esta imagem?');
			$('#mask').fadeIn('fast');
			$('#confirmation_window').fadeIn('slow');

		});

		$('.title_delete').click(function(e){
			e.preventDefault();
			$('#registration_delete form').attr('action', this.href);
			if ($(this).hasClass('album'))
				$('#registration_delete p').html('Tem certeza que deseja excluir este álbum?');
			else
				$('#registration_delete p').html('Tem certeza que deseja excluir esta imagem?');
			$('#mask').fadeIn('fast');
			$('#confirmation_window').fadeIn('slow');
		});		

		$('.title_plus').live('click', function (e){
			e.preventDefault();
			$('#mask').fadeIn('fast');
			$('#form_window').fadeIn('slow');
			$.get(this.href).done(function(data) {
				$("#registration").empty();
				$("#registration").append(data);
			})
			.fail(function() {
				console.log("Erro ao tentar carregar ábluns via AJAX!");
			});
		});
		
		$('#stoaLogin').live('click', function (e) {
			e.preventDefault();
			$('#mask').fadeIn('fast');
			$('#form_login_window').fadeIn('slow');
		});


	$('.leaderboard').live("click", (function (e) {
		e.preventDefault();
		$.ajax({
			type: "get",
			url: "../rank/get" ,
		})
		.done(function( o ) {
			$("#leaderboard").append('<div style="border:5px solid #000000;" id="leaderboard_content"></div>');
			$("#leaderboard_content").append('<center><h1> Leaderboard (Top 10) </h1></center>');
			$("#leaderboard_content").append('<a class="close" title="FECHAR">Fechar</a>');
			for(i=0;i<o.length;i++) {
				if(o[i].image==null){
					o[i].image="/img/avatar.png"
				} 
				if ($('#user-'+o[i].id).length == 0) {
					$("#leaderboard_content").append('<div class="user"><li><h1 style="display:inline" id="user-' + o[i].id + '">' + o[i].score + '</h1> <img style="height:45px" src=' + (o[i].image) + '/> ' +'<a href="users/' + o[i].id + '">' + o[i].name + "</a>" + '</li></div>');
				} else {
					$('#user-'+o[i].id).html(o[i].score);
				}
				
			}
			$('#leaderboard').fadeIn('fast');
			$('#leaderboard_content').fadeIn('slow');
			$("#container, #footer").css({  "opacity":"0.3","filter":"alpha(opacity=30)" });
			$("#leaderboard .close").live("click", function(e){
    			$('#leaderboard').fadeOut('fast');
				$('#leaderboard_content').fadeOut('slow');
				$("#container,#footer").css({  "opacity":"1","filter":"alpha(opacity=100)" })
    			$("#leaderboard_content").empty();
    		
    		});
		}).fail(function (jqXHR, textStatus) {
			console.log(textStatus);	
		});

		return false;
	}))	;


               





});

