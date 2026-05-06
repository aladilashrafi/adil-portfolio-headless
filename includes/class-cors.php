<?php
defined( 'ABSPATH' ) || exit;

/**
 * Adds CORS headers to all /wp-json/adil/v1/* responses.
 * Origin is locked to the configured frontend URL.
 */
class Adil_CORS {

    public static function init(): void {
        // REST API responses
        add_action( 'rest_api_init', [ __CLASS__, 'add_cors_headers' ], 15 );

        // Handle preflight OPTIONS requests before WP tries to route them
        add_action( 'init', [ __CLASS__, 'handle_preflight' ] );
    }

    public static function add_cors_headers(): void {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        add_filter( 'rest_pre_serve_request', [ __CLASS__, 'send_cors_headers' ] );
    }

    public static function send_cors_headers( bool $served ): bool {
        $origin  = get_http_origin();
        $allowed = self::allowed_origins();

        if ( in_array( $origin, $allowed, true ) ) {
            header( 'Access-Control-Allow-Origin: '  . esc_url_raw( $origin ) );
        } else {
            // Fallback: allow the primary frontend
            header( 'Access-Control-Allow-Origin: ' . esc_url_raw( $allowed[0] ) );
        }

        header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Vary: Origin' );

        return $served;
    }

    public static function handle_preflight(): void {
        if ( 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {
            $origin  = get_http_origin();
            $allowed = self::allowed_origins();

            if ( in_array( $origin, $allowed, true ) ) {
                header( 'Access-Control-Allow-Origin: '  . esc_url_raw( $origin ) );
                header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
                header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
                header( 'Access-Control-Max-Age: 86400' );
                header( 'Content-Length: 0' );
                header( 'Content-Type: text/plain' );
                status_header( 204 );
                exit;
            }
        }
    }

    private static function allowed_origins(): array {
        return array_filter( [
            get_option( 'adil_frontend_url', 'https://adilashrafi.com' ),
            'https://adilashrafi.com',
            'http://localhost:3000',   // local Next.js dev
            'http://localhost:3001',
        ] );
    }
}
