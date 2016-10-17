$(document).ready(function() {

	window.scrollTo(0,0);

	var nav_buttons = $(".menu-navegacao a");

	var nav_clicked = false;

	nav_buttons.each(function() {

		$(this).click(function (event) {

			nav_clicked = true;

			event.preventDefault();

			var previously_clicked = $(".active");
			previously_clicked.removeClass("active");
			$(this).children().addClass("active");

			var go_to = $(this).attr("href");

			$("html, body").animate({
				scrollTop: $(go_to).offset().top
			}, 900, 'easeInOutQuart', function () {
				nav_clicked = false;

			});
		});

	}); 


	var bottom_page = $("#bottom-page");
	bottom_page.click(function (event) {
		event.preventDefault();

		nav_clicked = true;

		var go_to = $(this).children().attr("href");
		$("html, body").animate({
			scrollTop: $(go_to).offset().top
		}, 900, 'easeInOutQuart', function () {
			nav_clicked = false;
		});

	});


	var sections_height = $(window).height();
	$(window).scroll(function() {
		if (!nav_clicked) {
			console.log(nav_clicked);
			var scroll_position = $(window).scrollTop();
			for (var i = 1; i <= 7; i++) {
				if (scroll_position >= sections_height*(i-1) && scroll_position <= sections_height*i) {
					var active_section = $("#page" + i.toString());
					if (!active_section.hasClass("active")) {
						var previously_clicked = $(".active");
						previously_clicked.removeClass("active");
						$('.menu-navegacao a[href="#page' + i + '"] div').addClass("active");
					}
				}
			}
		}
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


