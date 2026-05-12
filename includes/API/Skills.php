<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Skills {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/skills', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
            'args'                => [
                'category' => [ 'type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ],
                'page'     => [ 'type' => 'integer', 'default' => 1, 'sanitize_callback' => 'absint' ],
                'per_page' => [ 'type' => 'integer', 'default' => -1, 'sanitize_callback' => 'absint' ],
            ],
        ] );

        register_rest_route( $ns, '/skills/(?P<slug>[a-z0-9\-]+)', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_item' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
            'args'                => [ 'slug' => [ 'required' => true, 'sanitize_callback' => 'sanitize_title' ] ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $category = $req->get_param( 'category' );
        $per_page = $req->get_param( 'per_page' ) ?: -1;

        $args = [
            'post_type'      => 'hpcms_skill',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];

        if ( $category ) {
            $args['tax_query'] = [
                [ 'taxonomy' => 'hpcms_skill_category', 'field' => 'slug', 'terms' => sanitize_title( $category ) ],
            ];
        }

        $posts = get_posts( $args );
        return new \WP_REST_Response( array_map( [ __CLASS__, 'shape' ], $posts ), 200 );
    }

    public static function get_item( \WP_REST_Request $req ) {
        $posts = get_posts( [
            'post_type'   => 'hpcms_skill',
            'name'        => $req->get_param( 'slug' ),
            'numberposts' => 1,
            'post_status' => 'publish',
        ] );
        if ( empty( $posts ) ) {
            return new \WP_Error( 'hpcms_not_found', 'Skill not found.', [ 'status' => 404 ] );
        }
        return new \WP_REST_Response( self::shape( $posts[0] ), 200 );
    }

    private static function shape( \WP_Post $post ): array {
        return [
            'id'              => $post->ID,
            'title'           => esc_html( $post->post_title ),
            'slug'            => $post->post_name,
            'level'           => esc_html( get_post_meta( $post->ID, '_hpcms_skill_level', true ) ),
            'icon'            => esc_url( get_post_meta( $post->ID, '_hpcms_skill_icon', true ) ),
            'experienceYears' => (int) get_post_meta( $post->ID, '_hpcms_experience_years', true ),
            'officialUrl'     => esc_url( get_post_meta( $post->ID, '_hpcms_skill_url', true ) ),
            'categories'      => Helper::get_terms_for( $post->ID, 'hpcms_skill_category' ),
            'order'           => (int) $post->menu_order,
        ];
    }
}
