$(document).ready(function(){

    // Override append function to handle dynamic dom additions using triggers.
    (function($) {
      var origAppend = $.fn.append;

      $.fn.append = function () {
          return origAppend.apply(this, arguments).trigger("append");
      };
    })(jQuery);

    // Handle contact form submission
    $('#url_form').on('submit', function(e) {
        e.preventDefault();  //prevent form from submitting

        var form_button = $('#form_save');
        form_button.prop('disabled', true);

        var form = $(this);
        var error = {};
    		var url = $('#form_url').val().trim();

        // Validations
    		if (url == '' || url == null || !isURL(url)) {
    			$('#form_url').addClass('invalid');
    			$('#url_error').show();
    			error['url'] = true;
    		} else {
    			$('#url').removeClass('invalid');
    			$('#url_error').hide();
    			delete error['url'];
    		}

        // Submit using ajax if validation is successful
    		if (Object.keys(error).length == 0) {
              var formSerialize = form.serialize();

              $.post('/shorten', formSerialize, function(response) {
                  //your callback here
                  if (response != null && response.hasOwnProperty('status')) {
                    if (response.status === 'success') {
                      $('#recent').prepend(response.html);
                    } else {
                      $('.form-message').text(response.message).show().addClass('red');
                    }
                  } else {
                    $('.form-message').text('Something went wrong, go back and try again!').show().addClass('red');
                  }
              }, 'JSON');
  		  }

  		 form_button.prop('disabled', false);
    });
});


function addNewURL(short_url, destination_url) {
	var html = '<div class="url_card"><a class="from_url" target="_blank" href="'+short_url+'">'+short_url+'</a><div class="to_url">'+destination_url+'</div></div>';
	$('#recent').prepend(html);
}


//function to validate urls at the front end. Ideally would use a library like https://www.npmjs.com/package/valid-url
function isURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return pattern.test(str);
}
