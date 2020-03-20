<?php
/**
 * WooCommerce Avatar Discounts
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@woocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Avatar Discounts
 *  to newer versions in the future.
 *
 * @author  Brian DiChiara
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( empty( $avatars ) && ! $encourage_text ) {
	return;
}

?>
<div class="wc-ad-manage-avatars<?php echo esc_attr( $classname ); ?>">
	<?php if ( $encourage_text && ! is_admin() ) : ?>
		<h4 class="encourage-text"><?php echo esc_html( $encourage_text ); ?></h4>
	<?php endif; ?>
	<?php if ( ! empty( $avatars ) ) : ?>
		<div class="wc-ad-avatar-selection">
			<?php if ( $badge_title ) : ?>
				<b class="badge-count" title="<?php echo esc_attr( $badge_title ); ?>"><?php echo esc_html( $count ); ?></b>
			<?php endif; ?>
			<?php
			foreach ( $avatars as $avatar ) :
				// TODO: Set selected in class based on database data.
				if ( 'featured' === $avatar->status ) {
					$selected = $avatar->id;
				}
				?>
				<a href="#avatar" class="status-<?php echo esc_attr( $avatar->status ); ?>" data-avatar-id="<?php echo esc_attr( $avatar->id ); ?>">
					<!--
						These buttons would be hidden with CSS until the parent
						element has "expanded" class. Then they can be bound to
						controls to edit/delete avatars.
					-->
					<!--<button class="edit-avatar">Edit</button>-->
					<!--<button class="delete-avatar">Delete</button>-->
					<img src="<?php echo esc_attr( $avatar->url ); ?>">
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div class="upload-avatar">
		<button class="upload-button">
			<?php echo esc_html( $button_text ); ?>
			<input type="file" class="wc-ad-upload" name="woocommerce_avatar_discounts_upload">
		</button>
		<span class="wc-ad-file-display"></span>
	</div>
	<input type="hidden" name="woocommerce_avatar_discounts_avatar" value="<?php echo esc_attr( $selected ); ?>">
</div>
