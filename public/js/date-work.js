jQuery(function($) {
    
    var period = {
        'Before':['Anterior ao ano de 1401'],
        'XV': ['De 1 a 100'],
        'XVI': ['De 101 a 200'],
        'XVII': ['De 201 a 300'],
        'XVIII': ['De 301 a 400'],
        'XIX': ['De 301 a 400'],
        'XX': ['De 301 a 400'],
        'XXI': ['De 301 a 400'],
    }
    var decade ={
        'XV': ['1401 a 1410','1411 a 1420','1421 a 1430','1431 a 1440'],
        'XVI': ['1501 a 1510','1511 a 1520','1521 a 1530','1531 a 1540'],
    }

    var $period = $('#period_select');
    var $decade = $('decade_select');
    var txtPeriod = '';
      //  alert(period);
    $('#century').change(function () {
        var century = $(this).val(), lcns = period[century] || [];
        var decadeRange = decade[century]; //|| [];
        alert('sec='+century+'-lcns='+lcns+'arra'+lcns[0]+'decadR='+decadeRange);
        var i=0;
       // var select='';
        var html = $.map(lcns, function(lcn){
            /*i++;
            if(i==1){
                centuryPeriod ='Período: <label>'+ lcn+'</label>';
               //select = '<option value="">' + lcn + '</option>';
            }else{
                //select = '<option value="' + lcn + '">' + lcn + '</option>';
                centuryPeriod ='Período: <label>'+ lcn+'</label>';
            }

            //return centuryPeriod//'<option value="' + lcn + '">' + lcn + '</option>' */
            txtPeriod = 'Período: <label>'+ lcn+'</label>';
            //txtDecRange = '<option value="' + decRange + '">' + decRange + '</option>';

            return 'Período: <label>'+ lcn+'</label>';

        }).join('');
        $period.html(html)
    });



   var dayArray = {
        'janeiro': ['1','2','3'],
        'fevereiro': ['1','2','3'],
        'marco': ['1','2','3'],
        'abril': ['1','2','3'],
   }

   var $dayArray = $('#day_select');

   $('#month').change(function () {
        var month = $(this).val(), lcns = dayArray[month] || [];
        alert('sec='+month+'-lcns='+lcns+' arra'+lcns[0]);
        var i=0;
        var select='';
        var html = $.map(lcns, function(lcn){
            i++;
            if(i==1){
               select = '<option value="">' + lcn + '</option>';
            }else{
                select = '<option value="' + lcn + '">' + lcn + '</option>';
            }
            return select//'<option value="' + lcn + '">' + lcn + '</option>'

        }).join('');
        $dayArray.html(html)
    });

});