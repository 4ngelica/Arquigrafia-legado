$(document).ready(function() {
	$('#registrationStoa form p.error').hide();
	$('#registrationStoa form').submit(function (e) {
	 	e.preventDefault();
		var form = $(this);
		// var nusp = form.find('#stoa_account').val();
		// var password = $(this).find('#password').val();
		var data = form.serializeArray();
		$.post('/users/stoaLogin', data)
		.done(function(success) {
			if (success) {
				window.location.replace('/');
			} else {
				form.find('p.error').show();
			}
			
		});
	});
});