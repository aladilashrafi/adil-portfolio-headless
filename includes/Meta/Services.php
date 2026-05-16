<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Services {
    public static function register(): void {
        register_meta( 'post', '_hpcms_service_num', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ] );
        register_meta( 'post', '_hpcms_service_icon', [
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'sanitize_callback' => null,
        ] );
    }

    public static function render_box( \WP_Post $post ): void {
        wp_nonce_field( 'hpcms_save_meta', 'hpcms_nonce' );
        $num  = get_post_meta( $post->ID, '_hpcms_service_num', true );
        $icon = get_post_meta( $post->ID, '_hpcms_service_icon', true );
        ?>
        <table class="form-table">
            <tr>
                <th><label for="_hpcms_service_num">Service Number</label></th>
                <td><input type="text" name="_hpcms_service_num" id="_hpcms_service_num" value="<?php echo esc_attr( $num ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="_hpcms_service_icon">Service Icon (Lucide/SVG/URL)</label></th>
                <td><textarea name="_hpcms_service_icon" id="_hpcms_service_icon" class="large-text" rows="4" placeholder="Enter Lucide icon name (e.g. Rocket), raw SVG code, or Image URL"><?php echo esc_textarea( $icon ); ?></textarea></td>
            </tr>
        </table>
        <?php
    }
}
