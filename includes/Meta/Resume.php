<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Resume {
    public static function register(): void {
        $fields = [
            '_hpcms_resume_file'    => 'string',
            '_hpcms_resume_version' => 'string',
            '_hpcms_resume_type'    => 'string',
            '_hpcms_last_updated'   => 'string',
        ];
        foreach ( $fields as $key => $type ) {
            register_post_meta( 'hpcms_resume', $key, [
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
            '_hpcms_resume_file'    => [ 'label' => 'Resume File URL',  'type' => 'url',    'placeholder' => 'https://... (PDF link)' ],
            '_hpcms_resume_version' => [ 'label' => 'Version',          'type' => 'text',   'placeholder' => 'e.g. v2.1' ],
            '_hpcms_resume_type'    => [ 'label' => 'Resume Type',      'type' => 'select', 'options' => [ '' => '— Select —', 'developer' => 'Developer Resume', 'marketing' => 'Marketing Resume', 'agency' => 'Agency Resume', 'designer' => 'Designer Resume', 'general' => 'General Resume' ] ],
            '_hpcms_last_updated'   => [ 'label' => 'Last Updated Date','type' => 'text',   'placeholder' => 'e.g. 2025-05-01' ],
        ];
        Helper::render_fields( $post, $fields );
        echo '<p class="description">Upload your resume PDF via the Media Library and paste the direct URL above.</p>';
    }
}
