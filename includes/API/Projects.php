<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Projects {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/projects', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
            'args'                => [
                'page'        => [ 'type' => 'integer', 'default' => 1,    'minimum' => 1, 'sanitize_callback' => 'absint' ],
                'per_page'    => [ 'type' => 'integer', 'default' => 10,   'minimum' => 1, 'maximum' => 100, 'sanitize_callback' => 'absint' ],
                'technology'  => [ 'type' => 'string',  'default' => '',   'sanitize_callback' => 'sanitize_text_field' ],
                'category'    => [ 'type' => 'string',  'default' => '',   'sanitize_callback' => 'sanitize_text_field' ],
                'industry'    => [ 'type' => 'string',  'default' => '',   'sanitize_callback' => 'sanitize_text_field' ],
                'featured'    => [ 'type' => 'string',  'default' => '',   'sanitize_callback' => 'sanitize_text_field' ],
                'sort'        => [ 'type' => 'string',  'default' => 'menu_order', 'sanitize_callback' => 'sanitize_text_field', 'enum' => [ 'menu_order', 'latest', 'oldest', 'title' ] ],
            ],
        ] );

        register_rest_route( $ns, '/projects/(?P<slug>[a-z0-9\-]+)', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_item' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
            'args'                => [
                'slug' => [ 'required' => true, 'sanitize_callback' => 'sanitize_title' ],
            ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $page       = $req->get_param( 'page' );
        $per_page   = $req->get_param( 'per_page' );
        $technology = $req->get_param( 'technology' );
        $category   = $req->get_param( 'category' );
        $industry   = $req->get_param( 'industry' );
        $featured   = $req->get_param( 'featured' );
        $sort       = $req->get_param( 'sort' );

        $args = [
            'post_type'      => 'hpcms_project',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];

        switch ( $sort ) {
            case 'latest':
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
            case 'oldest':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
            case 'title':
                $args['orderby'] = 'title';
                $args['order']   = 'ASC';
                break;
        }

        if ( $featured === '1' || $featured === 'true' ) {
            $args['meta_query'] = [
                [ 'key' => '_hpcms_featured', 'value' => '1', 'compare' => '=' ],
            ];
        }

        $tax_query = [];
        if ( $technology ) {
            $tax_query[] = [ 'taxonomy' => 'hpcms_technology', 'field' => 'slug', 'terms' => sanitize_title( $technology ) ];
        }
        if ( $category ) {
            $tax_query[] = [ 'taxonomy' => 'hpcms_project_category', 'field' => 'slug', 'terms' => sanitize_title( $category ) ];
        }
        if ( $industry ) {
            $tax_query[] = [ 'taxonomy' => 'hpcms_industry', 'field' => 'slug', 'terms' => sanitize_title( $industry ) ];
        }
        if ( ! empty( $tax_query ) ) {
            $tax_query['relation']  = 'AND';
            $args['tax_query']      = $tax_query;
        }

        $query  = new \WP_Query( $args );
        $posts  = array_map( [ __CLASS__, 'shape' ], $query->posts );

        $response = new \WP_REST_Response( $posts, 200 );
        $response->header( 'X-WP-Total',      (string) $query->found_posts );
        $response->header( 'X-WP-TotalPages', (string) $query->max_num_pages );
        return $response;
    }

    public static function get_item( \WP_REST_Request $req ) {
        $posts = get_posts( [
            'post_type'   => 'hpcms_project',
            'name'        => $req->get_param( 'slug' ),
            'numberposts' => 1,
            'post_status' => 'publish',
        ] );

        if ( empty( $posts ) ) {
            return new \WP_Error( 'hpcms_not_found', 'Project not found.', [ 'status' => 404 ] );
        }

        return new \WP_REST_Response( self::shape( $posts[0] ), 200 );
    }

    private static function shape( \WP_Post $post ): array {
        $tech_raw  = get_post_meta( $post->ID, '_hpcms_tech_stack', true );
        $tech_tags = $tech_raw
            ? array_values( array_filter( array_map( 'trim', explode( ',', $tech_raw ) ) ) )
            : [];

        $gallery_raw = get_post_meta( $post->ID, '_hpcms_gallery', true );
        $gallery     = [];
        if ( $gallery_raw ) {
            $decoded = json_decode( $gallery_raw, true );
            $gallery = is_array( $decoded ) ? array_map( 'esc_url', $decoded ) : [];
        }

        return [
            'id'            => $post->ID,
            'title'         => esc_html( $post->post_title ),
            'slug'          => $post->post_name,
            'excerpt'       => esc_html( get_the_excerpt( $post ) ),
            'content'       => wp_kses_post( apply_filters( 'the_content', $post->post_content ) ),
            'featuredImage' => Helper::get_featured_image( $post->ID ),
            'client'        => esc_html( get_post_meta( $post->ID, '_hpcms_client_name', true ) ),
            'links'         => [
                'live'   => esc_url( get_post_meta( $post->ID, '_hpcms_project_url', true ) ),
                'github' => esc_url( get_post_meta( $post->ID, '_hpcms_github_url', true ) ),
            ],
            'completionDate'=> esc_html( get_post_meta( $post->ID, '_hpcms_completion_date', true ) ),
            'featured'      => (bool) get_post_meta( $post->ID, '_hpcms_featured', true ),
            'techStack'     => $tech_tags,
            'gallery'       => $gallery,
            'seo'           => [
                'title'       => esc_html( get_post_meta( $post->ID, '_hpcms_seo_title', true ) ?: $post->post_title ),
                'description' => esc_html( get_post_meta( $post->ID, '_hpcms_seo_description', true ) ),
            ],
            'technologies'  => Helper::get_terms_for( $post->ID, 'hpcms_technology' ),
            'categories'    => Helper::get_terms_for( $post->ID, 'hpcms_project_category' ),
            'industries'    => Helper::get_terms_for( $post->ID, 'hpcms_industry' ),
            'order'         => (int) $post->menu_order,
            'date'          => $post->post_date,
            'modified'      => $post->post_modified,
        ];
    }
}
