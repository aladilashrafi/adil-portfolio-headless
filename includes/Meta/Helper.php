<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Helper {
    public static function render_fields( \WP_Post $post, array $fields ): void {
        echo '<table class="form-table"><tbody>';
        foreach ( $fields as $key => $cfg ) {
            $value = get_post_meta( $post->ID, $key, true );
            echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $cfg['label'] ) . '</label></th><td>';

            if ( $cfg['type'] === 'select' ) {
                echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '">';
                foreach ( $cfg['options'] as $val => $label ) {
                    echo '<option value="' . esc_attr( $val ) . '"' . selected( $value, $val, false ) . '>' . esc_html( $label ) . '</option>';
                }
                echo '</select>';
            } elseif ( $cfg['type'] === 'textarea' ) {
                echo '<textarea name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '"'
                    . ' placeholder="' . esc_attr( $cfg['placeholder'] ?? '' ) . '"'
                    . ' class="large-text" rows="4">' . esc_textarea( $value ) . '</textarea>';
            } else {
                echo '<input type="' . esc_attr( $cfg['type'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '"'
                    . ' value="' . esc_attr( $value ) . '"'
                    . ' placeholder="' . esc_attr( $cfg['placeholder'] ?? '' ) . '"'
                    . ' class="regular-text">';
            }

            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
}
