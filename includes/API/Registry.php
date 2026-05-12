<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Registry {
    public static function init(): void {
        add_action( 'rest_api_init', [ __CLASS__, 'register_all' ] );
    }

    public static function register_all(): void {
        if ( get_option( 'hpcms_enable_api', '1' ) !== '1' ) {
            return;
        }

        self::init_content_filters();

        $ns = HPCMS_API_NS;

        Projects::register_routes( $ns );
        Experience::register_routes( $ns );
        Education::register_routes( $ns );
        Resume::register_routes( $ns );
        Skills::register_routes( $ns );
        Testimonials::register_routes( $ns );
        Profile::register_routes( $ns );
    }

    private static function init_content_filters(): void {
        $filters = [
            'wptexturize',
            'convert_chars',
            'wpautop',
            'shortcode_unautop',
            'do_shortcode',
        ];
        foreach ( $filters as $filter ) {
            add_filter( 'hpcms_content', $filter );
        }
    }

    public static function check_public_read_permission(): bool {
        return true;
    }
}
