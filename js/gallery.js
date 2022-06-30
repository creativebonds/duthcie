/*
 * A custom function that checks if element is in array, we'll need it later
 */
function in_array(el, arr) {
	for(var i in arr) {
		if(arr[i] == el) return true;
	}
	return false;
}

jQuery( function( $ ) {
	/*
	 * Sortable images
	 */
	$('ul.product_gallery_gallery_mtb').sortable({
		items:'li',
		cursor:'-webkit-grabbing', /* mouse cursor */
		scrollSensitivity:40,
		/*
		You can set your custom CSS styles while this element is dragging
		start:function(event,ui){
			ui.item.css({'background-color':'grey'});
		},
		*/
		stop:function(event,ui){
			ui.item.removeAttr('style');

			var sort = new Array(), /* array of image IDs */
			    gallery = $(this); /* ul.product_gallery_gallery_mtb */

			/* each time after dragging we resort our array */
			gallery.find('li').each(function(index){
				sort.push( $(this).attr('data-id') );
			});
			/* add the array value to the hidden input field */
			gallery.parent().next().val( sort.join() );
			/* console.log(sort); */
		}
	});
	/*
	 * Multiple images uploader
	 */
	$('.product_gallery_upload_gallery_button').click( function(e){ /* on button click*/
		e.preventDefault();

		var button = $(this),
		    hiddenfield = button.prev(),
		    hiddenfieldvalue = hiddenfield.val().split(","), /* the array of added image IDs */
	    	    custom_uploader = wp.media({
			title: 'Insert images', /* popup title */
			library : {type : 'image'},
			button: {text: 'Use these images'}, /* "Insert" button text */
			multiple: true
		    }).on('select', function() {

			var attachments = custom_uploader.state().get('selection').map(function( a ) {
				a.toJSON();
            			return a;
			}),
			thesamepicture = false,
			i;

			/* loop through all the images */
          		for (i = 0; i < attachments.length; ++i) {

				/* if you don't want the same images to be added multiple time */
				if( !in_array( attachments[i].id, hiddenfieldvalue ) ) {
					
					/* add HTML element with an image */
					$('ul.product_gallery_gallery_mtb').append('<li data-id="' + attachments[i].id + '"><span style="background-image:url(' + attachments[i].attributes.url + ');width: 50px;height: 50px;display: block;background-position: center;background-size: contain;"></span><a href="#" class="product_gallery_gallery_remove">&times;</a></li>');
					/* add an image ID to the array of all images */
					hiddenfieldvalue.push( attachments[i].id );
				} else {
					thesamepicture = true;
				}
          		}
			/* refresh sortable */
			$( "ul.product_gallery_gallery_mtb" ).sortable( "refresh" );
			/* add the IDs to the hidden field value */
			hiddenfield.val( hiddenfieldvalue.join() );
			/* you can print a message for users if you want to let you know about the same images */
			if( thesamepicture == true ) alert('The same images are not allowed.');
		}).open();
	});

	/*
	 * Remove certain images
	 */
	$('body').on('click', '.product_gallery_gallery_remove', function(){
		var id = $(this).parent().attr('data-id'),
		    gallery = $(this).parent().parent(),
		    hiddenfield = gallery.parent().next(),
		    hiddenfieldvalue = hiddenfield.val().split(","),
		    i = hiddenfieldvalue.indexOf(id);

		$(this).parent().remove();

		/* remove certain array element */
		if(i != -1) {
			hiddenfieldvalue.splice(i, 1);
		}

		/* add the IDs to the hidden field value */
		hiddenfield.val( hiddenfieldvalue.join() );

		/* refresh sortable */
		gallery.sortable( "refresh" );

		return false;
	});
	/*
	 * Selected item
	 */
	$('body').on('mousedown', 'ul.product_gallery_gallery_mtb li', function(){
		var el = $(this);
		el.parent().find('li').removeClass('product_gallery-active');
		el.addClass('product_gallery-active');
	});
});