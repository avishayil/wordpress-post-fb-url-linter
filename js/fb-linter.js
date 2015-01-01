jQuery(document).ready(function($) {
	function validate() {
		if ($('#fb_id').val().length != 15) {
			$('#info1').html('Facebook App ID must contain 15 Characters');
		} else {
			$('#info1').html('');
		}
		if ($('#fb_secret').val().length != 32) {
			$('#info2').html('Facebook App Secret must contain 32 Characters');	
		} else {
			$('#info2').html('');
		}
		if (($('#fb_id').val().length == 15) && ($('#fb_secret').val().length == 32)) {
			$('#fb_active').removeAttr('disabled');
			$('input[type="submit"]').removeAttr('disabled');
		} else {
			$('#fb_active').removeAttr('checked').attr('disabled','disabled');
			$('input[type="submit"]').attr('disabled','disabled');
		}
	}

	$('#fb_id').bind('change paste keyup',function() {
		validate();
	});

	$('#fb_secret').bind('change paste keyup',function() {
		validate();
	});
});