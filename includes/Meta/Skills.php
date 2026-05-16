<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Skills {
    public static function register(): void {
        $fields = [
            '_hpcms_skill_level'      => 'string',
            '_hpcms_skill_icon'       => 'string',
            '_hpcms_skill_percentage' => 'integer',
            '_hpcms_experience_years' => 'integer',
            '_hpcms_skill_url'        => 'string',
        ];
        foreach ( $fields as $key => $type ) {
            $sanitize = 'sanitize_text_field';
            if ( $type === 'integer' ) {
                $sanitize = 'absint';
            } elseif ( $key === '_hpcms_skill_icon' ) {
                $sanitize = null; // Registry.php will handle sanitization on save
            }

            register_post_meta( 'hpcms_skill', $key, [
                'type'              => $type,
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => $sanitize,
                'default'           => $type === 'integer' ? 0 : '',
            ] );
        }
    }

    public static function render_box( \WP_Post $post ): void {
        wp_nonce_field( 'hpcms_save_meta', 'hpcms_nonce' );
        $fields = [
            '_hpcms_skill_level'      => [ 'label' => 'Skill Level',         'type' => 'select', 'options' => [ '' => '— Select —', 'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'expert' => 'Expert' ] ],
            '_hpcms_skill_percentage' => [ 'label' => 'Proficiency (%)',   'type' => 'number', 'placeholder' => '95' ],
            '_hpcms_skill_icon'       => [ 'label' => 'Icon (Lucide, SVG or URL)',   'type' => 'textarea', 'placeholder' => 'Enter Lucide icon name (e.g. Rocket), raw SVG code, or Image URL' ],
            '_hpcms_experience_years' => [ 'label' => 'Years of Experience', 'type' => 'number', 'placeholder' => '3' ],
            '_hpcms_skill_url'        => [ 'label' => 'Official URL',        'type' => 'url',    'placeholder' => 'https://reactjs.org' ],
        ];
        Helper::render_fields( $post, $fields );
        echo '<p class="description">Assign a <strong>Skill Category</strong> in the taxonomy box on the right.</p>';
    }
}
