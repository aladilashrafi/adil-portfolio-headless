<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Experience {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/experience', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
            'args'                => [
                'page'     => [ 'type' => 'integer', 'default' => 1,  'sanitize_callback' => 'absint' ],
                'per_page' => [ 'type' => 'integer', 'default' => 20, 'sanitize_callback' => 'absint' ],
            ],
        ] );

        register_rest_route( $ns, '/experience/(?P<slug>[a-z0-9\-]+)', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_item' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
            'args'                => [ 'slug' => [ 'required' => true, 'sanitize_callback' => 'sanitize_title' ] ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $per_page = $req->get_param( 'per_page' ) ?: -1;
        $posts    = get_posts( [
            'post_type'      => 'hpcms_experience',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );
        return new \WP_REST_Response( array_map( [ __CLASS__, 'shape' ], $posts ), 200 );
    }

    public static function get_item( \WP_REST_Request $req ) {
        $posts = get_posts( [
            'post_type'   => 'hpcms_experience',
            'name'        => $req->get_param( 'slug' ),
            'numberposts' => 1,
            'post_status' => 'publish',
        ] );
        if ( empty( $posts ) ) {
            return new \WP_Error( 'hpcms_not_found', 'Experience entry not found.', [ 'status' => 404 ] );
        }
        return new \WP_REST_Response( self::shape( $posts[0] ), 200 );
    }

    private static function shape( \WP_Post $post ): array {
        return [
            'id'              => $post->ID,
            'title'           => html_entity_decode( $post->post_title ),
            'slug'            => $post->post_name,
            'role'            => html_entity_decode( get_post_meta( $post->ID, '_hpcms_position', true ) ?: $post->post_title ),
            'company'         => html_entity_decode( get_post_meta( $post->ID, '_hpcms_company_name', true ) ),
            'companyUrl'      => esc_url( get_post_meta( $post->ID, '_hpcms_company_url', true ) ),
            'employmentType'  => html_entity_decode( get_post_meta( $post->ID, '_hpcms_employment_type', true ) ),
            'startDate'       => html_entity_decode( get_post_meta( $post->ID, '_hpcms_start_date', true ) ),
            'endDate'         => html_entity_decode( get_post_meta( $post->ID, '_hpcms_end_date', true ) ),
            'isCurrent'       => (bool) get_post_meta( $post->ID, '_hpcms_current_position', true ),
            'location'        => html_entity_decode( get_post_meta( $post->ID, '_hpcms_location', true ) ),
            'description'     => wp_kses_post( apply_filters( 'hpcms_content', $post->post_content ) ),
            'order'           => (int) $post->menu_order,
        ];
    }
}
