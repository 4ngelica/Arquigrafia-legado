$(document).ready(function() {
	$('#registrationInstitution form p.error').hide();
	$('#registrationInstitution form').submit(function (e) {
	 	e.preventDefault();
		var form = $(this);
		var data = form.serializeArray();
		var institution = form.find('#institution').val();
		$.post(baseUrl + '/users/institutionalLogin', data)
		.done(function(success) {
			if (success) {
				window.location.replace(baseUrl + '/institutions/' + institution);
			} else {
				form.find('p.error').show();
			}
			
		});
	});
});