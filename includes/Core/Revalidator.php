<?php
namespace HPCMS\Core;

defined( 'ABSPATH' ) || exit;

class Revalidator {
    public static function init(): void {
        add_action( 'save_post', [ __CLASS__, 'handle_save' ], 20, 2 );
    }

    public static function handle_save( int $post_id, \WP_Post $post ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( $post->post_status !== 'publish' ) {
            return;
        }

        $supported_types = [
            'hpcms_project', 'hpcms_service', 'hpcms_experience', 
            'hpcms_education', 'hpcms_skill', 'hpcms_testimonial',
            'hpcms_client', 'hpcms_resume'
        ];

        if ( ! in_array( $post->post_type, $supported_types, true ) ) {
            return;
        }

        self::ping_frontend( $post );
    }

    private static function ping_frontend( \WP_Post $post ): void {
        $frontend_url = get_option( 'hpcms_frontend_url' );
        $token        = get_option( 'hpcms_revalidate_token' );

        if ( ! $frontend_url || ! $token ) {
            return;
        }

        $paths = self::get_affected_paths( $post );
        
        foreach ( $paths as $path ) {
            wp_remote_post( trailingslashit( $frontend_url ) . 'api/revalidate', [
                'blocking' => false,
                'timeout'  => 5,
                'body'     => wp_json_encode( [
                    'secret' => $token,
                    'path'   => $path,
                ] ),
                'headers'  => [ 'Content-Type' => 'application/json' ],
            ] );
        }
    }

    private static function get_affected_paths( \WP_Post $post ): array {
        $paths = [ '/' ]; // Always revalidate home

        switch ( $post->post_type ) {
            case 'hpcms_project':
                $paths[] = '/projects';
                $paths[] = '/projects/' . $post->post_name;
                break;
            case 'hpcms_experience':
            case 'hpcms_education':
            case 'hpcms_skill':
                $paths[] = '/resume';
                break;
        }

        return array_unique( $paths );
    }
}
