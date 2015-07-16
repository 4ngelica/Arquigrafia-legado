$(document).ready(function() {
	$('#registrationInstitution form p.error').hide();
	$('#registrationInstitution form').submit(function (e) {
	 	e.preventDefault();
		var form = $(this);
		var data = form.serializeArray();
		$.post('/users/institucionalLogin', data)
		.done(function(success) {
			if (success) {
				window.location.replace('/');
			} else {
				form.find('p.error').show();
			}
			
		});
	});
});