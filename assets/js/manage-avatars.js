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
		$( '.wc-ad-manage-avatars' ).hasClass( 'expanded' );
	},

	expand_avatars = function() {
		$( '.wc-ad-manage-avatars' ).addClass( 'expanded' );
	},

	collapse_avatars = function() {
		$( '.wc-ad-manage-avatars' ).removeClass( 'expanded' );
	},

	set_avatar = function( id ) {
		// TODO: Set hidden input value to this id.
		// TODO: Change class from status-active to status-featured.
		// TODO: Change URL from #select-avatar to #manage-avatars
		collapse_avatars();
	};

	return {
		init: init
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.ManageAvatars.init );
