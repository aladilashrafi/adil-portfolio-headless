<?php
/**
 * Plugin Name:       Adil Portfolio — Headless CMS
 * Plugin URI:        https://api.adilashrafi.com
 * Description:       Purpose-built headless WordPress plugin that powers the Next.js portfolio at adilashrafi.com. Registers CPTs, meta fields, REST API endpoints, CORS headers, contact form handling, auto-revalidation, and admin UI.
 * Version:           1.2.0
 * Author:            Al Adil Ashrafi
 * Author URI:        https://adilashrafi.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Text Domain:       adil-portfolio-headless
 */

defined( 'ABSPATH' ) || exit;

// ── Constants ─────────────────────────────────────────────────────────────────
define( 'ADIL_VERSION',     '1.2.0' );
define( 'ADIL_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'ADIL_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'ADIL_PLUGIN_FILE', __FILE__ );
define( 'ADIL_API_NS',      'adil/v1' );
define( 'ADIL_SITE_URL',    'https://api.adilashrafi.com' );
define( 'ADIL_FRONTEND_URL', get_option( 'adil_frontend_url', 'https://adilashrafi.com' ) );

// ── Autoload includes ─────────────────────────────────────────────────────────
$includes = [
    'class-post-types.php',
    'class-meta-fields.php',
    'class-settings.php',
    'class-cors.php',
    'class-contact-log.php',
    'class-contact-handler.php',
    'class-rest-api.php',
];

foreach ( $includes as $file ) {
    require_once ADIL_PLUGIN_DIR . 'includes/' . $file;
}

require_once ADIL_PLUGIN_DIR . 'admin/class-admin-ui.php';

// ── Bootstrap ─────────────────────────────────────────────────────────────────
function adil_bootstrap(): void {
    Adil_Post_Types::init();
    Adil_Meta_Fields::init();
    Adil_Settings::init();
    Adil_CORS::init();
    Adil_Contact_Log::init();
    Adil_REST_API::init();
    Adil_Admin_UI::init();
}
add_action( 'plugins_loaded', 'adil_bootstrap' );

// ── Activation hook ───────────────────────────────────────────────────────────
register_activation_hook( __FILE__, 'adil_activate' );
function adil_activate(): void {
    Adil_Post_Types::register_all();
    flush_rewrite_rules();

    // Generate revalidation token if not already set
    if ( ! get_option( 'adil_revalidate_token' ) ) {
        update_option( 'adil_revalidate_token', wp_generate_password( 48, false ) );
    }
}

// ── Deactivation hook ─────────────────────────────────────────────────────────
register_deactivation_hook( __FILE__, 'adil_deactivate' );
function adil_deactivate(): void {
    flush_rewrite_rules();
}
