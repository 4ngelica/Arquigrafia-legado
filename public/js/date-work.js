function date_visibility(id) {
       var e = document.getElementById(id);        
       //alert($(e).attr("id"));
       //alert("id="+id);
       if(e.style.display == 'none'){
          e.style.display = 'block'; //alert("aparece");
       }else{
          e.style.display = 'none';
          //alert("esconde");
        }
        if(id == "otherDate")
          $("#answer_date").text(""); 
        else
          $("#answer_date_image").text(""); 
       
}



function retrieveYearDate(year){
  $('#workDate').val(year);
}

function retrieveCentury(century){
   // alert("N="+century);
    //if(type=="workDate"){
      $('#century').val(century);
    /*}else{
      $('#century_image').val(century);
    } */ 
}

function retrieveCenturyImage(century){
    // alert("I="+century);
    $('#century_image').val(century);
}

function retrieveDecade(decade){
 //   if(type=="workDate"){
        $('#decade_select').val(decade);
   // }else{
     //   $('#decade_select_image').val(decade);
   // }          
}

function retrieveDecadeImage(decade){
   // if(type=="workDate"){
   //     $('#decade_select').val(decade);
   // }else{
        $('#decade_select_image').val(decade);
   // }          
}

//function show


 $(function() {
    
    /*$( "#workDate" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );
     $( "#country" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );   
     $( "#state" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );   */    
  });

 
var period = {
        'Before':['Anterior ao ano de 1401'],
        'XV': ['De 1401 a 1500'],
        'XVI': ['De 1501 a 1600'],
        'XVII': ['De 1601 a 1700'],
        'XVIII': ['De 1701 a 1800'],
        'XIX': ['De 1801 a 1900'],
        'XX': ['De 1901 a 2000'],
        'XXI': ['De 2001 a 2100'],
    }
    var decade ={
        'NS':  ['Escolha década','Anterior ao ano de 1401','1401 a 1410','1411 a 1420','1421 a 1430','1431 a 1440','1441 a 1450',
                '1451 a 1460','1461 a 1470','1471 a 1480','1481 a 1490','1491 a 1500',
                '1501 a 1510','1511 a 1520','1521 a 1530','1531 a 1540','1541 a 1550',
                '1551 a 1560','1561 a 1570','1571 a 1580','1581 a 1590','1591 a 1600',
                '1601 a 1610','1611 a 1620','1621 a 1630','1631 a 1640','1641 a 1650',
                '1651 a 1660','1661 a 1670','1671 a 1680','1681 a 1690','1691 a 1700',    
                '1701 a 1710','1711 a 1720','1721 a 1730','1731 a 1740','1741 a 1750',
                '1751 a 1760','1761 a 1770','1771 a 1780','1781 a 1790','1791 a 1800',
                '1801 a 1810','1811 a 1820','1821 a 1830','1831 a 1840','1841 a 1850',
                '1851 a 1860','1861 a 1870','1871 a 1880','1881 a 1890','1891 a 1900',
                '1901 a 1910','1911 a 1920','1921 a 1930','1931 a 1940','1941 a 1950',
                '1951 a 1960','1961 a 1970','1971 a 1980','1981 a 1990','1991 a 2000',     
                '2001 a 2010','2011 a 2020'
                //'2021 a 2030','2031 a 2040','2041 a 2050',
                //'2051 a 2060','2061 a 2070','2071 a 2080','2081 a 2090','2091 a 2100'
                ],    
        'Before': ['Anterior ao ano de 1401'],        
        'XV': ['Escolha década','1401 a 1410','1411 a 1420','1421 a 1430','1431 a 1440','1441 a 1450',
                '1451 a 1460','1461 a 1470','1471 a 1480','1481 a 1490','1491 a 1500'],
        'XVI': ['Escolha década','1501 a 1510','1511 a 1520','1521 a 1530','1531 a 1540','1541 a 1550',
                '1551 a 1560','1561 a 1570','1571 a 1580','1581 a 1590','1591 a 1600'],
        'XVII': ['Escolha década','1601 a 1610','1611 a 1620','1621 a 1630','1631 a 1640','1641 a 1650',
                '1651 a 1660','1661 a 1670','1671 a 1680','1681 a 1690','1691 a 1700'],    
        'XVIII': ['Escolha década','1701 a 1710','1711 a 1720','1721 a 1730','1731 a 1740','1741 a 1750',
                '1751 a 1760','1761 a 1770','1771 a 1780','1781 a 1790','1791 a 1800'],
        'XIX': ['Escolha década','1801 a 1810','1811 a 1820','1821 a 1830','1831 a 1840','1841 a 1850',
                '1851 a 1860','1861 a 1870','1871 a 1880','1881 a 1890','1891 a 1900'],  
        'XX': ['Escolha década','1901 a 1910','1911 a 1920','1921 a 1930','1931 a 1940','1941 a 1950',
                '1951 a 1960','1961 a 1970','1971 a 1980','1981 a 1990','1991 a 2000'],      
        'XXI': ['Escolha década','2001 a 2010','2011 a 2020'
                //,'2021 a 2030','2031 a 2040','2041 a 2050',
                //'2051 a 2060','2061 a 2070','2071 a 2080','2081 a 2090','2091 a 2100'
                ],   
    }

 function getCenturyOfDecade(decade,eventType){
    
    if(decade != "BD" && decade != null){ 
        decadeDigit = decade.substring(0,4);
        century = centuryOfDecade(decadeDigit);
        showPeriodCentury(century);
        $('#century').val(century);
       //filter decades
        filterDecadesOfCentury(century);
        $('#decade_select').val(decade);
    }else if(decade == "BD"){ 
        century = "Before";
        showPeriodCentury(century);
        $('#century').val(century);
        //aqu embaix
        filterDecadesOfCentury(century);
        //$("#period_select").text("");//(Período:  Anterior ao ano de 1401");
    }else{ //decade_select ==null
      
        $("#decade_select").val("");
        $("#century").val(""); 
        //$("#decade_select").val("");
        $("#answer_date").text("");
        $("#period_select").text("");
    }
    

 }    

 function centuryOfDecade(decadeDigit){
    var century = "";     
    var decadeDigit = parseInt(decadeDigit);

    if(1401 <= decadeDigit && decadeDigit <= 1500){
       century = "XV";
    }else if(1501 <= decadeDigit && decadeDigit <= 1600){
       century = "XVI";
        //alert("CC"+century);
    }else if(1601 <= decadeDigit && decadeDigit <= 1700){
       century = "XVII";
    }else if(1701 <= decadeDigit && decadeDigit <= 1800){
       century = "XVIII";
    }else if(1801 <= decadeDigit && decadeDigit <= 1900){
       century = "XIX";
    }else if(1901 <= decadeDigit && decadeDigit <= 2000){
       century = "XX";
    }else if(2001 <= decadeDigit && decadeDigit <= 2100){
       century = "XXI";
    }
    return century;
 }

 function showPeriodCentury(century){
    var period_century = period[century];

    //if (type=="workDate"){
        if(century != "NS"){
            $("#period_select").text("Período: "+period_century); 
        }else{
            $("#period_select").text("");
        }
    /*} else{
        if(century != "NS"){
            $("#period_select_image").text("Período: "+period_century); 
        }else{
            $("#period_select_image").text("");
        }
    }*/
 }

