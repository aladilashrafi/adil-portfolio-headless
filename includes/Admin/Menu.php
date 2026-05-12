<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class Menu {
    public static function init(): void {
        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    public static function register_menu(): void {
        add_menu_page(
            __( 'Headless Portfolio CMS', 'headless-portfolio-cms' ),
            __( 'Portfolio CMS', 'headless-portfolio-cms' ),
            'manage_options',
            'headless-portfolio-cms',
            [ Dashboard::class, 'render' ],
            'dashicons-rest-api',
            25
        );

        add_submenu_page( 'headless-portfolio-cms', __( 'Dashboard', 'headless-portfolio-cms' ),    __( 'Dashboard', 'headless-portfolio-cms' ),    'manage_options', 'headless-portfolio-cms',      [ Dashboard::class, 'render' ] );
        add_submenu_page( 'headless-portfolio-cms', __( 'Settings', 'headless-portfolio-cms' ),     __( 'Settings', 'headless-portfolio-cms' ),     'manage_options', 'hpcms-settings',              [ Settings::class, 'render' ] );
        add_submenu_page( 'headless-portfolio-cms', __( 'API Reference', 'headless-portfolio-cms' ),__( 'API Reference', 'headless-portfolio-cms' ),'manage_options', 'hpcms-api-reference',         [ API_Reference::class, 'render' ] );
    }

    public static function enqueue_assets( $hook ): void {
        if ( strpos( $hook, 'headless-portfolio-cms' ) !== false || strpos( $hook, 'hpcms-' ) !== false ) {
            wp_enqueue_style( 'hpcms-admin-css', HPCMS_PLUGIN_URL . 'assets/css/admin.css', [], HPCMS_VERSION );
        }
    }
}
