jQuery(document).ready(function($) {
	function httpGet(theUrl)
	{
	    var xmlHttp = null;

	    xmlHttp = new XMLHttpRequest();
	    xmlHttp.open( "GET", theUrl, false );
	    xmlHttp.send( null );
	    return xmlHttp.responseText;
	}

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
			var valid = httpGet('https://graph.facebook.com/' + $('#fb_id').val() + '?fields=roles&access_token=' + $('#fb_id').val() + '|' + $('#fb_secret').val());
			if (valid.indexOf("error") !=-1) {
				$('#info3').css('color', '#ff0000');
			    $('#info3').html('Facebook App ID & Facebook Secret Does Not Match.');
			} else {
				$('#info3').css('color', '#00ff00');
				$('#fb_active').removeAttr('disabled');
				$('#info3').html('Facebook App ID & Facebook Secret Match');
			}
		} else {
			$('#fb_active').removeAttr('checked').attr('disabled','disabled');
			$('#info3').html('');
		}		
	}
	validate();
	$('#fb_id').bind('change paste keyup',function() {
		validate();
	});

	$('#fb_secret').bind('change paste keyup',function() {
		validate();
	});
});