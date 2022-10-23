$(document).ready(function(){
	$('#newInstitutionalAlbum').on('click', function (e) {
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

