$(document).ready(function() {

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

  function getPage(requested_page) {
    if ( isOutOfRange(requested_page) ) {
      return -1;
    } else if ( requested_page == paginator.current_page ) {
      return 0;
    }
    loadPage(requested_page);
    return 0;
  }

  function updatePaginator(data) {
    var btn_prev = $('.less-than'), btn_next = $('.greater-than');
    paginator.current_page = data['current_page'];
    paginator.last_page = data['last_page'];
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
    $('.draft_last_page').html(paginator.last_page);
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
    if ( hasError(this, page_number) ) {
      el.val(paginator.current_page);
      el.removeClass('error');
    }
  });

  function isInteger(value) {
    return value == parseInt(value);
  }

  function updateView(data) {
    var tbody = $('.form-table.drafts').find('tbody');
    tbody.html(data['view']);
    $('.draft_count').html(data['total_items']);
  }

  function loadPage(page) {
    var url = paginator.url + '?page=' + page + '&perPage=' + paginator.per_page;    
    $.get(url).done(function (data) {
      data = parseData(data);
      updateView(data);
      updatePaginator(data);
    }).fail(function() {
      var message = 'Não foi possível atualizar a página! Tente novamente mais tarde.'
      $(".message_box").message(message, 'error');
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

  $('.delete_draft').on('click', function (evt) {
    $('a.delete_draft_confirm').data('draft', $(this).data('draft'));
    $('#mask').fadeIn('fast');
    $('#draft_window').fadeIn('slow');
  });

  $('.delete_draft_confirm').on('click', function (evt) {
    var id = $(this).data('draft');
    var draft = $('#draft_' + id);
    var data = {
      draft: id,
      per_page: paginator.per_page,
      last_page: paginator.last_page
    };
    $.post(delete_url, data).done(function (data) {
      data = parseData(data);
      console.log(data);
      if ( data == false ) {
        var message = 'Não foi possível atualizar a página! Tente novamente mais tarde.'
        $(".message_box").message(message, 'error');
        return;
      }
      if ( data['refresh'] == true) {
        updateView(data);
        updatePaginator(data);
      } else {
        draft.fadeOut(400, function () { draft.remove() });  
        $('.draft_count').html(data['total_items']);
      }
    });
    evt.preventDefault();
    $('#mask').fadeOut();
    $('#draft_window').fadeOut('fast');
  });
});
