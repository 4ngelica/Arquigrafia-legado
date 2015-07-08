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
      console.log("Erro ao tentar carregar álbuns via AJAX!");
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
    $("#information_input").slideToggle('fast');
    loadQuestion();
  });
  
  $('#information_input a.close').click(function(e) {
    e.preventDefault();
    $('#information_input').slideUp('fast');
    $('#information_input div input[type="text"]').val('');
    $('#information_input div textarea').html('');
  });

  function loadQuestion() {
    url = getFieldURL;
    $.get(url).done(function (data) {
      var container = $('#information_input div');
      var field = data['field'];
      var question = data['question'];
      container.empty();
      container.append('<h3>' + question + '</h3>');
      if (field == 'description') {
        container.append('<textarea id="' + field + '" name="' + field + '"></textarea>');
      } else {
        container.append('<input type="text" id="' + field + '" name="' + field + '" value="" />');
      }
    }).fail(function (data) {
      console.log('Erro! Não foi possível consultar o servidor. Tente novamente mais tarde.');
    });
  }

});