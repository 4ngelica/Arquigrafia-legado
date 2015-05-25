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

	$('.select_all').click(function() {
		var checked = $(this).is(':checked');
		var type = getType(this);
		checkPhotos(type, checked, paginators[type]);
	});

	$('.select_all + label').click(function() {
		var type = getType(this);
		var checkbox = $(this).siblings('.select_all');
		var check = checkbox.is(':checked');
		checkbox.prop('checked', !check);
		checkPhotos(type, !check, paginators[type]);
	});

	$('.ch_photo').live('click', function() {
		var type = getType(this);
		var paginator = paginators[type];
		paginator.selectedItems += ($(this).is(':checked') ? 1 : -1);
		updateSelectedItemsText(type, paginator.selectedItems);
		updateCheckBox(type, paginator);
	});

	$('.ch_photo + img').live('click', function () {
		var type = getType(this);
		var paginator = paginators[type];
		var checkbox = $(this).siblings('.ch_photo');
		checkbox.prop('checked', !checkbox.prop('checked'));
		paginator.selectedItems += (checkbox.is(':checked') ? 1 : -1);
		updateSelectedItemsText(type, paginator.selectedItems);
		updateCheckBox(type, paginator);
	});

	function checkPhotos(type, checked, paginator) {
		var currentPage = paginator.currentPage;
		var ch_photos = $( photosFromCurrentPage(type, paginator) );
		var checked_ch_photos = $( photosFromCurrentPage(type, paginator) + ':checked');
		var diff = ch_photos.length - checked_ch_photos.length;
		ch_photos.prop('checked', checked);
		paginator.selectedItems += (checked ? diff : -ch_photos.length);
		updateSelectedItemsText(type, paginator.selectedItems);
	}

	function updateSelectedItemsText(type, selectedItems) {
		if (selectedItems > 0) {
			$('#' + type + '_photos_btn').fadeIn();
			if (selectedItems == 1) {
				$('.' + type + ' p.selectedItems').html(selectedItems + ' imagem selecionada');
			} else {
				$('.' + type + ' p.selectedItems').html(selectedItems + ' imagens selecionadas');
			}
		} else {
			$('#' + type + '_photos_btn').fadeOut();
			$('.' + type + ' p.selectedItems').html('');
		}
	}

	function updateCheckBox(type, paginator) {
		var current_photos = photosFromCurrentPage(type, paginator);
		if ( $(current_photos).length != $(current_photos + ':checked').length ) {
			$('.' + type + ' .select_all').prop('checked', false);
		} else {
			$('.' + type + ' .select_all').prop('checked', true);
		}
	}

	function photosFromCurrentPage(type, paginator) {
		return '#' + type + '_page' + paginator.currentPage + ' .ch_photo';
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

	function getType(element) {
		if ( $(element).parent().hasClass('rm') ) {
			return 'rm';
		} else if ( $(element).parent().hasClass('rm') ) {
			return 'add';
		}
		return null;
	}

	function transition(paginator, page, type) {
		fixContentDivHeight(type);
		$('#' + type + '_page' + paginator.currentPage).hide();
		if (paginator.loadedPages.indexOf(page) >= 0) {
			$("#"+ type + "_page" + page).fadeIn();
			paginator.currentPage = page;
			$('.' + type + '.buttons p').html(paginator.currentPage + ' / ' + paginator.maxPage);
			updateCheckBox(type, paginator);
		} else {
			var callback = function(paginator, page, type, data) {
				$("#" + type).append(data['content']);
				paginator.loadedPages.push(page);
				paginator.currentPage = page;
				$('.' + type + '.buttons p').html(paginator.currentPage + ' / ' + paginator.maxPage);
				updateCheckBox(type, paginator);
			};
			requestPage(page, type, paginator.url, callback, paginator);
		}
	}
	//requisição ajax para navegar pelas páginas de fotos do álbum
	function requestPage(page, type, URL, callback, paginator) {
		var ret = 0;
		$("." + type + ".loader").show();
		$.get(URL + '?page=' + page + '&q=' + paginator.searchQuery)
		.done(function() {
			ret = 1;
		}).fail(function() {
			$(".message_box").message('Não foi possível atualizar seu álbum! Tente novamente mais tarde.', 'error');
		}).always(function(data) {
			$("." + type + ".loader").hide();
			if (ret == 1) {
				callback(paginator, page, type, data);
			} else {
				$('#' + type + '_page' + paginator.currentPage).show();
			}
		});
	}

	$('#rm_photos_btn').click(function(e) {
		e.preventDefault();
		var rm_photos = [];
		$.each($('[name="photos_rm[]"]:checked'), function() {
			rm_photos.push($(this).val());
		});
		var photos = { "photos_rm[]": rm_photos };
		actOnPhotos(photos, resetData, 'rm', 'detach');
	})

	function fixContentDivHeight(type, animate) {
		var maxHeight = $('#' + type).children('#' + type + '_page1').css('height');
		if (animate) {
			$('#' + type).animate({ 'height' : maxHeight });
		} else {
			$('#' + type).css({ 'height' : maxHeight });
		}
	}

	function actOnPhotos(photos, callback, type, action) {
		var loaderMarginTop = $('#' + type + '_page1').height() / 2;
		loaderMarginTop = (loaderMarginTop >= 150 ? 130 : loaderMarginTop);
		fixContentDivHeight(type);
		$('#'+ type + ' .page').hide();
		$('.' + type + '.loader').css({ 'margin-top' : loaderMarginTop }).show();
		$.post('/albums/' + album + '/' + action + '/photos?page=1&q=' + paginators[type].searchQuery, photos).done(function(response) { //volta tudo para a página 1
			$('.'+ type + '.loader').fadeOut('fast', function() {
				if (response == 'failed') {
					$('#' + type + '_page' + paginators[type].currentPage).fadeIn();
					$(".message_box").message('Não foi possível atualizar seu álbum! Tente novamente mais tarde.', 'error');
				} else {
					$('#' + type + ' .page').detach();
					$(response['content']).appendTo('#' + type).hide().fadeIn(function() {
						callback(type, response);
					});
				}
			});
		}).fail(function(xhr, status, error) {
			$('.' + type + '.loader').hide().delay(500);
			$('#' + type + '_page' + paginators[type].currentPage).fadeIn();
			$(".message_box").message('Não foi possível atualizar seu álbum! Tente novamente mais tarde.', 'error');
		});
	}

	var resetData = function(type, response) {
		clearPaginator(type, response['maxPage']);
		resetCheckboxAndText(type);
		fixContentDivHeight(type, true);
	};

	function clearPaginator(type, maxPage) {
		paginators[type].currentPage = 1;
		paginators[type].maxPage = maxPage;
		paginators[type].loadedPages = [1];
		paginators[type].selectedItems = 0;
	}

	function resetCheckboxAndText(type) {
		var paginator = paginators[type];
		$('.' + type + ' .select_all').prop('checked', false);
		$('#' + type + '_photos_btn').fadeOut();
		$('.' + type + ' p.selectedItems').html('');
		$('.' + type + '.buttons p').html(paginator.currentPage + ' / ' + paginator.maxPage);
	}

	$('.rm input[type=text].search_bar, .add input[type=text].search_bar').keypress(function(e) {
		if (e.which == 13) {
			var type = getType(this);
			var paginator = paginators[type];
			var text = $(this).val();
			if (type == null || text == '') {
				return;
			}
			e.preventDefault();
			searchPhotos(type, paginator, text);
		}
	});

	$('input[type=button].search_bar_button').click(function(e) {
		var type = getType(this);
		var paginator = paginators[type];
		var text = $(this).siblings('input[type=text]').val();
		if (type == null || text == '') {
			return;
		}
		e.preventDefault();
		searchPhotos(type, paginator, text);
	});

	function searchPhotos(type, paginator, text) {
		paginator.searchQuery = text;
		var callback = function (paginator, page, type, data) {
			$('#' + type + ' .page').detach();
			$(data['content']).appendTo('#' + type).hide().fadeIn(function() {
				resetData(type, data);
			});
		};
		$('#' + type + ' .page').hide();
		requestPage(1, type, paginator.url, callback, paginator);
	}

});