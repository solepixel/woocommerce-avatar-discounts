/**
 * WoocommerceAvatarDiscounts Upload Avatars JS Class
 *
 * @package WoocommerceAvatarDiscounts
 */

var WoocommerceAvatarDiscounts = window.WoocommerceAvatarDiscounts || {};

WoocommerceAvatarDiscounts.Upload = ( function( $ ) {
	'use strict';

	/**
	 * Initialize this class.
	 */
	var init = function() {
		handleUpload();
	},

	/**
	 * Get filename from fake upload path.
	 *
	 * @param {string} fullPath  Full path.
	 *
	 * @return {string}  The file name.
	 */
	extractFilename = function( fullPath ) {
		if ( fullPath ) {
		    var startIndex = ( fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/') ),
		    	filename = fullPath.substring( startIndex );

		    if ( filename.indexOf( '\\' ) === 0 || filename.indexOf('/') === 0) {
		        filename = filename.substring(1);
		    }
		    return filename;
		}
	},

	/**
	 * Upload file to server via AJAX.
	 */
	handleUpload = function() {
		$( '.wc-ad-upload' ).on( 'change', function() {

			/**
			 * Display upload filename after selecting file.
			 */
			var $input = $( this );

			$( '.wc-ad-file-display' ).html( extractFilename( $input.val() ) );

			var name = $input.attr( 'name' ),
				fileData = $input.prop( 'files' )[0],
				formData = new FormData(),
				userID = $( 'input[name="woocommerce_avatar_discounts_user"]' ).val();

			if ( ! fileData ){
				return;
			}

			formData.append( name, fileData );
			formData.append( 'user', userID );

			showLoader();

			$.ajax({
				url: wcad_vars.ajax_url + '?action=wcad_upload_avatar',
				dataType: 'text',
				cache: false,
				contentType: false,
				processData: false,
				type: 'post',
				data: formData,
				success: function( response ) {
					response = JSON.parse( response );

					// Reset input.
					$( '.wc-ad-file-display' ).html( '' );
					$input.val();

					if ( response.success ) {
						WoocommerceAvatarDiscounts.Manage.clearFeatured();
						$( '.wc-ad-avatar-selection' ).append( response.html );
						WoocommerceAvatarDiscounts.Manage.init();
						WoocommerceAvatarDiscounts.Manage.updateCount();
						WoocommerceAvatarDiscounts.Manage.collapseAvatars();
					} else {
						alert( response.error );
					}
					hideLoader();
				}
			});
		});
	},

	/**
	 * Show a loading spinner
	 */
	showLoader = function() {
		var $loader = $( '.wcad-loader' );
		if ( ! $loader.length ) {
			$loader = $( '<div />' ).addClass( 'wcad-loader' );
			var $spinner = $( '<span />' ).addClass( 'spinner' );
			$loader.append( $spinner );
			$( '.wc-ad-manage-avatars' ).prepend( $loader );
		}
		$loader.addClass( 'active' );
	},

	/**
	 * Hide the loading spinner
	 */
	hideLoader = function() {
		$( '.wcad-loader' ).removeClass( 'active' );
	};

	return {
		init: init,
		showLoader: showLoader,
		hideLoader: hideLoader
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.Upload.init );
