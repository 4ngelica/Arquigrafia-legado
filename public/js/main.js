$(document).ready(function() {

	var nav_buttons = $(".menu-navegacao a");
	var previously_clicked = "";

	nav_buttons.each(function() {

		$(this).click(function (event) {
			event.preventDefault();

			if (previously_clicked != "") {
				previously_clicked.children().removeClass("active");
			}
			$(this).children().addClass("active");

			var go_to = $(this).attr("href");

			$("html, body").animate({
				scrollTop: $(go_to).offset().top
			}, 900, 'easeInOutQuart');

			previously_clicked = $(this);
		});

	});

	var pins = $(".georref-hover");

	pins.each(function() {
		
		$(this).mouseover(function () {
			var place = $(this).attr("id");
			var img = $("#img-" + place);
			
			if (img.css("display") == "none")
				img.fadeIn();
			
		});

		$(this).mouseout(function () {
			var place = $(this).attr("id");
			var img = $("#img-" + place);

			if (img.css("display") == "block")
				img.fadeOut();
			
		});

	});

});