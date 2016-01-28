var pageRequestRunning = false;

//alert(window.location.href);

$(window).load(function() {
	//atribuir pagina
	var b = document.querySelector("#pageCur"); 
	
	//b.setAttribute("disabled", "disabled");
});

$(document).ready(function() { 
if($('#pgVisited').val() != 0){ 
	//if($('#typeSearch').val() == 'advance'){ 
		var type = "add";
		var pageCurrentAdv = $('#pageCurrent1').val();
		var p = getPaginator(type); 
		var pageInitial = document.getElementById("add_page"+pageCurrentAdv);
		pageInitial.id = "add_page1";
		document.getElementById("add_page1").setAttribute("style","display: none;");
		clearContent(type);
		fixPageContainerHeight(type);
		changePage(p, pageCurrentAdv, type);
		
}


    	
$(".greater-than").click(function(e) { 
		//adition
		var currentPg = $('#pageCurrent1').val(); 	
		//alert(currentPg);
		if(currentPg != 1){ //currentPg != null || 
			deletedTable(currentPg);
		}	
		var type = $(this).getType();
		var p = getPaginator(type); 
		
		e.preventDefault();
		if (p.currentPage < p.maxPage) {
			var newPage = ( $(this).hasClass('greater') ? p.maxPage : parseInt(p.currentPage) + 1 );
			
			$('#pageLinked').val(newPage);
			changePage(p, newPage, type);

		}
	});

 function deletedTable(currentPage){  				
	//document.getElementById(pageDelete).remove();		
	//document.getElementById("add_page"+currentPg).remove();	
 	$('#add_page'+currentPage).remove();
 }

function getPaginator(type) { 	
	return paginators[type];
}

function changePage(paginator, page, type) {
	//* */
	var currentPg = $('#pageCurrent1').val(); 
	if($("#" + currentPg).length == 0 && page == currentPg) {
		if(paginator.loadedPages.indexOf(currentPg) >= 0){
		//alert("current"+currentPg);
		var callback = function(type, data, paginator, currentPg) {			    
			$("#" + type).append(data['content']);
			//console.log(data['content']);
			paginator.loadedPages.push(currentPg);
			showPage(currentPg, type);
		};
		requestPage(currentPg, type, paginator.url, callback, paginator);
	  }
	}	
	
	//* */

	$('#pageLinked').val(page); 
	//alert(paginator.loadedPages.indexOf(page));
	if (paginator.loadedPages.indexOf(page) >= 0) { 
		fixPageContainerHeight(type);
		clearContent(type);
		showPage(page, type);

		if(page == 1 || page == paginator.maxPage){
			deletedTable(page);
			var callback = function(type, data, paginator, page) {			    
			$("#" + type).append(data['content']);
				console.log(data['content']);
				paginator.loadedPages.push(page);
				showPage(page, type);
			};
			requestPage(page, type, paginator.url, callback, paginator);
		}

	} else { //alert("callback"+page+"tp="+type);
		var callback = function(type, data, paginator, page) {			    
			$("#" + type).append(data['content']);
			console.log(data['content']);
			paginator.loadedPages.push(page);
			showPage(page, type);
		};
		requestPage(page, type, paginator.url, callback, paginator);

	}
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

function showPage(page, type) {
	//addition
	document.getElementById("add").style.height = "685px";

	var paginator = getPaginator(type);
	$('#' + type + '_page' + page).fadeIn();
	paginator.currentPage = page;	
	updatePaginationButtons(type);
	updateSelectAllCheckbox(type, paginator);
}

function requestPage(page, type, URL, callback, paginator, runInBackground) {
	var data = { page: page, q: paginator.searchQuery, wp: $('input[name=which_photos]:checked').val() };
	clearContent(type);
	if (pageRequestRunning) {
		return;
	} else {
		pageRequestRunning = true;
	}
	if (!runInBackground) {
		fixPageContainerHeight(type);
		showAndFixElementSpacing(type, $('.' + type + ' .loader'), true);
	}
	//console.log(URL);
	//console.log(data);
	//console.log(page);
	$.get(URL, data)
	.done(function(data) {
		$("." + type + " .loader").hide();
		data = parseData(data);
		if (data == false) {
			failedRequest(type, 'Aconteceu um erro! Tente novamente mais tarde.');	
		} else {
			callback(type, data, paginator, page);
		}
	}).fail(function() {
		$("." + type + " .loader").hide();
		failedRequest(type, 'Aconteceu um erro! Tente novamente mais tarde.');
	}).always(function() {
		pageRequestRunning = false;
	});
}
function updatePaginationButtons(type) {
	var paginator = getPaginator(type);
	var buttons = $('.' + type + ' .buttons');
	buttons.find('p').text(paginator.currentPage + ' / ' + paginator.maxPage);
}
function updateSelectAllCheckbox(type, paginator) {
	var current_photos = photosFromCurrentPage(type, paginator);
	var checked = ( $(current_photos).length == $(current_photos + ':checked').length );
	$('.' + type + ' .select_all').prop('checked', checked);
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
function showAndFixElementSpacing(type, element, adjustMarginLeft) {
	var container = element.parent();
	var containerHeight = container.height();
	var marginTop;
	if (containerHeight == 0) {
		container.css({ 'min-height' : 300 });
		containerHeight = 300;
	}
	marginTop = containerHeight / 2 - element.height() / 2;
	element.css({ 'margin-top' : marginTop });
	if (adjustMarginLeft) {
		var containerWidth = container.width();
		var marginLeft = containerWidth / 2 - element.width() / 2;
		element.css({ 'margin-left' : marginLeft });
	}
	element.show();
}

$.fn.extend({
	
	getPosition: function() {
		return $(this).hasClass('left') ? 'left' : 'right';
	},
	message: function (message, message_type) {
		var message_box = $(this);
		message_box.addClass(message_type).text(message).fadeIn()
			.delay(2000).fadeOut(400, function () {
				message_box.removeClass(message_type);
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

function hidePages(type) {
	$('#' + type + ' .page').hide();
}

function parseData(data) {
	try {
		return (typeof data == 'string') ? $.parseJSON(data) : data;
	} catch (err) {
		console.error(err.message);
	}
	return false;
}

function photosFromCurrentPage(type) { 	
	var paginator = getPaginator(type);
	return '#' + type + '_page' + paginator.currentPage + ' .ch_photo';
}

$(".less-than").click(function(e) { 
		var type = $(this).getType();
		var p = getPaginator(type);
		e.preventDefault();
		if (p.currentPage > 1) {
			var newPage = ( $(this).hasClass('less') ? 1 : parseInt(p.currentPage) - 1 );
			changePage(p, newPage, type);
		}
	});

});