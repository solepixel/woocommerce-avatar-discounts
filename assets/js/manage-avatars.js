/**
 * WoocommerceAvatarDiscounts Manage Avatars JS Class
 *
 * TODO: Compile/Minify with Task runner.
 *
 * @package WoocommerceAvatarDiscounts
 */

var WoocommerceAvatarDiscounts = window.WoocommerceAvatarDiscounts || {};

WoocommerceAvatarDiscounts.ManageAvatars = ( function( $ ) {
	'use strict';

	/** @type {string} Wrapper selector */
	const wrapperClass = '.wc-ad-manage-avatars',

	/**
	 * Initialize this class.
	 */
	init = function() {
		bindAvatars();
		handleUpload();
	},

	/**
	 * Bind Avatars to manage interface.
	 */
	bindAvatars = function() {
		$( wrapperClass + ' a' ).on( 'click', function( e ) {
			e.preventDefault();

			const id = $( this ).attr( 'data-avatar-id' );

			if ( ! isExpanded() ) {
				$( this ).blur();
				expandAvatars();
				return;
			}

			setAvatar( id );
		});
	},

	/**
	 * Check if wrapper element is already expanded.
	 *
	 * @return {Boolean}  If expanded or not.
	 */
	isExpanded = function() {
		// Always return true for wp-admin.
		if ( $( 'body' ).hasClass( 'wp-admin' ) ) {
			return true;
		}
		return $( wrapperClass ).hasClass( 'expanded' );
	},

	/**
	 * Expand the avatars element.
	 */
	expandAvatars = function() {
		$( wrapperClass ).addClass( 'expanded' );
	},

	/**
	 * Collapse the avatars element.
	 */
	collapseAvatars = function() {
		$( wrapperClass ).removeClass( 'expanded' );
	},

	/**
	 * Set the new avatar ID.
	 *
	 * @param {int} id  The avatar ID.
	 */
	setAvatar = function( id ) {
		// Set hidden input to Avatar ID.
		$( 'input[name="woocommerce_avatar_discounts_avatar"]' ).val( id );

		// Change featured Avatar.
		$( wrapperClass + ' a.status-featured' ).removeClass( 'status-featured' );
		$( wrapperClass + ' a[data-avatar-id="' + id + '"]' ).addClass( 'status-featured' ).blur();

		// Collapse interface.
		collapseAvatars();
	},

	handleUpload = function() {
		$( '.wc-ad-upload' ).on( 'change', function() {
			const $input = $( this ),
				$display = $( '.wc-ad-file-display' ).html( extractFilename( $input.val() ) );

		});
	},

	extractFilename = function( fullPath ) {
		if ( fullPath ) {
		    var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/')),
		    	filename = fullPath.substring(startIndex);

		    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
		        filename = filename.substring(1);
		    }
		    return filename;
		}
	};

	return {
		init: init
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.ManageAvatars.init );
