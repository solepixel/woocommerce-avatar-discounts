/**
 * WoocommerceAvatarDiscounts Manage Avatars JS Class
 *
 * @package WoocommerceAvatarDiscounts
 */

var WoocommerceAvatarDiscounts = window.WoocommerceAvatarDiscounts || {};

WoocommerceAvatarDiscounts.Manage = ( function( $ ) {
	'use strict';

	/** @type {string} Wrapper selector */
	var wrapperClass = '.wc-ad-manage-avatars',

	/**
	 * Initialize this class.
	 */
	init = function() {
		bindAvatars();
	},

	/**
	 * Bind Avatars to manage interface.
	 */
	bindAvatars = function() {
		$( wrapperClass + ' a' ).off( 'click' ).on( 'click', function( e ) {
			e.preventDefault();

			var id = $( this ).attr( 'data-avatar-id' );

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
		clearFeatured()
		$( wrapperClass + ' a[data-avatar-id="' + id + '"]' ).addClass( 'status-featured' ).blur();

		// Collapse interface.
		collapseAvatars();
	},

	/**
	 * Clear Featured Image status.
	 */
	clearFeatured = function() {
		$( wrapperClass + ' a.status-featured' ).removeClass( 'status-featured' );
	};

	return {
		init: init,
		clearFeatured: clearFeatured,
		collapseAvatars: collapseAvatars,
		bindAvatars: bindAvatars
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.Manage.init );
