<?php
namespace HPCMS\Core;

defined( 'ABSPATH' ) || exit;

class Settings {
    public static function init(): void {
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function register_settings(): void {
        // Profile
        register_setting( 'hpcms_settings_profile', 'hpcms_full_name',   [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_tagline',     [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_hero_bio',    [ 'sanitize_callback' => 'wp_kses_post',        'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_bio',         [ 'sanitize_callback' => 'wp_kses_post',        'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_email',       [ 'sanitize_callback' => 'sanitize_email',      'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_phone',       [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_location',    [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_profile', 'hpcms_avatar_url',  [ 'sanitize_callback' => 'esc_url_raw',         'default' => '' ] );

        // Social
        register_setting( 'hpcms_settings_social', 'hpcms_github',    [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );
        register_setting( 'hpcms_settings_social', 'hpcms_linkedin',  [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );
        register_setting( 'hpcms_settings_social', 'hpcms_twitter',   [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );
        register_setting( 'hpcms_settings_social', 'hpcms_youtube',   [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );
        register_setting( 'hpcms_settings_social', 'hpcms_behance',   [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );
        register_setting( 'hpcms_settings_social', 'hpcms_dribbble',  [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );

        // SEO
        register_setting( 'hpcms_settings_seo', 'hpcms_meta_title',       [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_seo', 'hpcms_meta_description', [ 'sanitize_callback' => 'sanitize_textarea_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_seo', 'hpcms_og_image',         [ 'sanitize_callback' => 'esc_url_raw',         'default' => '' ] );

        // API
        register_setting( 'hpcms_settings_api', 'hpcms_enable_api',      [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '1' ] );
        register_setting( 'hpcms_settings_api', 'hpcms_enable_cors',     [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '1' ] );
        register_setting( 'hpcms_settings_api', 'hpcms_allowed_origins', [ 'sanitize_callback' => 'sanitize_textarea_field', 'default' => "http://localhost:3000\nhttp://localhost:8000" ] );
        register_setting( 'hpcms_settings_api', 'hpcms_cache_duration',  [ 'sanitize_callback' => 'absint', 'default' => 3600 ] );
        register_setting( 'hpcms_settings_api', 'hpcms_api_token',       [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_api', 'hpcms_frontend_url',    [ 'sanitize_callback' => 'esc_url_raw', 'default' => '' ] );
        register_setting( 'hpcms_settings_api', 'hpcms_revalidate_token', [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );
        register_setting( 'hpcms_settings_api', 'hpcms_contact_email',   [ 'sanitize_callback' => 'sanitize_email', 'default' => '' ] );
    }

    public static function get_profile(): array {
        return [
            'name'             => esc_html( get_option( 'hpcms_full_name', '' ) ),
            'tagline'          => esc_html( get_option( 'hpcms_tagline', '' ) ),
            'hero_bio'         => wp_kses_post( get_option( 'hpcms_hero_bio', '' ) ),
            'bio'              => wp_kses_post( get_option( 'hpcms_bio', '' ) ),
            'email'            => sanitize_email( get_option( 'hpcms_email', '' ) ),
            'phone'            => esc_html( get_option( 'hpcms_phone', '' ) ),
            'location'         => esc_html( get_option( 'hpcms_location', '' ) ),
            'avatar'           => esc_url( get_option( 'hpcms_avatar_url', '' ) ),
            'social'           => [
                'github'   => esc_url( get_option( 'hpcms_github', '' ) ),
                'linkedin' => esc_url( get_option( 'hpcms_linkedin', '' ) ),
                'twitter'  => esc_url( get_option( 'hpcms_twitter', '' ) ),
                'youtube'  => esc_url( get_option( 'hpcms_youtube', '' ) ),
                'behance'  => esc_url( get_option( 'hpcms_behance', '' ) ),
                'dribbble' => esc_url( get_option( 'hpcms_dribbble', '' ) ),
            ],
            'seo'              => [
                'title'       => esc_html( get_option( 'hpcms_meta_title', '' ) ),
                'description' => esc_html( get_option( 'hpcms_meta_description', '' ) ),
                'ogImage'     => esc_url( get_option( 'hpcms_og_image', '' ) ),
            ],
        ];
    }

    public static function get_allowed_origins(): array {
        $raw = get_option( 'hpcms_allowed_origins', 'http://localhost:3000' );
        $lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
        return array_values( $lines );
    }
}
