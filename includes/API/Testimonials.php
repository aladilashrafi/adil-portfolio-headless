<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Testimonials {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/testimonials', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $posts = get_posts( [
            'post_type'      => 'hpcms_testimonial',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );
        return new \WP_REST_Response( array_map( [ __CLASS__, 'shape' ], $posts ), 200 );
    }

    private static function shape( \WP_Post $post ): array {
        $image = get_post_meta( $post->ID, '_hpcms_client_image', true );
        if ( ! $image && has_post_thumbnail( $post->ID ) ) {
            $src   = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
            $image = $src ? esc_url( $src[0] ) : '';
        }
        return [
            'id'             => $post->ID,
            'slug'           => $post->post_name,
            'quote'          => wp_kses_post( apply_filters( 'hpcms_content', $post->post_content ) ),
            'clientName'     => esc_html( get_post_meta( $post->ID, '_hpcms_client_name', true ) ),
            'clientPosition' => esc_html( get_post_meta( $post->ID, '_hpcms_client_position', true ) ),
            'company'        => esc_html( get_post_meta( $post->ID, '_hpcms_company', true ) ),
            'companyUrl'     => esc_url( get_post_meta( $post->ID, '_hpcms_company_url', true ) ),
            'rating'         => (int) get_post_meta( $post->ID, '_hpcms_rating', true ) ?: 5,
            'clientImage'    => $image,
            'order'          => (int) $post->menu_order,
        ];
    }
}
