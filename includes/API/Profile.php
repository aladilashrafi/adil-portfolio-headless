<?php
namespace HPCMS\API;

use HPCMS\Core\Settings;

defined( 'ABSPATH' ) || exit;

class Profile {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/profile', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_item' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
        ] );
    }

    public static function get_item( \WP_REST_Request $req ): \WP_REST_Response {
        return new \WP_REST_Response( Settings::get_profile(), 200 );
    }
}
