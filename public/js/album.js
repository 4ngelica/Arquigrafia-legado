$(document).ready(function() {

	$("#album_info form").submit(function(e) {
		e.preventDefault();
		var url = $(this).attr('action');
		var data = $(this).serializeArray();
		$.post(url, data).done(function(response) {
			if (response === 'success') {
				$("#info .error:first").text('');
				$("#album_title").text('Edição de ' + $("#title").val());
				$(".message_box").message('Informações do álbum atualizadas com sucesso!', 'success');
			} else {
				$("#info .error:first").append(response.title[0]);
			}
		}).fail(function() {
			var message = 'Não foi possível atualizar seu álbum! Tente novamente mais tarde.';
			$(".message_box").message(message, 'error');
		});
	});

	$('.select_all').click(function() {
		toggleCurrentPagePhotos($(this));
	});

	$('.select_all + label').click(function() {
		var checkbox = $(this).siblings('.select_all');
		checkbox.toggleCheckbox();
		toggleCurrentPagePhotos(checkbox);
	});

	$('.ch_photo').live('click', function() {
		updatePageInfo($(this));
	});

	$('.ch_photo + img').live('click', function () {
		var checkbox = $(this).siblings('.ch_photo');
		checkbox.toggleCheckbox();
		updatePageInfo(checkbox);
	});

	$(".less-than").click(function(e) {
		var type = $(this).getType();
		var p = getPaginator(type);
		e.preventDefault();
		if (p.currentPage > 1) {
			var newPage = ( $(this).hasClass('less') ? 1 : p.currentPage - 1 );
			changePage(p, newPage, type);
		}
	});

	$(".greater-than").click(function(e) {
		var type = $(this).getType();
		var p = getPaginator(type);
		e.preventDefault();
		if (p.currentPage < p.maxPage) {
			var newPage = ( $(this).hasClass('greater') ? p.maxPage : p.currentPage + 1 );
			changePage(p, newPage, type);
		}
	});


	$('#rm_photos_btn').click(function(e) {
		e.preventDefault();
		var rm_photos = [];
		$.each($('[name="photos_rm[]"]:checked'), function() {
			rm_photos.push($(this).val());
		});
		var photos = { "photos_rm[]": rm_photos };
		detachOrAttachPhotos(photos, updateContent, 'rm', 'detach');
	});

	$('#add_photos_btn').click(function(e) {
		e.preventDefault();
		var photos = [];
		$.each($('[name="photos_add[]"]:checked'), function() {
			photos.push($(this).val());
		});
		var photos = { "photos_add[]": photos };
		detachOrAttachPhotos(photos, updateContent, 'add', 'attach');
	});


	$('.rm input[type=text].search_bar, .add input[type=text].search_bar').keypress(function(e) {
		if (e.which == 13) {
			e.preventDefault();
			getTextAndSearchPhotos($(this));
		}
	});

	$('input[type=button].search_bar_button').click(function(e) {
		e.preventDefault();
		getTextAndSearchPhotos($(this).siblings('input[type=text]'));
	});

	$('input[name=which_photos]').click(function() {
		var wp = $(this).val();
		if (wp == which_photos) {
			return;
		}
		which_photos = wp;
		searchPhotos('add', '');
	});

	$('.search_bar').toolTip('search-bar-info-theme');
	tooltipPhotos();

});

$(document).ajaxComplete(function() {
	tooltipPhotos();
});

$.fn.extend({
	toolTip: function(tooltip_theme, tooltip_content, tooltip_position) {
		var options = {}
		options.contentAsHTML = true;
		options.theme =  tooltip_theme;
		if (typeof tooltip_content === 'undefined') {
			tooltip_content = $(this).attr('title');
		}
		options.content =  tooltip_content;
		if (typeof tooltip_position !== 'undefined') {
			options.position = tooltip_position;
		}
		$(this).tooltipster(options);
	},
	getPosition: function() {
		return $(this).hasClass('left') ? 'left' : 'right';
	},
	message: function (message, type) {
		var message_box = $(this);
		message_box.addClass(type).text(message).fadeIn()
			.delay(3000).fadeOut(400, function () {
				message_box.removeClass(type);
			});
	},
	isChecked: function() {
		return $(this).is(':checked');
	},
	toggleCheckbox: function() {
		var checked = $(this).isChecked();
		$(this).prop('checked', !checked);
	},
	getType: function() {
		if ( $(this).closest('.rm').length ) {
			return 'rm';
		} else if ( $(this).closest('.add').length ) {
			return 'add';
		}
		return null;
	}
});

