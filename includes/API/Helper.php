<?php
namespace HPCMS\API;

defined( 'ABSPATH' ) || exit;

class Helper {
    public static function get_featured_image( int $post_id ): array {
        if ( ! has_post_thumbnail( $post_id ) ) {
            return [ 'url' => '', 'width' => 0, 'height' => 0, 'alt' => '' ];
        }
        $thumb_id = get_post_thumbnail_id( $post_id );
        $src      = wp_get_attachment_image_src( $thumb_id, 'large' );
        return [
            'url'    => $src ? esc_url( $src[0] ) : '',
            'width'  => $src ? (int) $src[1] : 0,
            'height' => $src ? (int) $src[2] : 0,
            'alt'    => esc_attr( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ),
        ];
    }

    public static function get_terms_for( int $post_id, string $taxonomy ): array {
        $terms = get_the_terms( $post_id, $taxonomy );
        if ( is_wp_error( $terms ) || ! $terms ) {
            return [];
        }
        return array_map( static function ( \WP_Term $term ): array {
            return [
                'id'   => $term->term_id,
                'name' => esc_html( $term->name ),
                'slug' => $term->slug,
            ];
        }, $terms );
    }
}
