( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var languageTabs = document.querySelectorAll( '[data-smm-language]' );
		var languagePanels = document.querySelectorAll( '[data-smm-language-panel]' );
		var selectButton = document.getElementById( 'smm-select-image' );
		var removeButton = document.getElementById( 'smm-remove-image' );
		var imageIdInput = document.getElementById( 'smm-image-id' );
		var preview = document.getElementById( 'smm-image-preview' );
		var mediaFrame;

		languageTabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				var language = tab.getAttribute( 'data-smm-language' );
				languageTabs.forEach( function ( candidate ) {
					var isActive = candidate === tab;
					candidate.classList.toggle( 'is-active', isActive );
					candidate.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				} );
				languagePanels.forEach( function ( panel ) {
					var isActive = panel.getAttribute( 'data-smm-language-panel' ) === language;
					panel.classList.toggle( 'is-active', isActive );
					panel.hidden = ! isActive;
				} );
			} );
		} );

		if ( ! selectButton || ! imageIdInput || ! preview || ! window.wp || ! window.wp.media ) {
			return;
		}

		selectButton.addEventListener( 'click', function () {
			if ( mediaFrame ) {
				mediaFrame.open();
				return;
			}

			mediaFrame = window.wp.media( {
				title: 'Choose dish photo',
				button: { text: 'Use this photo' },
				library: { type: 'image' },
				multiple: false
			} );

			mediaFrame.on( 'select', function () {
				var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
				var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
				imageIdInput.value = attachment.id;
				preview.innerHTML = '<img src="' + imageUrl + '" alt="">';
				if ( removeButton ) {
					removeButton.hidden = false;
				}
			} );

			mediaFrame.open();
		} );

		if ( removeButton ) {
			removeButton.addEventListener( 'click', function () {
				imageIdInput.value = '';
				preview.innerHTML = '<span class="dashicons dashicons-format-image" aria-hidden="true"></span><p>No photo selected</p>';
				removeButton.hidden = true;
			} );
		}
	} );
}() );
