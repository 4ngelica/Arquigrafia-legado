$(document).ready(function() {
	var all_covers = [];
	var empty_album_message = "<p>Para alterar a capa do álbum, é preciso ter pelo menos uma imagem.</p>";
	var select_cover_btn = "<a id=\"cover_btn\" href=\"#\" class=\"btn\">Selecionar capa</a>";
	$(".add_photo").on("change", function () {
		var selected_photos = $(".add_photo:checked");
		if (selected_photos.length == 0)
			$(".cover_btn_box").html(empty_album_message);
		if (selected_photos.length == 1)
			$(".cover_btn_box").html(select_cover_btn);
	});
	$(".cover_btn").on("click", function(e) {
		e.preventDefault();
		var callback = function(data) {
			generateCoversHtml(data);
		};
		coverPage = 1;
		$('#mask').fadeIn('fast');
		$('#form_window').fadeIn('slow');
		$.get("/albums/get/cover/" + album)
		.done(function(data) {
			callback(data);
		});
	});
	$(".covers").on("click", function(e) {
		if ($(this).is(":checked"))
		{
			var cover = $(".covers:checked").val();
			$("#_cover").val(cover);
			$("#cover").val(cover);
			$("#cover-img").attr("src", "/arquigrafia-images/" + cover + "_view.jpg");
			$("#cover-img").removeClass('hidden');
			$('.no_cover').addClass('hidden');
			$('#mask').fadeOut();
			$('#form_window').fadeOut('fast');
		}
	});

	$("#get_more_covers").on("click", function(e) {
		e.preventDefault();
		if (++coverPage == maxCoverPage)	$(this).hide();
		var removed_photos = $(".rm_photo:checked").map(function() { return $(this).val(); }).get();
		var callback = function(data) {
			all_covers = [];
			all_covers = all_covers.concat(data);
			all_covers = $.grep(all_covers, function(el) { return $.inArray( el, removed_photos ) == -1; })
			generateRowsAndCss(all_covers);
		};
		$.get("/albums/get/cover/" + album + "?page=" + coverPage)
		.done(function(data) {
			callback(data);
		});
	});
});

function generateCoversHtml(covers) {
	var coverHtml;
	var coversHtml =
		'<div class=\"list\">' +
			'<h2> Suas fotos </h2>' +
			'<p class=\"row\"> Selecione uma imagem para ser a capa</p>' +
			'<table class=\"form-table\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">' +
			'</table>';
		if (maxCoverPage > 1)
			coversHtml += '<a class=\"right\" id=\"get_more_covers\" href=\"#\">Ver mais</a>';
		coversHtml += '</div>';
	// coversHtml += '<p><a href=\"#\" id=\"select_cover\" class=\"btn\">Escolher capa</a></p>';
	$("#covers_registration").empty();
	$("#covers_registration").append(coversHtml);
	generateRowsAndCss(covers);
}

function generateRowsAndCss(covers) {
	covers_counter = 0;
	for (var i = covers_counter; i < covers.length; i++)
	{
		if (covers_counter % 3 == 0) {
			coverHtml = "";
			coverHtml += "<tr>";
		}
		coverHtml += '<td>';
		coverHtml += '<input type=\"checkbox\" class=\"covers\" id=\"cover_' + covers[i] + '\" value=\"' + covers[i] + '\">';
		coverHtml += '<label for=\"cover_' +  covers[i] + '\"></label>'
		coverHtml += '</td>';
		if (covers_counter % 3 == 2) {
			coverHtml += "</tr>";
			$("#covers_registration table").append(coverHtml);
		}
		covers_counter++;
	}
	if (covers_counter % 3 != 0) {
		coverHtml += "</tr>";
		$("#covers_registration table").append(coverHtml);
	}
	for (var i = 0; i < covers.length; i++)
	{
		$("#cover_" + covers[i] + " + label").css('background', 'url(/arquigrafia-images/' + covers[i] + '_home.jpg)');
	}
}