function showPeriodCenturyImage(century){
    var period_century = period[century];

    if(century != "NS"){
            $("#period_select_image").text("Período: "+period_century); 
        }else{
            $("#period_select_image").text("");
        } 
 }

function filterDecadesOfCentury(century){
        var $decade = $('#decade_select');
        lcns = period[century] //|| []; 
        
        var decadeRange = decade[century]|| []; //alert(decadeRange);
        //alert('-lcns='+lcns+'arra'+lcns[0]+'decadR='+decadeRange);      
        period_text(century,lcns,"work");

        var i=0;
        var html = $.map(decadeRange, function(decRange){    
        i++;
        return option_value(decRange,i);

        }).join('');
        $decade.html(html);
}

function filterDecadesOfCenturyofImage(century){
      var $decade = $('#decade_select_image');
      var i=0;
        lcns = period[century] //|| []; 
        
        var decadeRange = decade[century]|| []; //alert(decadeRange);
        //alert('-lcns='+lcns+'arra'+lcns[0]+'decadR='+decadeRange);
        period_text(century,lcns,"image");    
        var html = $.map(decadeRange, function(decRange){    
        i++;
        return option_value(decRange,i);

        }).join('');
        $decade.html(html);
}

function period_text(century,period,type){
    if(century != "NS"){
        if(type == "work")
            $("#period_select").text("Período: "+lcns);
        else
            $("#period_select_image").text("Período: "+lcns);      
    }else{
        if(type == "work")
            $("#period_select").text("");
        else  
            $("#period_select_image").text("");
    }  
}

