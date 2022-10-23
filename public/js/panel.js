// JavaScript Document

$(document).ready(function(e) {
	// mansonry
	panel = $('#panel').isotope({
    itemSelector : '.item',
    layoutMode: 'masonryHorizontal',
    masonryHorizontal: {
      rowHeight: 114
    }
	});
});

$(window).on('load',function(e) {
	// resize by height
	/*
	$("#panel .item").each(function(i){
		var objh = $(this).height();
		var img = $(this).find("img");
		var imgh = img.height();
		if (imgh-10 < objh) {
			img.css({"width":"auto", "height": objh+20});
		}
	});
	*/

	// teste de carregamento
  imgcounter = 0;
  function changeImage() {
    var $item = $("#panel .item").eq(imgcounter);
	$item.find("img").attr("src", $item.find("img").data("src") );
	$item.find("img").bind("load", function(){$item.addClass('sharpen');});
    imgcounter++;
  }
  setInterval(changeImage, 10);

	// pan by depth
	/* $("body").mousemove(function(e) {
    var ww = $(window).width();
    var wh = $(window).height();
		// normaliza de -0.5 para 0.5
		xm = ((ww - e.pageX) / ww) - 0.5;
		xm = Math.round(xm*200)/10;
		ym = ((wh - e.pageY) / wh) - 0.5;
		ym = Math.round(ym*200)/10;
		console.log(xm);
		$("#panel .layer").each(function(i){
			var l = $(this);
			var depth = 10 * parseFloat( $(this).data("depth") );
			l.stop(true, true).animate({"top":(ym*depth), "left":(xm*depth) }, 2000);
		});
  }); */

	page = 1;
	$(".panel-back").hide(0);

	// navegação do painel
	$("#panel .layer").each(function(i){
		// left offset
		var lo = $(this).offset().left;
		var ww = $( window ).width();
		if (lo > ww ) $(this).addClass("off").fadeTo(0,0);
	});

	// CARREGAR PAINEL
	$(document).keydown(function(e){
    // NEXT
		if (e.keyCode == 39) {
			panelnext();
      return false;
    }
		// BACK
		if (e.keyCode == 37) {
			panelback();
		}
	});


	$(".panel-next").click(function(e) {
		// next
		panelnext();
  });
	$(".panel-back").click(function(e) {
		// back
		panelback();
	});

	function panelback() {
		if (page > 1) {
			page--;
			$(".panel-next").show(0);
			ww = $( window ).width();
			var pl = $("#panel").css("left");
			if ( parseInt(pl) < 0 ) $("#panel").stop(true,true).delay(200).animate({"left":"+=1000"}, 1000, function(){
				$("#panel .layer").each(function(i){
					// left offset
					var lo = $(this).offset().left;
					if (lo > ww ) $(this).addClass("off").fadeTo(0,0);
				});
			});
		} else {
			$(".panel-back").hide(0);
		}
	}

	function panelnext() {

    if (page < 60) {
			page++;
			$(".panel-back").show(0);
			$("#panel").animate({"left":"-=1000"}, 1000);
			var depth, mov;
			$("#panel .layer.off").each(function(i){
				var layer = $(this);
				var lo = layer.offset().left;
				var ww = $( window ).width();
				if ((lo - ww) < 1000) {
					depth = 10 * parseFloat( $(this).data("depth") );
					mov = Math.round(depth * 200);
					layer.removeClass("off").css({"left":"+="+mov}).delay(50*(i+5)).animate({"left":"-="+mov, "opacity":"1"},600);
				}
			});
		} else {
			$(".panel-next").hide(0);
		}
	}

});
