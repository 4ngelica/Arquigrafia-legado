

$(function(){
	
    $('.fancybox').fancybox({
	  	
  	  beforeShow: function () {
          	$.fancybox.wrap.bind("contextmenu", function (e) {
                  return false; 
          	});
      },
				
      afterLoad : function() {
			var download = $('#single_view_image_buttons');
         	if (download.size() === 0) {
				this.title = '<a id="download_login_link" href="/users/login">Faça o login para fazer o download</a>';
			} else {
				var buttons = $("#single_view_buttons_box").clone(),
				social_network_buttons = buttons.find("#single_view_social_network_buttons");
								
				social_network_buttons.remove();
				this.title = '' + buttons.html();
			}

      },

      scrolling: 'no', 
      minWidth: 500,
      minHeight: 600,
      
    });

    $('#delete_photo').live('click', function(e){
		return confirm('Tem certeza que deseja excluir esta imagem?');
	});

	$('#plus').live('click', function(e){
		e.preventDefault();
		$.fancybox.close(true);
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

	$("#like_button").click(function(e) {
		var like_button = $(this);
		e.preventDefault();
		$.get(this.href).done(function(data) {
			if (data == 'fail') {
				return;
			}
			data = $.parseJSON(data);
			like_button.toggleClass('dislike');
			like_button.attr('href', data.url);
			$("#likes + small").text(data.likes_count);
			//console.log(typeof data);
			//console.log(data['likes_count']);
		});
		
	});

	$(".like_comment").click(function(e) {
		var like = $(this);
		var like_text = like.text();
		e.preventDefault();
		$.get(this.href).done(function(data) {
			if (data == 'fail') {
				return;
			}
			data = $.parseJSON(data);
			like.attr('href', data.url);
			if (like_text == 'Curtir') {
				like.text('Descurtir');
			}
			else{
				like.text('Curtir');
			}
			like.parent().parent().find('.likes').text(data.likes_count);
			});

	});
});