function tooltipPhotos() {
	$('.ch_photo + img').each(function() {
		var element = this;
		var id = $(this).data('id');
		var img = $('<img />').attr('src', '/arquigrafia-images/' + id + '_view.jpg')
			.load(function () {
				if (!this.complete || typeof this.naturalWidth == "undefined" || this.naturalWidth == 0) {
					alert('broken image: ' + id);
				} else {
					var photo_content = $('<div></div>');
					var title = $(element).attr('title');
					photo_content.append($(this));
					photo_content.append('<p>' + title + '</p>');
					
					$(element).toolTip('image-tooltip-theme', photo_content.html(),
						$(element).getPosition());
				}
			});
	});
}

function getPaginator(type) {
	return paginators[type];
}

function photosFromCurrentPage(type) {
	var paginator = getPaginator(type);
	return '#' + type + '_page' + paginator.currentPage + ' .ch_photo';
}

function toggleCurrentPagePhotos(checkbox) {
	var type = checkbox.getType();
	var paginator = getPaginator(type);
	var checked = checkbox.isChecked();
	var current_photos = photosFromCurrentPage(type);
	var checked_photos = $( current_photos + ':checked');
	var unchecked_photos = $( current_photos + ':not(:checked)');
	$(current_photos).prop('checked', checked); // check or uncheck all checkboxes from current page
	paginator.selectedItems += (checked ? unchecked_photos.length : -checked_photos.length);
	updateSelectedItemsText(type, paginator.selectedItems);
}

function updatePageInfo(checkbox) {
	var type = checkbox.getType();
	var paginator = getPaginator(type);
	paginator.selectedItems += (checkbox.isChecked() ? 1 : -1);
	updateSelectedItemsText(type, paginator.selectedItems);
	updateSelectAllCheckbox(type, paginator);
}

function updateSelectedItemsText(type, selectedItems) {
	var message = '';
	if (selectedItems > 0) {
		message = selectedItems;
		message += (selectedItems == 1 ? ' imagem selecionada' : ' imagens selecionadas');
		$('#' + type + '_photos_btn').fadeIn();
	} else {
		$('#' + type + '_photos_btn').fadeOut();
	}
	$('.' + type + ' p.selectedItems').text(message);
}

function updateSelectAllCheckbox(type, paginator) {
	var current_photos = photosFromCurrentPage(type, paginator);
	var checked = ( $(current_photos).length == $(current_photos + ':checked').length );
	$('.' + type + ' .select_all').prop('checked', checked);
}

function changePage(paginator, page, type) {
	if (paginator.loadedPages.indexOf(page) >= 0) {
		fixPageContainerHeight(type);
		clearContent(type);
		showPage(page, type);
	} else {
		var callback = function(type, data, paginator, page) {
			$("#" + type).append(data['content']);
			paginator.loadedPages.push(page);
			showPage(page, type);
		};
		requestPage(page, type, paginator.url, callback, paginator);
	}
}

function requestPage(page, type, URL, callback, paginator) {
	var data = { page: page, q: paginator.searchQuery, wp: $('input[name=which_photos]:checked').val() };
	console.log(data);
	fixPageContainerHeight(type);
	clearContent(type);
	showAndFixElementSpacing(type, $('.' + type + ' .loader'));
	$.get(URL, data)
	.done(function(data) {
		$("." + type + " .loader").hide();
		callback(type, data, paginator, page);
	}).fail(function() {
		$("." + type + " .loader").hide();
		failedRequest(type, 'Aconteceu um erro! Tente novamente mais tarde.');
	});
}

function failedRequest(type, message) {
	paginator = getPaginator(type);
	showPage(paginator.currentPage, type);
	$(".message_box").message(message, 'error');
}

var resetData = function(type, response) {
	updateFilterText(type, response['count']);
	clearPaginator(type, response['maxPage']);
	resetCheckboxAndText(type);
	fixPageContainerHeight(type, true);
};

