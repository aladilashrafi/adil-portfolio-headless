<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Services {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/services', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $posts = get_posts( [
            'post_type'      => 'hpcms_service',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );
        return new \WP_REST_Response( array_map( [ __CLASS__, 'shape' ], $posts ), 200 );
    }

    private static function shape( \WP_Post $post ): array {
        return [
            'id'          => $post->ID,
            'slug'        => $post->post_name,
            'num'         => esc_html( get_post_meta( $post->ID, '_hpcms_service_num', true ) ),
            'icon'        => get_post_meta( $post->ID, '_hpcms_service_icon', true ),
            'name'        => esc_html( $post->post_title ),
            'description' => wp_kses_post( get_the_excerpt( $post ) ),
            'content'     => wp_kses_post( apply_filters( 'hpcms_content', $post->post_content ) ),
            'order'       => (int) $post->menu_order,
        ];
    }
}
