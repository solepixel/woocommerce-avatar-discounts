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
	init = function() {
		handleUpload();
	},

	/**
	 * Upload file to server via AJAX.
	 */
	handleUpload = function() {
		$( '.wc-ad-upload' ).on( 'change', function() {

		});
	};

	return {
		init: init
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.Upload.init );
