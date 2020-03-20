/**
 * WoocommerceAvatarDiscounts Manage Avatars JS Class
 *
 * @package WoocommerceAvatarDiscounts
 */

var WoocommerceAvatarDiscounts = window.WoocommerceAvatarDiscounts || {};

WoocommerceAvatarDiscounts.ManageAvatars = ( function( $ ) {
	'use strict';

	/** @type {string} Wrapper selector */
	const wrapper_class = '.wc-ad-manage-avatars',

	/**
	 * Initialize this class.
	 */
	init = function() {
		bind_avatars();
	},

	/**
	 * Bind Avatars to manage interface.
	 */
	bind_avatars = function() {
		$( wrapper_class + ' a' ).on( 'click', function( e ) {
			e.preventDefault();

			const id = $( this ).attr( 'data-avatar-id' );

			if ( ! is_expanded() ) {
				expand_avatars();
				return;
			}

			set_avatar( id );
		});
	},

	/**
	 * Check if wrapper element is already expanded.
	 *
	 * @return {Boolean}  If expanded or not.
	 */
	is_expanded = function() {
		return $( wrapper_class ).hasClass( 'expanded' );
	},

	/**
	 * Expand the avatars element.
	 */
	expand_avatars = function() {
		$( wrapper_class ).addClass( 'expanded' );
	},

	/**
	 * Collapse the avatars element.
	 */
	collapse_avatars = function() {
		$( wrapper_class ).removeClass( 'expanded' );
	},

	/**
	 * Set the new avatar ID.
	 *
	 * @param {int} id  The avatar ID.
	 */
	set_avatar = function( id ) {
		// Set hidden input to Avatar ID.
		$( 'input[name="woocommerce_avatar_discounts_avatar"]' ).val( id );

		// Change featured Avatar.
		$( wrapper_class + ' a.status-featured' ).removeClass( 'status-featured' );
		$( wrapper_class + ' a[data-avatar-id="' + id + '"]' ).addClass( 'status-featured' );

		// Collapse interface.
		collapse_avatars();
	};

	return {
		init: init
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.ManageAvatars.init );
