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




	


});




