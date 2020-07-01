<?php
/**
 * Main plugin class.
 *
 * @package Newspack
 */

namespace Newspack_Teams_For_WC_Memberships_Auto_Join_By_Email;

/**
 * Newspack Teams for WooCommerce Memberships Auto-Join by Email main Plugin class.
 */
class Plugin {

	/**
	 * @var Plugin|null
	 */
	private static $instance;

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		// Only register if Teams for WooComm Memberships is active.
		if ( ! is_plugin_active( 'woocommerce-memberships-for-teams/woocommerce-memberships-for-teams.php' ) ) {
			return;
		}

		$this->register_logic();
		$this->register_interface();
	}

	/**
	 * Singleton get.
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Adds the plugin's logic.
	 */
	private function register_logic() {
		add_action( 'init', function() {
			add_action( 'woocommerce_created_customer', [ $this, 'auto_add_user_to_team_by_email' ] );
		} );
	}

	/**
	 * Defines the interface -- adds a new section under WooCommerce > Settings > Memberships.
	 */
	private function register_interface() {
		add_filter( 'woocommerce_get_sections_memberships', [ $this, 'add_section' ] );
		add_filter( 'woocommerce_get_settings_memberships', [ $this, 'get_settings' ], 10, 2 );
	}

	/**
	 * Automatically assigns a newly registered User/Customer to existing Team Memberships based on their email -- if the new
	 * User's email domain is the same as an existing Team Membership Owner's, the new user will be auto-assigned to that
	 * Team Membership when they register. A list of excluded/ignored email domains is configurable, and specified email domains
	 * get to be excluded from this logic, and they are not auto-assigned to a Team (e.g. @gmail.com, etc.).
	 *
	 * @param int $customer_id User ID.
	 */
	public function auto_add_user_to_team_by_email( $customer_id ) {
		$current_user = $customer_id ? get_user_by( 'ID', $customer_id ) : null;
		if ( ! $current_user ) {
			return;
		}

		$teams                     = $this->get_all_teams();
		$current_user_email        = $current_user->user_email;
		$current_user_email_domain = $this->get_domain_from_email( $current_user_email );

		// Compare if any of the Team Membership Owners has the same email domain as this users.
		foreach ( $teams as $team ) {
			$team_owner              = $team->get_owner();
			$team_owner_email_domain = $this->get_domain_from_email( $team_owner->get( 'user_email' ) );

			// Check if Team Owner's email matches this User's email.
			if ( strtolower( $team_owner_email_domain ) == strtolower( $current_user_email_domain ) ) {
				if ( $this->is_domain_excluded( $team_owner_email_domain ) ) {
					continue;
				}

				try {
					$team->add_member( $current_user );
				} catch ( \Exception $e ) {
				}
			}
		}
	}

	/**
	 * Fetches all WC Team Memberships.
	 *
	 * @return array
	 */
	private function get_all_teams() {
		$teams = array();

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wc_memberships_team',
		);
		$query = new \WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return $teams;
		}

		$teams_ids = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$teams_ids[] = get_the_ID();
		}
		if ( empty( $teams_ids ) ) {
			return $teams;
		}

		foreach ( $teams_ids as $team_id ) {
			$teams[] = wc_memberships_for_teams_get_team( $team_id );
		}

		return $teams;
	}

	/**
	 * Checks if a domain is listed in excluded/ignored emails.
	 *
	 * @param string $domain
	 *
	 * @return bool
	 */
	private function is_domain_excluded( $domain ) {
		$excluded_domains = $this->get_excluded_email_domains();

		foreach ( $excluded_domains as $excluded_domain ) {
			$pattern = '|^'. str_replace( '\*', '.*', preg_quote( $excluded_domain ) ) .'$|is';
			if ( (bool) preg_match( $pattern, $domain ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the domain from an email.
	 *
	 * @param string $email
	 *
	 * @return string
	 */
	private function get_domain_from_email( $email ) {
		$pos_at = strpos( $email, '@' );
		if ( false === $pos_at || ( $pos_at + 1 ) >= strlen( $email ) ) {
			return false;
		}

		return substr( $email, $pos_at + 1 );
	}

	/**
	 * Gets a list of all the email domains to ignore when automatically adding newly registered users to existing Teams.
	 *
	 * @return array
	 */
	private function get_excluded_email_domains() {
		$all_options            = get_option( 'wc_team_memberships_auto_join_by_email', '' );
		$excluded_email_domains = isset( $all_options[ 'excluded_email_domains' ] ) && ! empty( $all_options[ 'excluded_email_domains' ] )
			? explode( ',', $all_options[ 'excluded_email_domains' ] )
			: [];

		return $excluded_email_domains;
	}

	/**
	 * Sets interface -- creates a section beneath the Memberships tab.
	 *
	 * @param $sections
	 *
	 * @return array
	 */
	public function add_section( $sections ) {
		$sections[ 'newspack_teams_for_wc_memberships_auto_join_by_email' ] = __(
			'Teams Auto-Join by Email',
			'newspack_teams_for_wc_memberships_auto_join_by_email'
		);
		return $sections;
	}

	/**
	 * Sets interface -- adds settings to the new page.
	 *
	 * @param $settings
	 * @param $current_section
	 *
	 * @return array
	 */
	public function get_settings( $settings, $current_section ) {
		if ( $current_section !== 'newspack_teams_for_wc_memberships_auto_join_by_email' ) {
			return $settings;
		}

		$settings_custom   = [];
		$settings_custom[] = [
			'id'   => 'newspack_teams_for_wc_memberships_auto_join_by_email',
			'name' => __(
				'Auto-Join Team Memberships by Email Settings',
				'newspack_teams_for_wc_memberships_auto_join_by_email'
			),
			'type' => 'title',
			'desc' => __(
				'The following options are used to configure Teams Auto-Join by Email',
				'newspack_teams_for_wc_memberships_auto_join_by_email'
			),
		];
		$settings_custom[] = [
			'type'     => 'textarea',
			'id'       => 'wc_team_memberships_auto_join_by_email[excluded_email_domains]',
			'class'    => 'input-text wide-input messages-group-posts',
			'name'     => __(
				'Excluded email domains',
				'newspack_teams_for_wc_memberships_auto_join_by_email'
			),
			'desc'     => __(
				'Coma Separated Vaules of email domains which will be ignored when adding new Users to existing Team Memberships. The `*` quantifier is allowed.',
				'newspack_teams_for_wc_memberships_auto_join_by_email'
			),
			'css'      => 'min-width: 300px; height: 300px;',
			'desc_tip' => __(
				'Enter CSV values of email domains which will be ignored when new Users get added to Team Memberships based on their email domains. For example, setting \'gmail.com,yahoo.*\' as a value, will ignore new Users which have emails ending witn @gmail.com or any of the @yahoo.* extensions, and will not add those Users to Team Memberships',
				'newspack_teams_for_wc_memberships_auto_join_by_email'
			),
		];

		$settings_custom[] = [
			'type' => 'sectionend',
			'id'   => 'newspack_teams_for_wc_memberships_auto_join_by_email'
		];

		return $settings_custom;
	}
}
