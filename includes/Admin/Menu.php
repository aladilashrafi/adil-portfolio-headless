<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class Menu {
    public static function init(): void {
        add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'custom_menu_order', '__return_true' );
        add_action( 'menu_order', [ __CLASS__, 'reorder_submenu' ], 999 );
    }

    public static function reorder_submenu( $menu_order ) {
        global $submenu;
        $parent_slug = 'headless-portfolio-cms';

        if ( ! isset( $submenu[ $parent_slug ] ) ) {
            return $menu_order;
        }

        $items = $submenu[ $parent_slug ];
        $dashboard = [];
        $api_ref = [];
        $settings = [];
        $cpts = [];

        foreach ( $items as $item ) {
            $slug = $item[2];
            if ( $slug === $parent_slug ) {
                $dashboard[] = $item;
            } elseif ( $slug === 'hpcms-api-reference' ) {
                $api_ref[] = $item;
            } elseif ( $slug === 'hpcms-settings' ) {
                $settings[] = $item;
            } else {
                $cpts[] = $item;
            }
        }

        // Dashboard -> API Ref -> CPTs -> Settings
        $submenu[ $parent_slug ] = array_merge( $dashboard, $api_ref, $cpts, $settings );

        return $menu_order;
    }

    public static function register_menu(): void {
        add_menu_page(
            __( 'Headless Portfolio CMS', 'headless-portfolio-cms' ),
            __( 'Portfolio CMS', 'headless-portfolio-cms' ),
            'manage_options',
            'headless-portfolio-cms',
            [ Dashboard::class, 'render' ],
            HPCMS_PLUGIN_URL . 'assets/favicon.svg',
            100
        );

        add_submenu_page( 'headless-portfolio-cms', __( 'Dashboard', 'headless-portfolio-cms' ),    __( 'Dashboard', 'headless-portfolio-cms' ),    'manage_options', 'headless-portfolio-cms',      [ Dashboard::class, 'render' ] );
        add_submenu_page( 'headless-portfolio-cms', __( 'API Reference', 'headless-portfolio-cms' ),__( 'API Reference', 'headless-portfolio-cms' ),'manage_options', 'hpcms-api-reference',         [ API_Reference::class, 'render' ] );
        add_submenu_page( 'headless-portfolio-cms', __( 'Settings', 'headless-portfolio-cms' ),     __( 'Settings', 'headless-portfolio-cms' ),     'manage_options', 'hpcms-settings',              [ Settings::class, 'render' ] );
    }

    public static function enqueue_assets( $hook ): void {
        if ( strpos( $hook, 'headless-portfolio-cms' ) !== false || strpos( $hook, 'hpcms-' ) !== false ) {
            wp_enqueue_style( 'hpcms-admin-css', HPCMS_PLUGIN_URL . 'assets/css/admin.css', [], HPCMS_VERSION );
        }

        if ( 'plugins.php' === $hook ) {
            wp_enqueue_style( 'hpcms-admin-css', HPCMS_PLUGIN_URL . 'assets/css/admin.css', [], HPCMS_VERSION );
            wp_enqueue_script( 'hpcms-deactivation-js', HPCMS_PLUGIN_URL . 'assets/js/deactivation.js', [ 'jquery' ], HPCMS_VERSION, true );
            wp_localize_script( 'hpcms-deactivation-js', 'hpcms_deactivation', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'hpcms_deactivation_nonce' ),
                'plugin_slug' => 'headless-portfolio-cms'
            ] );
        }
    }
}
