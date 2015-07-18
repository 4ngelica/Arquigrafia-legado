$(function(){

    $('.fancybox').fancybox({
      
      beforeShow: function () {
            $.fancybox.wrap.bind("contextmenu", function (e) {
                  return false; 
            });
      },
        
      afterLoad : function() {
      var download = $('#single_view_image_buttons');
           if (download.size() === 0) {
        this.title = '<a id="download_login_link" href="/users/login">Faça o login para fazer o download</a>';
      } else {
        var buttons = $("#single_view_buttons_box").clone(),
        social_network_buttons = buttons.find("#single_view_social_network_buttons");
                
        social_network_buttons.remove();
        this.title = '' + buttons.html();
      }

      },

      scrolling: 'no', 
      minWidth: 500,
      minHeight: 600,
      
    });

    $('#delete_photo').live('click', function(e){
    return confirm('A imagem pode estar avaliada, tem certeza que deseja excluir esta imagem?');
  });

});

$(document).ready(function() {

  $('#plus').live('click', function(e){
    e.preventDefault();
    $.fancybox.close(true);
    $('#mask').fadeIn('fast');
    $('#form_window').fadeIn('slow');
    $.get(this.href).done(function(data) {
      $("#registration").empty();
      $("#registration").append(data);
    })
    .fail(function() {
      console.error("Erro ao tentar carregar álbuns via AJAX!");
    });
  });

  $("#like_button").click(function(e) {
    var like_button = $(this);
    e.preventDefault();
    $.get(this.href).done(function(data) {
      if (data == 'fail') {
        return;
      }
      if (typeof data == 'string') {
        data = $.parseJSON(data);
      }
      like_button.toggleClass('dislike');
      like_button.attr('href', data.url);
      like_button.attr('title', like_button.hasClass('dislike') ? 'Descurtir' : 'Curtir' );
      $("#likes + small").text(data.likes_count);
      $('#likes').closest('spam').attr('title', data.likes_count + " pessoas curtiram essa imagem");
    });
    
  });

  $(".like_comment").click(function(e) {
    var like = $(this);
    var like_text = like.text();
    e.preventDefault();
    $.get(this.href).done(function(data) {
      if (data == 'fail') {
        return;
      }
      if (typeof data == 'string') {
        data = $.parseJSON(data);
      }

      like.attr('href', data.url);
      if (like_text == 'Curtir') {
        like.text('Descurtir');
      }
      else{
        like.text('Curtir');
      }
      like.parent().parent().find('.likes').text(data.likes_count);
      });

  });
  
  $('#improve_image_data').click(function(e) {
    e.preventDefault();
    if (!hasField) {
      loadQuestion();
    }
    $("#information_input").slideToggle('fast');
  });

  $('#skip_question').click(function(e) {
    e.preventDefault();
    loadQuestion();
  });
  
  $('#information_input a.close').click(function(e) {
    e.preventDefault();
    $('#information_input').slideUp('fast');
    $('#information_input div input[type="text"]').val('');
    $('#information_input div textarea').html('');
  });

  function loadQuestion(callback) {
    var parent_container = $('#information_input');
    var loader = parent_container.children('.loader');
    var container = parent_container.children().children('div');
    var url = getFieldURL + '?fp=' + currentField;
    currentField++;
    showLoader(loader);
    $.get(url).done(function (data) {
      data = parseData(data);
      container.empty();
      hideLoader(loader);
      if (!data['end']) {
        appendQuestion(container, data['field'], data['question']);
        hasField = true;
      } else if (!data['complete']) {
        currentField = 0;
        container.empty();
        hasField = false;
        loader.siblings().hide();
        parent_container.fadeOut('fast');
      } else {
        hideInformationInputContainer(parent_container);
      }
    }).fail(function (data) {
      hideLoader(loader);
      console.error('Erro! Não foi possível consultar o servidor. Tente novamente mais tarde.');
    });
  }

  function hideInformationInputContainer(parent_container) {
    parent_container.empty().fadeOut('fast');
    $('#improve_image_data').hide();
  }

  function appendQuestion(container, field, question) {
    container.append('<h3>' + question + '</h3>');
    container.append('<input name="field" type="hidden" value="' + field + '" />')
    if (field == 'description') {
      container.append('<textarea name="' + field + '"></textarea>');
    } else {
      container.append('<input type="text" name="' + field + '" value="" />');
    }
  }

  function showAndFixElementSpacing(element, adjustMarginLeft) {
    var container = element.parent();
    var containerHeight = container.height();
    var marginTop;
    if (containerHeight == 0) {
      container.css({ 'min-height' : 100 });
      containerHeight = 100;
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

  function parseData(data) {
    return (typeof data == 'string') ? $.parseJSON(data) : data;
  }

  function showLoader(loader) {
    loader.siblings().hide();
    showAndFixElementSpacing(loader, false);
  }

  function hideLoader(loader) {
    loader.siblings().show();
    loader.hide();
  }

  $('#information_input form').submit(function(e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    $.post(setFieldURL, data).done(function(data) {
      data = parseData(data);
      console.log(data);
      updateFieldContainer(data);
      currentField--;
      loadQuestion();
      progressbar.levelUp(data['information_completion']);
    }).fail(function(data){
      console.error('Não foi possível atualizar os dados da imagem');
    });
  });

  function updateFieldContainer(data) {
    var p;
    var container = $('#' + ( data['is_address'] ? 'address' : data['field'] ) + '_container' );
    container.empty().hide();
    container.append(data['html']);
    p = container.children('p');
    p.css({ 'background-color' : '#ffff99' });
    container.fadeIn(1500, function () {
      p.css({ 'background-color': '#fff' });
    });
  }

});