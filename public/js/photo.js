$(function(){
  try {
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
  } catch(err) {
    console.error('Função fancybox não definida.');
  }
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

  $('#denounce_photo').live('click', function(e){
    e.preventDefault();
    $.fancybox.close(true);
    $('#mask').fadeIn('fast');
    $('#form_window_notify').fadeIn('slow');
    $.get(this.href).done(function(data) {
      $("#registration_notify").empty();
      $("#registration_notify").append(data);
    })
    .fail(function() {
      console.error("Erro ao tentar carregar foto via AJAX!");
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
      $('#likes').closest('span').attr('title', data.likes_count + " pessoas curtiram essa imagem");
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

  applyMasks();
  
  $('.date_interval').click(function() {
    var radio = $(this);
    var checked = radio.val();
    var date_content = radio.closest('.date_container').find('.date_content').first();
    var date_field = getDateField(radio.closest('.date_container'));
    if (!checked) {
      date_content.find('.interval_text').text('');
      date_content.find('.date_box').last().empty();
    } else if ( !( $('[name=' + date_field + '2]').length ) ) {
      var date_type = $('[name=' + date_field + '1]').attr('class');
      var content = (contentSetter[date_type])(date_field, true);
      var date_boxes = date_content.find('.date_box');
      date_content.find('.interval_text').text('-');
      date_boxes.last().empty().append(content, '<p class="date_translation"></p>');
      applyMasks();
    }
  });

  $('.date').on('change', function() {
    var option = $(this);
    var date_container = option.closest('.date_container');
    var date_type = option.val();
    var interval = date_container.find('.date_interval:checked').val();
    setDateContainer(date_container, date_type, interval);
  });

  // $('.date + label').click(function() {
  //   $(this).siblings('#' + this.htmlFor).prop('checked', true).trigger('click');
  // });

  $('.decade, .century').live('change', function() {
    var select = $(this);
    translateDate(select);  
  });
});

  function applyMasks() {
    try {
      $('.day').inputmask("99/99/9999", 
      { 
        'oncomplete' : function() {
          console.log($(this).val())
          var t = translateDay( $(this).val() );
          if (t) {
            $(this).siblings('.date_translation').removeClass('error').text(t);
          } else {
            $(this).siblings('.date_translation').addClass('error').text('Data inválida');
          }
         }
      });
      $('.month').inputmask("99/9999", 
      { 
        'oncomplete' : function() {
          console.log($(this).val())
          var t = translateMonth( $(this).val() );
          if (t) {
            $(this).siblings('.date_translation').removeClass('error').text(t);
          } else {
            $(this).siblings('.date_translation').addClass('error').text('Data inválida');
          }
         }
      });
      $('.year').inputmask("9999", 
      { 
        'oncomplete' : function() {
          var t = translateYear( $(this).val() );
          if (t) {
            $(this).siblings('.date_translation').removeClass('error').text(t);
          } else {
            $(this).siblings('.date_translation').addClass('error').text('Data inválida');
          }
         }
      });
    } catch(err) {
      console.log("Não foi possível utilizar inputmask");
    }

  }

  var translator = {
    day: translateDay,
    month: translateMonth,
    year: translateYear,
    decade: translateDecade,
    century: translateCentury
  };

  function translateDate(element) {
    var date_type = element.attr('class');
    var date_value = element.val();
    var translation;
    translation = (translator[date_type])(date_value);    
    element.siblings('.date_translation').text(translation);
  }

  function translateYear(year) {
    var validacao_data = '01/01/' + year;
    if (!validaData(validacao_data)) {
      return false;
    }
    return 'Ano de ' + year;
  }

  function translateMonth(month) {
    var months = [
      'janeiro',
      'fevereiro',
      'março',
      'abril',
      'maio',
      'junho',
      'julho',
      'agosto',
      'setembro',
      'outubro',
      'novembro',
      'dezembro'
    ];
    var validacao_data = '01/' + month;
    var month_index = parseInt(month.split('/')[0], 10) - 1;
    var year = month.split('/')[1];
    if (!validaData(validacao_data)) {
      return false;
    }
    return capitalizeFirstLetter(months[month_index]) + ' de ' + year;
  }

  function translateDay(date) {
    var months = [
      'janeiro',
      'fevereiro',
      'março',
      'abril',
      'maio',
      'junho',
      'julho',
      'agosto',
      'setembro',
      'outubro',
      'novembro',
      'dezembro'
    ];
    if (!validaData(date)) {
      return false;
    }
    var day = date.split('/')[0];
    var month_index = parseInt(date.split('/')[1]) - 1;
    var year = date.split('/')[2];
    return day + ' de ' + months[month_index] + ' de ' + year;
  }

  function translateCentury(century) {
    return 'Século ' + romanize(century);
  }

  function translateDecade(decade) {
    return 'Década de ' + (decade * 10);
  }

  var contentSetter = {
    day: getDayContent,
    month: getMonthContent,
    year: getYearContent,
    decade: getDecadeContent,
    century: getCenturyContent
  }

  function setDateContainer(date_container, date_type, interval) {
    var content1, content2;
    var date_field = getDateField(date_container);
    var setter = contentSetter[date_type];
    content1 = setter(date_field, false);
    content2 = interval ? setter(date_field, true) : null;
    date_container.fadeOut(100, function() {
      var date_boxes = $(this).find('.date_box');
      date_boxes.first().empty().append(content1)
        .append('<p class="date_translation"></p>');
      if (interval) {
        date_boxes.last().empty().append(content2)
        .append('<p class="date_translation"></p>');
      }
      $(this).fadeIn(100, function() {
        $(this).children().first().focus();
        applyMasks();
      });
    });
  }

  function getDayContent(date_field, interval) {
    var today = new Date();
    var month = today.getMonth() + 1;
    month = (month < 10 ? '0' + month : month);
    var date = today.getDate() + '/' + month + '/' + today.getFullYear();
    var number = interval ? 2 : 1;
    var name = 'name="' + date_field + number + '"';
    return '<input type="text" ' + name + ' class="day" placeholder="Ex.: ' + date + '" >';
  }

  function getMonthContent(date_field, interval) {
    var today = new Date();
    var month = today.getMonth() + 1;
    month = (month < 10 ? '0' + month : month);
    var date = month + '/' + today.getFullYear();
    var number = interval ? 2 : 1;
    var name = 'name="' + date_field + number + '"';
    return '<input type="text" ' + name + ' class="month" placeholder="Ex.: ' + date + '" >';
  }

  function getYearContent(date_field, interval) {
    var date = new Date().getFullYear();
    var number = interval ? 2 : 1;
    var name = 'name="' + date_field + number + '"';
    return '<input type="text" ' + name + ' class="year" placeholder="Ex.: ' + date + '" >';
  }

  function getDecadeContent(date_field, interval) {
    var number = interval ? 2 : 1;
    var name = 'name="' + date_field + number + '"';
    var select = $('<select ' + name + ' class="decade"></select>');
    var options = [];
    var decades = getDecades();
    var decade_keys = [];
    for (var k in decades) {
      if ( decades.hasOwnProperty(k) ) {
        decade_keys.push(k);
      }
    }
    select.append('<option value="">- - - -</option>');
    for (var i = decade_keys.length - 1; i >= 0; i--) {
      var decade = decade_keys[i];
      select.append('<option value="' + decade + '">' + decades[decade] + '</option>');
    }
    return select;
  }

  function getCenturyContent(date_field, interval) {
    var number = interval ? 2 : 1;
    var name = 'name="' + date_field + number + '"';
    var select = $('<select ' + name + ' class="century"></select>');
    var options = [];
    var centuries = getCenturies();
    var century_keys = [];
    for (var k in centuries) {
      if ( centuries.hasOwnProperty(k) ) {
        century_keys.push(k);
      }
    }
    select.append('<option value="">- - -</option>');
    for (var i = century_keys.length - 1; i >= 0; i--) {
      var century = century_keys[i];
      select.append('<option value="' + century + '">' + centuries[century] + '</option>');
    }
    return select;
  }

  function getDecades() {
    var decades = {};
    var maxYear = parseInt(new Date().getFullYear() / 10);
    for (var i = maxYear; i >= 150; i--) {
      decades[i] = i * 10;
    }
    return decades;
  }

  function getCenturies() {
    var centuries = {};
    var maxYear = parseInt(new Date().getFullYear() / 100);
    for (var i = maxYear + 1; i >= 15; i--) {
      centuries[i] = romanize(i);
    }
    return centuries;
  }

  function romanize (num) {
    if (!+num)
      return false;
    var digits = String(+num).split(""),
      key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
             "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
             "","I","II","III","IV","V","VI","VII","VIII","IX"],
      roman = "",
      i = 3;
    while (i--)
      roman = (key[+digits.pop() + (i * 10)] || "") + roman;
    return Array(+digits.join("") + 1).join("M") + roman;
  }

  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  function getDateField(date_container) {
    var parent_id = date_container.attr('id');
    return parent_id.substring(0, parent_id.indexOf('_container'));
  }

  function validaData(data) {
    var data_split = data.split('/');
    var dia = parseInt(data_split[0], 10);
    var mes = parseInt(data_split[1], 10);
    var ano = parseInt(data_split[2], 10);
    var novaData = new Date(ano,mes-1,dia);
    var hoje = new Date();
    var primeiro_dia_valido;

    if (ano.toString().length != 4) {
      return false;
    }
    if (novaData.getFullYear() != ano ||
        novaData.getMonth() + 1 != mes || novaData.getDate() != dia) {
      return false;
    }
    if (novaData > hoje) {
      return false;
    }

    return true;
  }
