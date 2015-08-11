jQuery(document).ready(function(){
	jQuery('#ajax_formInstitutional').submit(function(){
		var dados = jQuery( this ).serialize();
 
		jQuery.ajax({
			type: "POST",
			url: "photos/savePhotoInstitutional",
			data: dados,
			success: function( data )
			{
				alert( "ok" );
			}
			}); alert("ppp");
				return false;
	});
});


window.onload = function () {

	/*document.getElementById('btnYes').onclick = function () {
         //document.getElementById('modal').style.display = "none"
          window.location.replace('#');
           $(this).dialog("close"); 
    };
    document.getElementById('btnNo').onclick = function () {
       window.location.replace('/photos/');
    };*/ 
    
    $( "#dialog-confirm" ).html("confirm");
			 $( "#dialog-confirm" ).dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
				"Sim": function() {
					$( this ).dialog( "close" );
					},
				"Não": function() {
						$( this ).dialog( "close" );
					}
				}
			});
}; 


$(document).ready(function(){

	
	$('#newInstitutionalAlbum').live('click', function (e) {
			e.preventDefault();
			$('#mask').fadeIn('fast');
			$('#form_repopulate_window').fadeIn('slow');
	});

	$('#formRepopulate form p.error').hide();
	$('#formRepopulate form').submit(function (e) {
	 	e.preventDefault();
		var form = $(this);
		var data = form.serializeArray();
		$.post('/albums/institutionalAlbum', data)
		.done(function(success) {
			if (success) {
				window.location.replace('/');
			} else {
				form.find('p.error').show();
			}
			
		});
	});




	/*function fnOpenNormalDialog(){
		alert("ok");
		document.forms["formInstitutional"].submit();

		$("#dialog-confirm").html("Deseja utilizar a informação do formulario anterior");

		$("#dialog-confirm").dialog({
			resizable:false,
			modal:true,
			title:"Modal",
			height:250,
			width:400,
			buttons:{
				"Yes": function(){
					$(this).dialog('close');
					//callback(true);
					//window.location.replace('/photos/savePhotoInstitutional');
					document.forms["formInstitutional"].submit();
				},
				"No": function(){
					$(this).dialog('close');
					alert("show");

				}
			}
		})
	} */
	
	//$("#btnOpenDialogRepopulate").click(fnOpenNormalDialog);

	/*$("#btnOpenDialogRepopulate").click(function(e) {
		alert("ddd");
                e.preventDefault();
                $("#dialog-confirm").dialog({
				resizable:false,
				modal:true,
				title:"Modal",
				height:250,
				width:400,
				buttons:{
				"Yes": function(){
					$(this).dialog('close');
					alert("hi");
					//callback(true);
					//window.location.replace('/photos/savePhotoInstitutional');
					//document.forms["formInstitutional"].submit();
				},
				"No": function(){
					$(this).dialog('close');
					alert("show");

				}
			}
		})
    });*/

	 
	/*function callback(value){
		if(value){

		}else{

		}
	}*/

});

$(function() {
		$( "#btnOpenDialogRepopulate" ).dialog({
		resizable: false,
		height:140,
		modal: true,
		buttons: {
			"Delete all items": function() {
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
			}
		});
	});


