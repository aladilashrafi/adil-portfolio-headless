<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Testimonials {
    public static function register(): void {
        $fields = [
            '_hpcms_client_name'     => 'string',
            '_hpcms_client_position' => 'string',
            '_hpcms_company'         => 'string',
            '_hpcms_company_url'     => 'string',
            '_hpcms_rating'          => 'integer',
            '_hpcms_client_image'    => 'string',
        ];
        foreach ( $fields as $key => $type ) {
            register_post_meta( 'hpcms_testimonial', $key, [
                'type'              => $type,
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => $type === 'integer' ? 'absint' : 'sanitize_text_field',
                'default'           => $type === 'integer' ? 5 : '',
            ] );
        }
    }

    public static function render_box( \WP_Post $post ): void {
        wp_nonce_field( 'hpcms_save_meta', 'hpcms_nonce' );
        $fields = [
            '_hpcms_client_name'     => [ 'label' => 'Client Name',     'type' => 'text',   'placeholder' => 'Jane Smith' ],
            '_hpcms_client_position' => [ 'label' => 'Client Position', 'type' => 'text',   'placeholder' => 'E-commerce Manager' ],
            '_hpcms_company'         => [ 'label' => 'Company',         'type' => 'text',   'placeholder' => 'Acme Corp' ],
            '_hpcms_company_url'     => [ 'label' => 'Company Website', 'type' => 'url',    'placeholder' => 'https://acme.com' ],
            '_hpcms_rating'          => [ 'label' => 'Rating (1-5)',    'type' => 'number', 'placeholder' => '5' ],
            '_hpcms_client_image'    => [ 'label' => 'Client Image URL','type' => 'url',    'placeholder' => 'https://... or use Featured Image' ],
        ];
        Helper::render_fields( $post, $fields );
        echo '<p class="description">Testimonial quote goes in the main editor. You may also use the Featured Image for the client photo.</p>';
    }
}
