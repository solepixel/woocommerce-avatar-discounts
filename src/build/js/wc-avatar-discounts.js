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

			formData.append( name, fileData );
			formData.append( 'user_id', userID );

			showLoader();

			$.ajax({
				url: wcad_vars.ajax_url + '?action=wcad_ajax_file_upload',
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
						WoocommerceAvatarDiscounts.Manage.bindAvatars();
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
		init: init
	};
} ) ( jQuery );

jQuery( window ).on( 'load', WoocommerceAvatarDiscounts.Upload.init );
