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

  function isOutOfRange(requested_page) {
     return requested_page < 1 || requested_page > paginator.last_page;
  }

  function getPage(requested_page, score_type) {
    if ( isOutOfRange(requested_page) ) {
      return -1;
    } else if ( requested_page == paginator.current_page
      && score_type == paginator.score_type ) {
      return 0;
    }
    score_type = score_type === undefined ? paginator.score_type : score_type;
    loadPage(requested_page, score_type);
    return 0;
  }

  function updatePaginator(data) {
    var select = $('select[name=score_type]');
    var btn_prev = $('.less-than'), btn_next = $('.greater-than');
    var type_text;
    paginator.current_page = data['current_page'];
    paginator.score_type = data['score_type'];
    if (paginator.current_page == 1) {
      btn_prev.addClass('disabled');
    } else {
      btn_prev.removeClass('disabled');
    }
    if (paginator.current_page == paginator.last_page) {
      btn_next.addClass('disabled');
    } else {
      btn_next.removeClass('disabled');
    }
    $('.page_number').val(paginator.current_page);
    select.val(paginator.score_type);
    type_text = select.find('[value=' + paginator.score_type +']').text();
    $('.score_type_header').text(capitalizeFirstLetter(type_text));
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function hasError(element, page_number) {
    if ( ! isInteger(page_number) || isOutOfRange(page_number) || page_number == '' ) {
      $(element).addClass('error');
      return true;
    } else {
      $(element).removeClass('error');
      return false;
    }
  }

  $(".page_number").keyup( function(event) {
    var page_number = $(this).val();
    if ( ! hasError(this, page_number) && event.keyCode == 13) {
      getPage(page_number);
    }
  });

  $(".page_number").blur( function(event) {
    var el = $(this);
    var page_number = el.val();
    hasError(this, page_number);
    if ( page_number == '' || ! isInteger(page_number) ) {
      el.val(paginator.current_page);
      el.removeClass('error');
    }
  });

  function isInteger(value) {
    return value == parseInt(value);
  }

  function loadPage(page, type) {
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

  $('.score_type').change( function(e) {
    var type = $(this).val();
    if ( type == paginator.score_type) {
      return;
    }
    getPage(1, type);
  });

});
