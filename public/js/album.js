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
			$(".message_box").message('Não foi possível atualizar seu álbum! Tente novamente mais tarde.', 'error');
		});
	});

	$.fn.extend({
   	message: function (message, type) {
   		var message_box = $(this);
      	message_box.addClass(type).html(message).fadeIn()
      		.delay(3000).fadeOut(400, function () {
      			message_box.removeClass(type);
      		});
   	}
	});

	$('.loader').hide();
	if (paginators['add'].maxPage < 2) {
		$('.add.buttons').hide();
	}
	if (paginators['rm'].maxPage < 2) {
		$('.rm.buttons').hide();
	}

   $('#rm_select_all').click(function() {
      checkPhotos('rm');
   });

   $('.rm_photo').click(function() {
     if ($('.rm_photo:checked').length == 0) {
       $('#rm_photos_btn').fadeOut();
       $('#rm_select_all').prop('checked', false);
     } else {
       $('#rm_photos_btn').fadeIn();
     }
   });

	function getType(element) {
		if ( $(element).parent().hasClass('rm') ) {
			return 'rm';
		}
		return 'add';
	}

	$(".less-than").click(function(e) {
		e.preventDefault();
		var type = getType(this);
		var p = paginators[type];
		if (p.currentPage > 1) {
			if ( $(this).hasClass('less')) {
				transition(p, 1, type);
			} else {
				transition(p, p.currentPage - 1, type);
			}
		}
	});

	$(".greater-than").click(function(e) {
		e.preventDefault();
		var type = getType(this);
		var p = paginators[type];
		if (p.currentPage < p.maxPage) {
			if ( $(this).hasClass('greater')) {
				transition(p, p.maxPage, type);
			} else {
				transition(p, p.currentPage + 1, type);
			}
		}
	});

});

function transition(paginator, page, type) {
	$('#' + type + '_page' + paginator.currentPage).hide();
	if (paginator.loadedPages.indexOf(page) >= 0) {
		$("#"+ type + "_page" + page).fadeIn();
		paginator.currentPage = page;
		$('.' + type + '.buttons p').html(paginator.currentPage + ' / ' + paginator.maxPage);
	} else {
		var callback = function(paginator, page) {
			if (paginator.loadedPages.indexOf(page) < 0) {
				paginator.loadedPages.push(page);
				paginator.currentPage = page;
				$('.' + type + '.buttons p').html(paginator.currentPage + ' / ' + paginator.maxPage);
			}
		};
		requestPage(page, type, paginator.url, callback, paginator);
	}
}

function requestPage(page, type, URL, callback, paginator) {
	var ret = 0;
	$("." + type + ".loader").show();
	$.get(URL + '?page=' + page)
	.done(function() {
		ret = 1;
	}).fail(function() {
		$(".message_box").message('Não foi possível atualizar seu álbum! Tente novamente mais tarde.', 'error');	
	}).always(function(data) {
		$("." + type + ".loader").hide();
		if (ret == 1) {
			$("#" + type).append(data);
			callback(paginator, page, type);
		}
	});
}

function checkPhotos(type) {
	if ($('#' + type + '_select_all').is(':checked')) {
		$('.' + type + '_photo').prop('checked', true);
		$('#' + type + '_photos_btn').fadeIn();
	} else {
		$('.' + type + '_photo').prop('checked', false);
		$('#' + type + '_photos_btn').fadeOut();
	}
}