function clearPaginator(type, maxPage) {
	var paginator = getPaginator(type);
	paginator.currentPage = 1;
	paginator.maxPage = maxPage;
	paginator.loadedPages = [1];
	paginator.selectedItems = 0;
}

function resetCheckboxAndText(type) {
	var paginator = getPaginator(type);
	$('.' + type + ' .select_all').prop('checked', false);
	updateSelectedItemsText(type, 0);
	updatePaginationButtons(type);
}


function getTextAndSearchPhotos(element) {
	var type = element.getType();
	var text = element.val();
	searchPhotos(type, text);
}

function searchPhotos(type, text) {
	var paginator = getPaginator(type);
	paginator.searchQuery = text;
	var callback = function (type, data) {
		updateContent(type, data);
	};
	requestPage(1, type, paginator.url, callback, paginator);
}

var updateContent = function(type, response) {
	$('#' + type + ' .page').remove();
	if (response['empty']) {
			var msg;
			if (type == 'add') {
				msg = 'Não foi encontrada nenhuma imagem para ser adicionada ao seu álbum.'
			} else if (paginators[type].searchQuery == '') {
				msg = 'Seu álbum está vazio.'
			} else {
				msg = 'Sua pesquisa não retornou nenhuma imagem.'
			}
			$('<p>' + msg + '</p>').appendTo('#' + type).hide();
			showAndFixElementSpacing(type, $('#' + type + ' p'));
			resetData(type, response);
	} else {
		$(response['content']).appendTo('#' + type).hide().fadeIn(function() {
			resetData(type, response);		
		});
	}
};
function updateFilterText(type, photo_count) {
	var paginator = getPaginator(type);
	var message = 'Todas as imagens';
	if (paginator.searchQuery != '') {
		message = 'Filtro: "' + paginator.searchQuery + '"';
	}
	message += ' (' + photo_count + ')';
	$('.' + type + ' .filter').text(message);
}
function hidePages(type) {
	$('#' + type + ' .page').hide();
}

function showPage(page, type) {
	var paginator = getPaginator(type);
	$('#' + type + '_page' + page).fadeIn();
	paginator.currentPage = page;
	updatePaginationButtons(type);
	updateSelectAllCheckbox(type, paginator);
}

function updatePaginationButtons(type) {
	var paginator = getPaginator(type);
	var buttons = $('.' + type + ' .buttons');
	buttons.find('p').text(paginator.currentPage + ' / ' + paginator.maxPage);
}

function showAndFixElementSpacing(type, element) {
	var containerHeight = element.parent().height();
	var containerWidth = element.parent().width();
	var marginTop = containerHeight / 2 - element.height() / 2;
	var marginLeft = containerWidth / 2 - element.width() / 2;
	element.css({ 'margin-top' : marginTop, 'margin-left' : marginLeft });
	element.show();
}

function fixPageContainerHeight(type, animate) {
	var maxHeight = $('#' + type).children('#' + type + '_page1').css('height');
	if (typeof maxHeight === 'undefined') {
		maxHeight = $('#' + type).css('height');
	}
	if (animate) {
		$('#' + type).animate({ 'height' : maxHeight });
	} else {
		$('#' + type).css({ 'height' : maxHeight });
	}
}

function clearContent(type) {
	hidePages(type);
	$('#' + type).children('p').remove();
}

function detachOrAttachPhotos(photos, callback, type, action) {
	var paginator = getPaginator(type);
	fixPageContainerHeight(type);
	clearContent(type);
	showAndFixElementSpacing(type, $('.' + type + ' .loader'));
	photos['q'] = paginator.searchQuery;
	photos['wp'] = $('input[name=which_photos]:checked').val();
	$.post('/albums/' + album + '/' + action + '/photos?page=1', photos).done(function(response) { //volta tudo para a página 1
		$('.'+ type + ' .loader').hide();
		if (response == 'failed') {
			failedRequest(type, 'Não foi possível atualizar seu álbum! Tente novamente mais tarde.');
		} else {
			callback(type, response);
		}
	}).fail(function() {
		$('.' + type + ' .loader').hide();
		failedRequest(type, 'Não foi possível atualizar seu álbum! Tente novamente mais tarde.');
	});
}

