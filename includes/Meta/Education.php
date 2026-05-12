<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Education {
    public static function register(): void {
        $fields = [
            '_hpcms_institution'      => 'string',
            '_hpcms_degree'           => 'string',
            '_hpcms_field_of_study'   => 'string',
            '_hpcms_start_date'       => 'string',
            '_hpcms_end_date'         => 'string',
            '_hpcms_grade'            => 'string',
            '_hpcms_certificate_url'  => 'string',
        ];
        foreach ( $fields as $key => $type ) {
            register_post_meta( 'hpcms_education', $key, [
                'type'              => $type,
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ] );
        }
    }

    public static function render_box( \WP_Post $post ): void {
        wp_nonce_field( 'hpcms_save_meta', 'hpcms_nonce' );
        $fields = [
            '_hpcms_institution'    => [ 'label' => 'Institution / School', 'type' => 'text', 'placeholder' => 'e.g. University of Dhaka' ],
            '_hpcms_degree'         => [ 'label' => 'Degree / Certification', 'type' => 'text', 'placeholder' => 'e.g. B.Sc. Computer Science' ],
            '_hpcms_field_of_study' => [ 'label' => 'Field of Study',       'type' => 'text', 'placeholder' => 'e.g. Computer Science & Engineering' ],
            '_hpcms_start_date'     => [ 'label' => 'Start Date',           'type' => 'text', 'placeholder' => 'e.g. 2019-09' ],
            '_hpcms_end_date'       => [ 'label' => 'End Date',             'type' => 'text', 'placeholder' => 'e.g. 2023-06' ],
            '_hpcms_grade'          => [ 'label' => 'Grade / GPA',          'type' => 'text', 'placeholder' => 'e.g. 3.8/4.0 or A+' ],
            '_hpcms_certificate_url'=> [ 'label' => 'Certificate URL',      'type' => 'url',  'placeholder' => 'https://credential.net/...' ],
        ];
        Helper::render_fields( $post, $fields );
        echo '<p class="description">Use <strong>Menu Order</strong> (Page Attributes box) to control display order.</p>';
    }
}
