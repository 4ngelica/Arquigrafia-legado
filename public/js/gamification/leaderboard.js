$(document).ready(function() {
  var lb = $('#leaderboard');

  $('.greater-than').click(function(e) {
    e.preventDefault();
    getPage(paginator.current_page + 1);
  });

  $('.less-than').click(function(e) {
    e.preventDefault();
    getPage(paginator.current_page - 1);
  });

  function getPage(requested_page) {
    if ( requested_page < 1 || requested_page > paginator.last_page ) {
      return;
    }
    loadPage(requested_page);
  }

  function updatePaginator(data) {
    paginator.current_page = data['current_page'];
    paginator.score_type = data['score_type'];
    var btn_prev = $('.less-than'), btn_next = $('.greater-than');
    if (paginator.current_page == 1) {
      btn_prev.addClass('disabled');
    } else {
      btn_prev.removeClass('disabled');
    }
    console.log(paginator.last_page);
    if (paginator.current_page == paginator.last_page) {
      btn_next.addClass('disabled');
    } else {
      btn_next.removeClass('disabled');
    }
    $('input[name=page]').val(paginator.current_page);
    $('select[name=score_type]').val(paginator.score_type);
  }

  function loadPage(page) {
    var type = paginator.score_type;
    var url = paginator.url + '?type=' + type + '&page=' + page;
    var loader = lb.children('.loader').first();
    var table = lb.children('table').first();
    var tbody = table.find('tbody');
    var content = tbody.html();
    table.addClass('opaque');
    loader.removeClass('hidden');
    $.get(url).done(function (data) {
      data = parseData(data);
      tbody.html(data['view']);
      updatePaginator(data);
    }).fail(function() {
      var message = 'Não foi possível atualizar a página! Tente novamente mais tarde.'
      $(".message_box").message(message, 'error');
    }).always(function() {
      loader.addClass('hidden');
      table.removeClass('opaque');
    });
  }

  function parseData(data) {
    return (typeof data == 'string') ? $.parseJSON(data) : data;
  }

  $.fn.extend({
    message: function (message, message_type) {
      var message_box = $(this);
      message_box.addClass(message_type).text(message).fadeIn()
        .delay(2000).fadeOut(400, function () {
          message_box.removeClass(message_type);
        });
    }
  });

});
