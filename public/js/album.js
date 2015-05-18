$(document).ready(function() {
	
	$("#album_info form").submit(function(e) {
		e.preventDefault();
		var cover = $("#_cover").val();
		var title = $("#title").val();
		var description = $("#description").val();
		var data = { title: title, description: description, cover_id: cover };
		var url = $(this).attr('action');
		$.post(url, data).done(function(response) {
			if (response === 'success') {
				$("#info .error:first").html('');
				$("#album_title").html('Edição de ' + title);
				$(".message_box").message('Informações do álbum atualizadas com sucesso!', 'success');
			} else {
				$("#info .error:first").append(response.title[0]);
			}
		}).fail(function() {
			alert("Ocorreu um erro! Tente novamente mais tarde");
		});
	});

	$.fn.extend({
   	message: function (message, type) {
   		var message_box = $(this);
      	message_box.addClass(type).html('Informações do álbum atualizadas com sucesso!').fadeIn()
      		.delay(3000).fadeOut(400, function () {
      			message_box.removeClass(type);
      		});
   	}
	});

});