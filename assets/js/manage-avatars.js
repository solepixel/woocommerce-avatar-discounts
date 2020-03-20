/**
 * WoocommerceAvatarDiscounts Manage Avatars JS Class
 *
 * @package WoocommerceAvatarDiscounts
 */

var WoocommerceAvatarDiscounts = window.WoocommerceAvatarDiscounts || {};

WoocommerceAvatarDiscounts.ManageAvatars = ( function( $ ) {
	'use strict';

	const init = function() {
		bind_avatars();
	},

	bind_avatars = function() {
		$( '.wc-ad-manage-avatars a' ).on( 'click', function( e ) {
			e.preventDefault();

			const id = $( this ).attr( 'data-avatar-id' );

			if ( ! is_expanded() ) {
				expand_avatars();
				return;
			}

			set_avatar( id );
		});
	},

	is_expanded = function() {
		return $( '.wc-ad-manage-avatars' ).hasClass( 'expanded' );
	},

	expand_avatars = function() {
		$( '.wc-ad-manage-avatars' ).addClass( 'expanded' );
	},

	collapse_avatars = function() {
		$( '.wc-ad-manage-avatars' ).removeClass( 'expanded' );
	},

	set_avatar = function( id ) {
		// Set hidden input to Avatar ID.
		$( 'input[name="woocommerce_avatar_discounts_avatar"]' ).val( id );

		// Change featured Avatar.
		$( '.wc-ad-manage-avatars a.status-featured' ).removeClass( 'status-featured' );
		$( '.wc-ad-manage-avatars a[data-avatar-id="' + id + '"]' ).addClass( 'status-featured' );

		// Collapse interface.
		collapse_avatars();
	};

	return {
		init: init
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.ManageAvatars.init );
