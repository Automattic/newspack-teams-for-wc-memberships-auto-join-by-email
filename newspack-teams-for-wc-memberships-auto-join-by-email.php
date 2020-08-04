<?php
/**
 * Plugin Name: Newspack Teams for WooCommerce Memberships Auto-Join by Email
 * Description: When a new user registers, the plugin automatically assigns them to corresponding WooComm Team Memberships based on their email domain.
 * Version: 0.4
 * Author: Automattic
 * Author URI: https://newspack.blog/
 * License: GPL2
 * Text Domain: newspack_teams_for_wc_memberships_auto_join_by_email
 *
 * @package Newspack_Teams_For_WC_Memberships_Auto_Join_By_Email
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'NEWSPACK_TEAMS_FOR_WC_MEMBERSHIPS_AUTO_JOIN_BY_EMAIL_FILE' ) ) {
	define( 'NEWSPACK_TEAMS_FOR_WC_MEMBERSHIPS_AUTO_JOIN_BY_EMAIL_FILE', __FILE__ );
}

require_once ( dirname( NEWSPACK_TEAMS_FOR_WC_MEMBERSHIPS_AUTO_JOIN_BY_EMAIL_FILE ) . '/plugin/class-plugin.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

\Newspack_Teams_For_WC_Memberships_Auto_Join_By_Email\Plugin::get_instance();
