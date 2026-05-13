<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Registry {
    public static function init(): void {
        add_action( 'init', [ __CLASS__, 'register_all' ] );
    }

    public static function register_all(): void {
        Projects::register();
        Experience::register();
        Education::register();
        Resume::register();
        Skills::register();
        Testimonials::register();
        Services::register();
        ContactLog::register();
        Clients::register();
    }
}
