<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Clients {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/clients', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $posts = get_posts( [
            'post_type'      => 'hpcms_client',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ] );
        return new \WP_REST_Response( array_map( [ __CLASS__, 'shape' ], $posts ), 200 );
    }

    private static function shape( \WP_Post $post ): array {
        return [
            'id'    => $post->ID,
            'name'  => esc_html( $post->post_title ),
            'logo'  => get_the_post_thumbnail_url( $post->ID, 'full' ) ?: '',
            'order' => (int) $post->menu_order,
        ];
    }
}
