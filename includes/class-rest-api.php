<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers all REST API endpoints under the adil/v1 namespace.
 *
 * Base URL: https://api.adilashrafi.com/wp-json/adil/v1
 *
 * GET  /portfolio           — All data in one request (homepage ISR)
 * GET  /projects            — All projects (?featured=1 for featured)
 * GET  /projects/{slug}     — Single project by slug
 * GET  /services            — All services
 * GET  /experience          — All experience entries
 * GET  /skills              — All skills
 * GET  /testimonials        — All testimonials
 * GET  /settings            — Global site meta
 * POST /contact             — Contact form submission
 * POST /revalidate          — Trigger Next.js ISR revalidation
 */
class Adil_REST_API {

    public static function init(): void {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );

        // Auto-revalidate Next.js on content publish/update
        add_action( 'save_post', [ __CLASS__, 'trigger_revalidation' ], 20, 2 );
    }

    public static function register_routes(): void {
        $ns = ADIL_API_NS;

        // Bulk fetch
        register_rest_route( $ns, '/portfolio',          [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_portfolio' ],   'permission_callback' => '__return_true' ] );

        // Projects
        register_rest_route( $ns, '/projects',           [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_projects' ],    'permission_callback' => '__return_true' ] );
        register_rest_route( $ns, '/projects/(?P<slug>[a-z0-9\-]+)', [ 'methods' => 'GET', 'callback' => [ __CLASS__, 'get_project_by_slug' ], 'permission_callback' => '__return_true',
            'args' => [ 'slug' => [ 'required' => true, 'sanitize_callback' => 'sanitize_title' ] ] ] );

        // Individual collections
        register_rest_route( $ns, '/services',           [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_services' ],    'permission_callback' => '__return_true' ] );
        register_rest_route( $ns, '/experience',         [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_experience' ],  'permission_callback' => '__return_true' ] );
        register_rest_route( $ns, '/skills',             [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_skills' ],      'permission_callback' => '__return_true' ] );
        register_rest_route( $ns, '/testimonials',       [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_testimonials' ],'permission_callback' => '__return_true' ] );
        register_rest_route( $ns, '/settings',           [ 'methods' => 'GET',  'callback' => [ __CLASS__, 'get_settings' ],    'permission_callback' => '__return_true' ] );

        // Mutations
        register_rest_route( $ns, '/contact',            [ 'methods' => 'POST', 'callback' => [ __CLASS__, 'post_contact' ],    'permission_callback' => '__return_true' ] );
        register_rest_route( $ns, '/revalidate',         [ 'methods' => 'POST', 'callback' => [ __CLASS__, 'post_revalidate' ], 'permission_callback' => '__return_true' ] );
    }

    // ── GET /portfolio ────────────────────────────────────────────────────────

    public static function get_portfolio( WP_REST_Request $req ): WP_REST_Response {
        return new WP_REST_Response( [
            'meta'         => Adil_Settings::get_all(),
            'projects'     => self::fetch_projects(),
            'services'     => self::fetch_services(),
            'experience'   => self::fetch_experience(),
            'skills'       => self::fetch_skills(),
            'testimonials' => self::fetch_testimonials(),
            'clients'      => self::fetch_clients(),
        ], 200 );
    }

    private static function fetch_clients(): array {
        $query = new WP_Query( [
            'post_type'      => 'adil_client',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        $data = [];
        foreach ( $query->posts as $post ) {
            $logo_id = get_post_thumbnail_id( $post->ID );
            $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
            
            $data[] = [
                'id'   => $post->ID,
                'name' => $post->post_title,
                'logo' => $logo_url,
            ];
        }
        return $data;
    }

    // ── GET /projects ─────────────────────────────────────────────────────────

    public static function get_projects( WP_REST_Request $req ): WP_REST_Response {
        $featured = $req->get_param( 'featured' ) === '1';
        return new WP_REST_Response( self::fetch_projects( $featured ), 200 );
    }

    // ── GET /projects/{slug} ──────────────────────────────────────────────────

    public static function get_project_by_slug( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $posts = get_posts( [
            'post_type'   => 'adil_project',
            'name'        => $req->get_param( 'slug' ),
            'numberposts' => 1,
            'post_status' => 'publish',
        ] );

        if ( empty( $posts ) ) {
            return new WP_Error( 'not_found', 'Project not found.', [ 'status' => 404 ] );
        }

        return new WP_REST_Response( self::shape_project( $posts[0] ), 200 );
    }

    // ── GET /services ─────────────────────────────────────────────────────────

    public static function get_services( WP_REST_Request $req ): WP_REST_Response {
        return new WP_REST_Response( self::fetch_services(), 200 );
    }

    // ── GET /experience ───────────────────────────────────────────────────────

    public static function get_experience( WP_REST_Request $req ): WP_REST_Response {
        return new WP_REST_Response( self::fetch_experience(), 200 );
    }

    // ── GET /skills ───────────────────────────────────────────────────────────

    public static function get_skills( WP_REST_Request $req ): WP_REST_Response {
        return new WP_REST_Response( self::fetch_skills(), 200 );
    }

    // ── GET /testimonials ─────────────────────────────────────────────────────

    public static function get_testimonials( WP_REST_Request $req ): WP_REST_Response {
        return new WP_REST_Response( self::fetch_testimonials(), 200 );
    }

    // ── GET /settings ─────────────────────────────────────────────────────────

    public static function get_settings( WP_REST_Request $req ): WP_REST_Response {
        return new WP_REST_Response( Adil_Settings::get_all(), 200 );
    }

    // ── POST /contact ─────────────────────────────────────────────────────────

    public static function post_contact( WP_REST_Request $req ): WP_REST_Response {
        $params = $req->get_json_params() ?? [];

        $data = [
            'name'    => sanitize_text_field( $params['name']    ?? '' ),
            'email'   => sanitize_email(      $params['email']   ?? '' ),
            'subject' => sanitize_text_field( $params['subject'] ?? '' ),
            'message' => sanitize_textarea_field( $params['message'] ?? '' ),
            'budget'  => sanitize_text_field( $params['budget']  ?? '' ),
        ];

        $result = Adil_Contact_Handler::handle( $data );

        return new WP_REST_Response(
            [ 'success' => $result['success'], 'message' => $result['message'] ],
            $result['code']
        );
    }

    // ── POST /revalidate ──────────────────────────────────────────────────────
    // This endpoint lets WordPress itself trigger revalidation from WP admin actions.

    public static function post_revalidate( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $params = $req->get_json_params() ?? [];
        $secret = sanitize_text_field( $params['secret'] ?? '' );
        $path   = sanitize_text_field( $params['path']   ?? '/' );

        if ( $secret !== get_option( 'adil_revalidate_token', '' ) ) {
            return new WP_Error( 'forbidden', 'Invalid revalidation token.', [ 'status' => 403 ] );
        }

        // Forward revalidation to Next.js
        $result = self::ping_nextjs_revalidation( $path );

        return new WP_REST_Response( [
            'revalidated' => $result,
            'path'        => $path,
        ], 200 );
    }

    // ── Auto-revalidation on WP save ──────────────────────────────────────────

    public static function trigger_revalidation( int $post_id, WP_Post $post ): void {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }
        if ( $post->post_status !== 'publish' ) {
            return;
        }

        $type_path_map = [
            'adil_project'     => [ '/', '/projects' ],
            'adil_service'     => [ '/' ],
            'adil_experience'  => [ '/', '/resume' ],
            'adil_skill'       => [ '/', '/resume' ],
            'adil_testimonial' => [ '/' ],
        ];

        if ( ! isset( $type_path_map[ $post->post_type ] ) ) {
            return;
        }

        $paths = $type_path_map[ $post->post_type ];

        // For projects: also revalidate the individual project page
        if ( $post->post_type === 'adil_project' ) {
            $paths[] = '/projects/' . $post->post_name;
        }

        foreach ( $paths as $path ) {
            self::ping_nextjs_revalidation( $path );
        }
    }

    /**
     * Sends a POST to the Next.js /api/revalidate endpoint.
     * Non-blocking: uses wp_remote_post with a short timeout.
     */
    private static function ping_nextjs_revalidation( string $path ): bool {
        $frontend_url = rtrim( get_option( 'adil_frontend_url', 'https://adilashrafi.com' ), '/' );
        $token        = get_option( 'adil_revalidate_token', '' );

        if ( empty( $token ) ) {
            return false;
        }

        $response = wp_remote_post( $frontend_url . '/api/revalidate', [
            'timeout'     => 8,
            'blocking'    => false, // fire-and-forget
            'headers'     => [ 'Content-Type' => 'application/json' ],
            'body'        => wp_json_encode( [ 'path' => $path, 'secret' => $token ] ),
            'data_format' => 'body',
        ] );

        return ! is_wp_error( $response );
    }

    // ── Data fetchers / shapers ───────────────────────────────────────────────

    private static function fetch_projects( bool $featured_only = false ): array {
        $args = [
            'post_type'      => 'adil_project',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];

        if ( $featured_only ) {
            $args['meta_query'] = [ [ 'key' => 'adil_featured', 'value' => '1', 'compare' => '=' ] ];
        }

        return array_map(
            [ __CLASS__, 'shape_project' ],
            get_posts( $args )
        );
    }

    private static function shape_project( WP_Post $post ): array {
        $tech_raw  = get_post_meta( $post->ID, 'adil_tech_tags', true );
        $tech_tags = $tech_raw
            ? array_map( 'trim', explode( ',', $tech_raw ) )
            : [];

        $image_url = '';
        if ( has_post_thumbnail( $post->ID ) ) {
            $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
            $image_url = $src ? $src[0] : '';
        }

        return [
            'id'          => $post->ID,
            'slug'        => $post->post_name,
            'name'        => $post->post_title,
            'description' => wp_strip_all_tags( $post->post_content ),
            'content'     => apply_filters( 'the_content', $post->post_content ),
            'badge'       => get_post_meta( $post->ID, 'adil_badge',    true ),
            'url'         => get_post_meta( $post->ID, 'adil_url',      true ),
            'status'      => get_post_meta( $post->ID, 'adil_status',   true ) ?: 'live',
            'featured'    => (bool) get_post_meta( $post->ID, 'adil_featured', true ),
            'tech_tags'   => $tech_tags,
            'image_url'   => $image_url,
            'order'       => (int) $post->menu_order,
            'role'        => get_post_meta( $post->ID, 'adil_role',     true ),
            'timeline'    => get_post_meta( $post->ID, 'adil_timeline', true ),
            'categories'  => self::fetch_categories( $post->ID ),
        ];
    }

    private static function fetch_categories( int $post_id ): array {
        $terms = get_the_terms( $post_id, 'adil_project_cat' );
        if ( is_wp_error( $terms ) || ! $terms ) {
            return [];
        }
        return array_map( function( $term ) {
            return [
                'id'   => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
            ];
        }, $terms );
    }

    private static function fetch_services(): array {
        $posts = get_posts( [
            'post_type'      => 'adil_service',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        return array_map( function ( WP_Post $post ): array {
            return [
                'id'          => $post->ID,
                'slug'        => $post->post_name,
                'num'         => get_post_meta( $post->ID, 'adil_num',  true ),
                'icon'        => get_post_meta( $post->ID, 'adil_icon', true ),
                'name'        => $post->post_title,
                'description' => wp_strip_all_tags( $post->post_content ),
                'content'     => apply_filters( 'the_content', $post->post_content ),
                'order'       => (int) $post->menu_order,
            ];
        }, $posts );
    }

    private static function fetch_experience(): array {
        $posts = get_posts( [
            'post_type'      => 'adil_experience',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        return array_map( function ( WP_Post $post ): array {
            return [
                'id'          => $post->ID,
                'period'      => get_post_meta( $post->ID, 'adil_period',  true ),
                'role'        => $post->post_title,
                'company'     => get_post_meta( $post->ID, 'adil_company', true ),
                'description' => wp_strip_all_tags( $post->post_content ),
                'type'        => get_post_meta( $post->ID, 'adil_type',    true ) ?: 'work',
                'order'       => (int) $post->menu_order,
            ];
        }, $posts );
    }

    private static function fetch_skills(): array {
        $posts = get_posts( [
            'post_type'      => 'adil_skill',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        return array_map( function ( WP_Post $post ): array {
            return [
                'id'         => $post->ID,
                'name'       => $post->post_title,
                'percentage' => (int) get_post_meta( $post->ID, 'adil_percentage', true ),
                'category'   => get_post_meta( $post->ID, 'adil_category', true ) ?: 'core',
                'order'      => (int) $post->menu_order,
            ];
        }, $posts );
    }

    private static function fetch_testimonials(): array {
        $posts = get_posts( [
            'post_type'      => 'adil_testimonial',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );

        return array_map( function ( WP_Post $post ): array {
            $avatar = get_post_meta( $post->ID, 'adil_avatar_url', true );
            if ( ! $avatar && has_post_thumbnail( $post->ID ) ) {
                $src    = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
                $avatar = $src ? $src[0] : '';
            }
            return [
                'id'         => $post->ID,
                'quote'      => wp_strip_all_tags( $post->post_content ),
                'author'     => get_post_meta( $post->ID, 'adil_author',  true ),
                'title'      => get_post_meta( $post->ID, 'adil_title',   true ),
                'company'    => get_post_meta( $post->ID, 'adil_company', true ),
                'avatar_url' => $avatar,
            ];
        }, $posts );
    }
}
