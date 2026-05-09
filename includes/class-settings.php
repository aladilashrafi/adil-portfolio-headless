<?php
defined( 'ABSPATH' ) || exit;

/**
 * Plugin settings: frontend URL, revalidation token, contact email.
 * Stored in wp_options.
 */
class Adil_Settings {

    public static function init(): void {
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function register_settings(): void {
        register_setting( 'adil_settings_group', 'adil_frontend_url',     [ 'sanitize_callback' => 'esc_url_raw',         'default' => 'https://adilashrafi.com' ] );
        register_setting( 'adil_settings_group', 'adil_contact_email',    [ 'sanitize_callback' => 'sanitize_email',      'default' => get_option( 'admin_email' ) ] );
        register_setting( 'adil_settings_group', 'adil_revalidate_token', [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '' ] );

        // Site meta fields
        register_setting( 'adil_settings_group', 'adil_site_title',                [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Al Adil Ashrafi' ] );
        register_setting( 'adil_settings_group', 'adil_site_tagline',              [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'The Marketing Alchemist' ] );
        register_setting( 'adil_settings_group', 'adil_site_bio',                  [ 'sanitize_callback' => 'wp_kses_post',        'default' => '' ] );
        register_setting( 'adil_settings_group', 'adil_site_location',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Mohammadpur, Dhaka, Bangladesh' ] );
        register_setting( 'adil_settings_group', 'adil_site_email',                [ 'sanitize_callback' => 'sanitize_email',      'default' => 'hello@adilashrafi.com' ] );
        register_setting( 'adil_settings_group', 'adil_site_linkedin',             [ 'sanitize_callback' => 'esc_url_raw',         'default' => 'https://www.linkedin.com/in/al-adil-ashrafi/' ] );
        register_setting( 'adil_settings_group', 'adil_site_availability',         [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Available for Freelance' ] );
        
        // Hero Stats
        register_setting( 'adil_settings_group', 'adil_hero_stat_roas',            [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '6.5×' ] );
        register_setting( 'adil_settings_group', 'adil_hero_stat_roas_sub',        [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'ROAS — Gulf Coast Marine · 90 Days' ] );
        register_setting( 'adil_settings_group', 'adil_hero_stat_ventures',        [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '3' ] );
        register_setting( 'adil_settings_group', 'adil_hero_stat_ventures_sub',    [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Mediusware · Markimist · Bangla Track' ] );
        
        // Bottom Hero Stats
        register_setting( 'adil_settings_group', 'adil_stat_1_label',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Years Leading Teams' ] );
        register_setting( 'adil_settings_group', 'adil_stat_1_value',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '2+' ] );
        register_setting( 'adil_settings_group', 'adil_stat_2_label',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Brands Scaled' ] );
        register_setting( 'adil_settings_group', 'adil_stat_2_value',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '10+' ] );
        register_setting( 'adil_settings_group', 'adil_stat_3_label',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Products Built' ] );
        register_setting( 'adil_settings_group', 'adil_stat_3_value',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '3' ] );

        // Section Headers
        register_setting( 'adil_settings_group', 'adil_services_label',           [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Services' ] );
        register_setting( 'adil_settings_group', 'adil_services_title',           [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'The compounds' ] );
        register_setting( 'adil_settings_group', 'adil_services_accent',          [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'I create' ] );

        register_setting( 'adil_settings_group', 'adil_projects_label',           [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Work' ] );
        register_setting( 'adil_settings_group', 'adil_projects_title',           [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Selected' ] );
        register_setting( 'adil_settings_group', 'adil_projects_accent',          [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Experiments' ] );

        register_setting( 'adil_settings_group', 'adil_resume_label',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Experience' ] );
        register_setting( 'adil_settings_group', 'adil_resume_title',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Growth' ] );
        register_setting( 'adil_settings_group', 'adil_resume_accent',            [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Trajectory' ] );

        register_setting( 'adil_settings_group', 'adil_contact_label',            [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Contact' ] );
        register_setting( 'adil_settings_group', 'adil_contact_title',            [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Ready to build' ] );
        register_setting( 'adil_settings_group', 'adil_contact_accent',           [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'New Compounds?' ] );

        register_setting( 'adil_settings_group', 'adil_about_label',              [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'About' ] );
        register_setting( 'adil_settings_group', 'adil_about_title',              [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Where chemistry' ] );
        register_setting( 'adil_settings_group', 'adil_about_accent',             [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'meets marketing' ] );

        register_setting( 'adil_settings_group', 'adil_testimonials_label',       [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Proof' ] );
        register_setting( 'adil_settings_group', 'adil_testimonials_title',       [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Client' ] );
        register_setting( 'adil_settings_group', 'adil_testimonials_accent',      [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Reactions' ] );

        register_setting( 'adil_settings_group', 'adil_cta_primary_label',         [ 'sanitize_callback' => 'sanitize_text_field', 'default' => "Let's Work →" ] );
        register_setting( 'adil_settings_group', 'adil_cta_primary_href',          [ 'sanitize_callback' => 'sanitize_text_field', 'default' => '#contact' ] );
        register_setting( 'adil_settings_group', 'adil_cta_secondary_label',       [ 'sanitize_callback' => 'sanitize_text_field', 'default' => 'View Resume' ] );
        register_setting( 'adil_settings_group', 'adil_cta_secondary_href',        [ 'sanitize_callback' => 'esc_url_raw',         'default' => 'https://adilashrafi.com/resume/' ] );
    }

    /**
     * Returns all settings as a structured array for the REST /settings endpoint.
     */
    public static function get_all(): array {
        return [
            'title'                   => get_option( 'adil_site_title',             'Al Adil Ashrafi' ),
            'tagline'                 => get_option( 'adil_site_tagline',            'The Marketing Alchemist' ),
            'bio'                     => get_option( 'adil_site_bio',                '' ),
            'location'                => get_option( 'adil_site_location',           'Mohammadpur, Dhaka, Bangladesh' ),
            'email'                   => get_option( 'adil_site_email',              'hello@adilashrafi.com' ),
            'linkedin'                => get_option( 'adil_site_linkedin',           'https://www.linkedin.com/in/al-adil-ashrafi/' ),
            'availability'            => get_option( 'adil_site_availability',       'Available for Freelance' ),
            
            // Stats
            'hero_stat_roas'          => get_option( 'adil_hero_stat_roas',          '6.5×' ),
            'hero_stat_roas_sub'      => get_option( 'adil_hero_stat_roas_sub',      'ROAS — Gulf Coast Marine · 90 Days' ),
            'hero_stat_ventures'      => get_option( 'adil_hero_stat_ventures',      '3' ),
            'hero_stat_ventures_sub'  => get_option( 'adil_hero_stat_ventures_sub',  'Mediusware · Markimist · Bangla Track' ),
            
            'stat_1_label'            => get_option( 'adil_stat_1_label',           'Years Leading Teams' ),
            'stat_1_value'            => get_option( 'adil_stat_1_value',           '2+' ),
            'stat_2_label'            => get_option( 'adil_stat_2_label',           'Brands Scaled' ),
            'stat_2_value'            => get_option( 'adil_stat_2_value',           '10+' ),
            'stat_3_label'            => get_option( 'adil_stat_3_label',           'Products Built' ),
            'stat_3_value'            => get_option( 'adil_stat_3_value',           '3' ),

            // Section Headers
            'services_label'          => get_option( 'adil_services_label',         'Services' ),
            'services_title'          => get_option( 'adil_services_title',         'The compounds' ),
            'services_accent'         => get_option( 'adil_services_accent',        'I create' ),

            'projects_label'          => get_option( 'adil_projects_label',         'Work' ),
            'projects_title'          => get_option( 'adil_projects_title',         'Selected' ),
            'projects_accent'         => get_option( 'adil_projects_accent',        'Experiments' ),

            'resume_label'            => get_option( 'adil_resume_label',           'Experience' ),
            'resume_title'            => get_option( 'adil_resume_title',           'Growth' ),
            'resume_accent'           => get_option( 'adil_resume_accent',          'Trajectory' ),

            'contact_label'           => get_option( 'adil_contact_label',          'Contact' ),
            'contact_title'           => get_option( 'adil_contact_title',          'Ready to build' ),
            'contact_accent'          => get_option( 'adil_contact_accent',         'New Compounds?' ),

            'about_label'             => get_option( 'adil_about_label',           'About' ),
            'about_title'             => get_option( 'adil_about_title',           'Where chemistry' ),
            'about_accent'            => get_option( 'adil_about_accent',          'meets marketing' ),

            'testimonials_label'      => get_option( 'adil_testimonials_label',     'Proof' ),
            'testimonials_title'      => get_option( 'adil_testimonials_title',     'Client' ),
            'testimonials_accent'     => get_option( 'adil_testimonials_accent',    'Reactions' ),

            'cta_primary_label'       => get_option( 'adil_cta_primary_label',       "Let's Work →" ),
            'cta_primary_href'        => get_option( 'adil_cta_primary_href',        '#contact' ),
            'cta_secondary_label'     => get_option( 'adil_cta_secondary_label',     'View Resume' ),
            'cta_secondary_href'      => get_option( 'adil_cta_secondary_href',      'https://adilashrafi.com/resume/' ),
        ];
    }
}
