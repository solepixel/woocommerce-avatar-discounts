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

?>
<a href="#avatar" class="status-<?php echo esc_attr( $avatar->status ); ?>" data-avatar-id="<?php echo esc_attr( $avatar->id ); ?>">
	<!--
		These buttons would be hidden with CSS until the parent
		element has "expanded" class. Then they can be bound to
		controls to edit/delete avatars.
	-->
	<!--<button class="edit-avatar">Edit</button>-->
	<button class="delete-avatar" title="<?php echo esc_attr__( 'Delete', 'woocommerce-avatar-discounts' ); ?>">&times;</button>
	<img src="<?php echo esc_attr( $avatar->url ); ?>">
</a>
