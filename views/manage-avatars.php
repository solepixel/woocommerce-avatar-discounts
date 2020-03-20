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

if ( empty( $avatars ) ) {
	return;
}

$encourage_text = woocommerce_avatar_discounts()->admin_settings()->get_setting( 'encourage_text' );
$selected       = '';

?>
<div class="wc-ad-manage-avatars">
	<?php if ( $encourage_text && ! is_admin() ) : ?>
		<p class="encourage-text"><?php echo esc_html( $encourage_text ); ?></p>
	<?php endif; ?>
	<?php
	foreach ( $avatars as $index => $avatar ) :
		if ( 'deleted' === $avatar['status'] ) :
			continue;
		endif;

		$avatar['id'] = $index; // TODO: Remove temorary ID.

		if ( 'featured' === $avatar['status'] ) {
			$selected = $avatar['id'];
		}
		?>
		<a href="#avatar" class="status-<?php echo esc_attr( $avatar['status'] ); ?>" data-avatar-id="<?php echo esc_attr( $avatar['id'] ); ?>">
			<!--<button class="edit-avatar">Edit</button>-->
			<img src="<?php echo esc_attr( $avatar['url'] ); ?>">
		</a>
	<?php endforeach; ?>
	<div class="upload-avatar">
		<button>Upload New Avatar</button>
	</div>
	<input type="hidden" name="woocommerce_avatar_discounts_avatar" value="<?php echo esc_attr( $selected ); ?>">
</div>
