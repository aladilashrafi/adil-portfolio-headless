<?php
namespace HPCMS\Core;

defined( 'ABSPATH' ) || exit;

class CORS {
    public static function init(): void {
        add_action( 'rest_api_init', [ __CLASS__, 'handle_preflight' ], 15 );
    }

    public static function handle_preflight(): void {
        if ( get_option( 'hpcms_enable_cors', '1' ) !== '1' ) {
            return;
        }

        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        add_filter( 'rest_pre_serve_request', [ __CLASS__, 'send_cors_headers' ], 10, 4 );
    }

    public static function send_cors_headers( $value, $response, $request, $server ) {
        $origin  = get_http_origin();
        $allowed = Settings::get_allowed_origins();

        if ( $origin && ( in_array( '*', $allowed, true ) || in_array( $origin, $allowed, true ) ) ) {
            header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $origin ) );
            header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
            header( 'Access-Control-Allow-Credentials: true' );
            header( 'Access-Control-Allow-Headers: Authorization, X-WP-Nonce, Content-Type, X-Requested-With' );
        }

        if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
            exit;
        }

        return $value;
    }
}
