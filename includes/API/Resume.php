<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Resume {
    public static function register_routes( string $ns ): void {
        register_rest_route( $ns, '/resume', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'get_items' ],
            'permission_callback' => [ Registry::class, 'check_public_read_permission' ],
        ] );
    }

    public static function get_items( \WP_REST_Request $req ): \WP_REST_Response {
        $posts = get_posts( [
            'post_type'      => 'hpcms_resume',
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
            'title'       => esc_html( $post->post_title ),
            'slug'        => $post->post_name,
            'fileUrl'     => esc_url( get_post_meta( $post->ID, '_hpcms_resume_file', true ) ),
            'version'     => esc_html( get_post_meta( $post->ID, '_hpcms_resume_version', true ) ),
            'type'        => esc_html( get_post_meta( $post->ID, '_hpcms_resume_type', true ) ),
            'lastUpdated' => esc_html( get_post_meta( $post->ID, '_hpcms_last_updated', true ) ),
            'order'       => (int) $post->menu_order,
        ];
    }
}
