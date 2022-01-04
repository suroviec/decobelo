jQuery(function($){

	// on upload button click
	$('body').on( 'click', '.misha-upl', function(e){

		e.preventDefault();

		var button = $(this),
		custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Dodaj wybrane zdjęcie'
			},
			multiple: true
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
		}).open();
	
	});

	// on remove button click
	$('body').on('click', '.misha-rmv', function(e){
		e.preventDefault();
		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.hide().prev().html('<a class="button">Upload image</a>');
	});

});