function option_value(decRange,i){
        if(century == "NS"){
            if(i==1){
              txtDecRange = '<option value="">' + decRange + '</option>';            
            }else if(i==2){
               txtDecRange = '<option value="BD">' + decRange + '</option>';
            }else {
               txtDecRange = '<option value="' + decRange + '">' + decRange + '</option>';
            }
        }else if(century == "Before"){
            txtDecRange = '<option value="BD">' + decRange + '</option>';
        }else{
            if(i==1){
              txtDecRange = '<option value="">' + decRange + '</option>';            
            }else {
              txtDecRange = '<option value="' + decRange + '">' + decRange + '</option>';
            }
        }
        
        return txtDecRange; 
}


function close_other_date(id) {
       var e = document.getElementById(id);
       
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';  

       resultSelectDateWork(e);
     }

function editDateWork(){
  var idDiv = "otherDate";   
  date_visibility(idDiv);
}

function editDateImage(){
  var idDiv = "date_img_inaccurate";   
  date_visibility(idDiv);
}

 function resultSelectDateWork(idArea){
      var idDateArea = $(idArea).attr("id");
      var century = "";
      var decade = "";
      var linkEditar = "";
      var result = "";
      if(idDateArea != "date_img_inaccurate"){
          century = $('#century').val();
          decade = $('#decade_select').val();
      }else{
          century = $('#century_image').val();
          decade = $('#decade_select_image').val();
      }  
            
      if(century != "NS" && century != "Before"){
        century = "Século: "+century;
      }else if(century == "Before"){
        century = "Antes do século XV e Anterior ao ano de 1401";
      }else{
        century = "";
      }

      if(century != "" && (decade != "" && decade != "BD")){
        decade = "e Década: "+decade;
      }else if(century == "" && (decade != "" && decade !="BD")){
        decade = "Década: "+decade;
      }else if(century == "" && decade == "BD"){
        decade = "Anterior ao ano de 1401";
      }else {
        decade = ""; 
      }

      if(idDateArea == "date_img_inaccurate"){
          //alert("imgh");
          if(century != "" || decade !=""){
              linkEditar = ' &nbsp;&nbsp;<a onclick="editDateImage()"; class="linkEdit">Editar</a>';
              result = century+" "+decade+" "+linkEditar;
          }            
          $("#answer_date_image").html(result);
      }else{ //otherDate
          //alert("work");
          if(century != "" || decade !=""){
              linkEditar = ' &nbsp;&nbsp;<a onclick="editDateWork()"; class="linkEdit">Editar</a>';
              result = century+" "+decade+" "+linkEditar;
          }            
          $("#answer_date").html(result);
      }            
      
 }    

function cleanToLoad(){
   $("#workDate").val("");
   $("#century").val("");
   $("#decade_select").val("");
   $("#answer_date").text("");
   $("#period_select").text("");
}

function closeArea(){
        var idDiv = "otherDate";
        var e = document.getElementById(idDiv);
        if(e.style.display == 'block'){
          e.style.display = 'none'; 
         } 
}

jQuery(function($) {
    var $period = $('#period_select');
    var $decade = $('#decade_select');
    var txtPeriod = '';
    
    $('#century').change(function () {
        $("#decade_select").val("");
        $("#workDate").val("");
        var century = $(this).val(); 
        filterDecadesOfCentury(century);        
    });

    
    //
    $('#workDate').change(function () {
        
        $("#answer_date").text("");
        $("#period_select").text("");        
        filterDecadesOfCentury("NS");       
        $("#century").val("");  
        closeArea();        
    });

    $('#decade_select').change(function () { 
        $("#workDate").val("");
       // $("#century").val("");
      //  $("#period_select").text("");

    });
    // Date Image
    $('#century_image').change(function () {
        $("#decade_select_img").val("");
        $("#datePickerImageDate").val("");
        var century = $(this).val();
        filterDecadesOfCenturyofImage(century);        
    });

    $("#datePickerImageDate").change(function () {         
        $("#answer_date_image").text("");
        $("#period_select_image").text("");        
        filterDecadesOfCenturyofImage("NS");       
        $("#century_image").val("");  
        //closeArea();        
    });

    $('#decade_select_image').change(function () { 
        $("#datePickerImageDate").val("");
    });
});