<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Experience {
    public static function register(): void {
        $fields = [
            '_hpcms_company_name'     => 'string',
            '_hpcms_position'         => 'string',
            '_hpcms_employment_type'  => 'string',
            '_hpcms_start_date'       => 'string',
            '_hpcms_end_date'         => 'string',
            '_hpcms_current_position' => 'boolean',
            '_hpcms_company_url'      => 'string',
            '_hpcms_location'         => 'string',
        ];
        foreach ( $fields as $key => $type ) {
            register_post_meta( 'hpcms_experience', $key, [
                'type'              => $type,
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => $type === 'boolean' ? 'rest_sanitize_boolean' : 'sanitize_text_field',
                'default'           => $type === 'boolean' ? false : '',
            ] );
        }
    }

    public static function render_box( \WP_Post $post ): void {
        wp_nonce_field( 'hpcms_save_meta', 'hpcms_nonce' );
        $fields = [
            '_hpcms_company_name'    => [ 'label' => 'Company Name',     'type' => 'text',   'placeholder' => 'e.g. Mediusware Limited' ],
            '_hpcms_position'        => [ 'label' => 'Position / Role',  'type' => 'text',   'placeholder' => 'e.g. Senior Developer' ],
            '_hpcms_employment_type' => [ 'label' => 'Employment Type',  'type' => 'select', 'options' => [ '' => '— Select —', 'full-time' => 'Full-time', 'part-time' => 'Part-time', 'freelance' => 'Freelance', 'contract' => 'Contract', 'internship' => 'Internship' ] ],
            '_hpcms_start_date'      => [ 'label' => 'Start Date',       'type' => 'text',   'placeholder' => 'e.g. 2022-06' ],
            '_hpcms_end_date'        => [ 'label' => 'End Date',         'type' => 'text',   'placeholder' => 'Leave blank if current' ],
            '_hpcms_company_url'     => [ 'label' => 'Company Website',  'type' => 'url',    'placeholder' => 'https://company.com' ],
            '_hpcms_location'        => [ 'label' => 'Location',         'type' => 'text',   'placeholder' => 'e.g. Dhaka, Bangladesh (Remote)' ],
        ];
        Helper::render_fields( $post, $fields );

        $current = get_post_meta( $post->ID, '_hpcms_current_position', true );
        echo '<p><label><input type="checkbox" name="_hpcms_current_position" value="1"' . checked( $current, true, false ) . '> Current position</label></p>';
        echo '<p class="description">Use <strong>Menu Order</strong> (Page Attributes box) to control display order.</p>';
    }
}
