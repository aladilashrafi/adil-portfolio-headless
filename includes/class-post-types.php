<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers all Custom Post Types for the Adil Portfolio plugin.
 */
class Adil_Post_Types {

    public static function init(): void {
        add_action( 'init', [ __CLASS__, 'register_all' ] );
    }

    public static function register_all(): void {
        self::register_taxonomies();
        self::register_project();
        self::register_service();
        self::register_experience();
        self::register_skill();
        self::register_testimonial();
        self::register_client(); // New
        self::register_contact_log();
    }

    // ... (keep taxonomies etc)

    // ── Client ────────────────────────────────────────────────────────────────
    private static function register_client(): void {
        register_post_type( 'adil_client', [
            'labels'              => self::labels( 'Client', 'Clients' ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'rest_base'           => 'portfolio-clients',
            'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
            'menu_icon'           => 'dashicons-groups',
            'rewrite'             => false,
        ] );
    }

    // ── Taxonomies ────────────────────────────────────────────────────────────
    private static function register_taxonomies(): void {
        register_taxonomy( 'adil_project_cat', 'adil_project', [
            'labels'            => self::tax_labels( 'Project Category', 'Project Categories' ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'project-category' ],
        ] );
    }

    // ── Project ───────────────────────────────────────────────────────────────
    private static function register_project(): void {
        register_post_type( 'adil_project', [
            'labels'              => self::labels( 'Project', 'Projects' ),
            'public'              => true, // Changed to true for direct access/SEO if needed
            'show_ui'             => true,
            'show_in_menu'        => true, // Separate menu
            'show_in_rest'        => true,
            'rest_base'           => 'portfolio-projects',
            'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes', 'excerpt' ],
            'menu_icon'           => 'dashicons-portfolio',
            'rewrite'             => [ 'slug' => 'projects' ],
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'taxonomies'          => [ 'adil_project_cat' ],
        ] );
    }

    // ── Service ───────────────────────────────────────────────────────────────
    private static function register_service(): void {
        register_post_type( 'adil_service', [
            'labels'              => self::labels( 'Service', 'Services' ),
            'public'              => true, // Future-ready for separate pages
            'show_ui'             => true,
            'show_in_menu'        => true, // Separate menu
            'show_in_rest'        => true,
            'rest_base'           => 'portfolio-services',
            'supports'            => [ 'title', 'editor', 'page-attributes', 'thumbnail' ],
            'menu_icon'           => 'dashicons-admin-tools',
            'rewrite'             => [ 'slug' => 'services' ],
        ] );
    }

    // ── Experience ────────────────────────────────────────────────────────────
    private static function register_experience(): void {
        register_post_type( 'adil_experience', [
            'labels'              => self::labels( 'Experience', 'Experience' ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true, // Separate menu
            'show_in_rest'        => true,
            'rest_base'           => 'portfolio-experience',
            'supports'            => [ 'title', 'editor', 'page-attributes' ],
            'menu_icon'           => 'dashicons-businessman',
            'rewrite'             => false,
        ] );
    }

    // ── Skill ─────────────────────────────────────────────────────────────────
    private static function register_skill(): void {
        register_post_type( 'adil_skill', [
            'labels'              => self::labels( 'Skill', 'Skills' ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true, // Separate menu
            'show_in_rest'        => true,
            'rest_base'           => 'portfolio-skills',
            'supports'            => [ 'title', 'page-attributes' ],
            'menu_icon'           => 'dashicons-chart-bar',
            'rewrite'             => false,
        ] );
    }

    // ── Testimonial ───────────────────────────────────────────────────────────
    private static function register_testimonial(): void {
        register_post_type( 'adil_testimonial', [
            'labels'              => self::labels( 'Testimonial', 'Testimonials' ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true, // Separate menu
            'show_in_rest'        => true,
            'rest_base'           => 'portfolio-testimonials',
            'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
            'menu_icon'           => 'dashicons-format-quote',
            'rewrite'             => false,
        ] );
    }

    // ── Contact Log (private) ─────────────────────────────────────────────────
    private static function register_contact_log(): void {
        register_post_type( 'adil_contact_log', [
            'labels'              => self::labels( 'Contact Submission', 'Contact Inbox' ),
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true, // Separate menu
            'show_in_rest'        => false,
            'supports'            => [ 'title' ],
            'menu_icon'           => 'dashicons-email-alt',
            'rewrite'             => false,
            'capabilities'        => [
                'create_posts'    => 'do_not_allow',
            ],
            'map_meta_cap'        => true,
        ] );
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    private static function tax_labels( string $singular, string $plural ): array {
        return [
            'name'              => $plural,
            'singular_name'     => $singular,
            'search_items'      => "Search {$plural}",
            'all_items'         => "All {$plural}",
            'parent_item'       => "Parent {$singular}",
            'parent_item_colon' => "Parent {$singular}:",
            'edit_item'         => "Edit {$singular}",
            'update_item'       => "Update {$singular}",
            'add_new_item'      => "Add New {$singular}",
            'new_item_name'     => "New {$singular} Name",
            'menu_name'         => $plural,
        ];
    }

    // ── Helper ────────────────────────────────────────────────────────────────
    private static function labels( string $singular, string $plural ): array {
        return [
            'name'               => $plural,
            'singular_name'      => $singular,
            'add_new'            => "Add {$singular}",
            'add_new_item'       => "Add New {$singular}",
            'edit_item'          => "Edit {$singular}",
            'new_item'           => "New {$singular}",
            'view_item'          => "View {$singular}",
            'search_items'       => "Search {$plural}",
            'not_found'          => "No {$plural} found",
            'not_found_in_trash' => "No {$plural} in Trash",
            'all_items'          => "All {$plural}",
            'menu_name'          => $plural,
        ];
    }
}
