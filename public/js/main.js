$(document).ready(function() {

	window.scrollTo(0,0);

	var nav_buttons = $(".menu-navegacao a");

	nav_buttons.each(function() {

		$(this).click(function (event) {
			event.preventDefault();

			var previously_clicked = $(".active");
			previously_clicked.removeClass("active");
			$(this).children().addClass("active");

			var go_to = $(this).attr("href");

			$("html, body").animate({
				scrollTop: $(go_to).offset().top
			}, 900, 'easeInOutQuart');
		});

	});

	var sections_height = $(window).height();
	$(window).scroll(function() {
		var scroll_position = $(window).scrollTop();
		for (var i = 1; i <= 7; i++) {
			if (scroll_position >= sections_height*(i-1) && scroll_position <= sections_height*i) {
				var active_section = $("#page" + i.toString());
				if (!active_section.hasClass("active")) {
					console.log(active_section.attr("id"));
					var previously_clicked = $(".active");
					previously_clicked.removeClass("active");
					$('.menu-navegacao a[href="#page' + i + '"] div').addClass("active");
				}
			}
		}
	})

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