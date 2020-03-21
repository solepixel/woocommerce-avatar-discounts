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
		bindDelete();
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

	bindDelete = function() {
		$( wrapperClass + ' a .delete-avatar' ).off( 'click' ).on( 'click', function( e ) {
			e.preventDefault();
			e.stopPropagation();

			if ( ! confirm( 'Are you sure you want to delete this Avatar?' ) ) {
				return false;
			}

			WoocommerceAvatarDiscounts.Upload.showLoader();

			var $el = $( this ).parents( 'a' ),
				id = $el.attr( 'data-avatar-id' ),
				userID = $( 'input[name="woocommerce_avatar_discounts_user"]' ).val(),
				$avatar_input = $( 'input[name="woocommerce_avatar_discounts_avatar"]' );

			if ( id == $avatar_input.val() ) {
				var $next = $( wrapperClass + ' a:not([data-avatar-id="' + id + '"])' ).first();
				if ( $next.length ) {
					setAvatar( $next.attr( 'data-avatar-id' ) );
				}
			}

			$.ajax({
				url: wcad_vars.ajax_url,
				type: 'post',
				data: { action: 'wcad_delete_avatar', avatar: id, user: userID },
				success: function( response ) {

					if ( response.success ) {
						$el.fadeOut( 'fast', function() {
							$el.remove();
							updateCount();
						});
					} else {
						alert( response.error );
					}
					WoocommerceAvatarDiscounts.Upload.hideLoader();
				}
			});
		});
	},

	/**
	 * Update Avatar Count
	 */
	updateCount = function() {
		var $count = $( 'b.badge-count' ),
			count = $( '.wc-ad-avatar-selection > a' ).length;
		$count.html( count ).attr( 'title', count + ' Avatars to choose from' );
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
		if ( $( 'body' ).hasClass( 'wp-admin' ) ) {
			return;
		}
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
		updateCount: updateCount
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.Manage.init );
