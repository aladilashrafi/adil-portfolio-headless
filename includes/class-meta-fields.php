<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers meta boxes and REST-exposed meta fields for all portfolio CPTs.
 * All fields are registered with show_in_rest so they appear in the WP REST API
 * AND are editable in the Gutenberg sidebar.
 */
class Adil_Meta_Fields {

    public static function init(): void {
        add_action( 'init',       [ __CLASS__, 'register_meta' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_boxes' ] );
        add_action( 'save_post',  [ __CLASS__, 'save_meta' ] );
    }

    // ── Register post meta (show_in_rest = true) ──────────────────────────────

    public static function register_meta(): void {

        // ── Project fields ────────────────────────────────────────────────────
        $project_fields = [
            'adil_badge'       => 'string',
            'adil_url'         => 'string',
            'adil_status'      => 'string',   // 'live' | 'development'
            'adil_featured'    => 'boolean',
            'adil_tech_tags'   => 'string',   // comma-separated
            'adil_role'        => 'string',   // e.g. 'Lead Strategist'
            'adil_timeline'    => 'string',   // e.g. '2023 - Present'
        ];
        foreach ( $project_fields as $key => $type ) {
            register_post_meta( 'adil_project', $key, [
                'type'         => $type,
                'single'       => true,
                'show_in_rest' => true,
                'default'      => $type === 'boolean' ? false : '',
            ] );
        }

        // ── Service fields ────────────────────────────────────────────────────
        $service_fields = [
            'adil_num'   => 'string',
            'adil_icon'  => 'string',
        ];
        foreach ( $service_fields as $key => $type ) {
            register_post_meta( 'adil_service', $key, [
                'type'         => $type,
                'single'       => true,
                'show_in_rest' => true,
                'default'      => '',
            ] );
        }

        // ── Experience fields ─────────────────────────────────────────────────
        $exp_fields = [
            'adil_period'  => 'string',
            'adil_company' => 'string',
            'adil_type'    => 'string',  // 'work' | 'education'
        ];
        foreach ( $exp_fields as $key => $type ) {
            register_post_meta( 'adil_experience', $key, [
                'type'         => $type,
                'single'       => true,
                'show_in_rest' => true,
                'default'      => '',
            ] );
        }

        // ── Skill fields ──────────────────────────────────────────────────────
        register_post_meta( 'adil_skill', 'adil_percentage', [
            'type'         => 'integer',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ] );
        register_post_meta( 'adil_skill', 'adil_category', [
            'type'         => 'string',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 'core',
        ] );

        // ── Testimonial fields ────────────────────────────────────────────────
        $testimonial_fields = [
            'adil_author'     => 'string',
            'adil_title'      => 'string',
            'adil_company'    => 'string',
            'adil_avatar_url' => 'string',
        ];
        foreach ( $testimonial_fields as $key => $type ) {
            register_post_meta( 'adil_testimonial', $key, [
                'type'         => $type,
                'single'       => true,
                'show_in_rest' => true,
                'default'      => '',
            ] );
        }

        // ── Contact log fields ────────────────────────────────────────────────
        $log_fields = [
            'adil_contact_name'    => 'string',
            'adil_contact_email'   => 'string',
            'adil_contact_subject' => 'string',
            'adil_contact_message' => 'string',
            'adil_contact_budget'  => 'string',
            'adil_contact_ip'      => 'string',
            'adil_contact_read'    => 'boolean',
        ];
        foreach ( $log_fields as $key => $type ) {
            register_post_meta( 'adil_contact_log', $key, [
                'type'         => $type,
                'single'       => true,
                'show_in_rest' => false,  // private
                'default'      => $type === 'boolean' ? false : '',
            ] );
        }
    }

    // ── Meta Boxes ────────────────────────────────────────────────────────────

    public static function add_meta_boxes(): void {
        add_meta_box( 'adil_project_meta',     'Project Details',      [ __CLASS__, 'render_project_box' ],     'adil_project',     'normal', 'high' );
        add_meta_box( 'adil_service_meta',     'Service Details',      [ __CLASS__, 'render_service_box' ],     'adil_service',     'normal', 'high' );
        add_meta_box( 'adil_experience_meta',  'Experience Details',   [ __CLASS__, 'render_experience_box' ],  'adil_experience',  'normal', 'high' );
        add_meta_box( 'adil_skill_meta',       'Skill Details',        [ __CLASS__, 'render_skill_box' ],       'adil_skill',       'normal', 'high' );
        add_meta_box( 'adil_testimonial_meta', 'Testimonial Details',  [ __CLASS__, 'render_testimonial_box' ], 'adil_testimonial', 'normal', 'high' );
    }

    // ── Render Boxes ──────────────────────────────────────────────────────────

    public static function render_project_box( WP_Post $post ): void {
        wp_nonce_field( 'adil_save_meta', 'adil_nonce' );
        $fields = [
            'adil_badge'     => [ 'label' => 'Badge / Category Label', 'type' => 'text',     'placeholder' => 'e.g. Plugin · WooCommerce · Bangladesh' ],
            'adil_url'       => [ 'label' => 'Project URL',            'type' => 'url',      'placeholder' => 'https://banglatrack.com' ],
            'adil_status'    => [ 'label' => 'Status',                 'type' => 'select',   'options' => [ 'live' => 'Live', 'development' => 'In Development' ] ],
            'adil_tech_tags' => [ 'label' => 'Tech Tags (comma-separated)', 'type' => 'text', 'placeholder' => 'WooCommerce, PHP, WordPress' ],
            'adil_role'      => [ 'label' => 'Your Role',              'type' => 'text',     'placeholder' => 'Lead Strategist' ],
            'adil_timeline'  => [ 'label' => 'Timeline',               'type' => 'text',     'placeholder' => '2023 - Present' ],
        ];
        self::render_fields( $post, $fields );

        $featured = get_post_meta( $post->ID, 'adil_featured', true );
        echo '<p><label><input type="checkbox" name="adil_featured" value="1"' . checked( $featured, true, false ) . '> Featured project</label></p>';
    }

    public static function render_service_box( WP_Post $post ): void {
        wp_nonce_field( 'adil_save_meta', 'adil_nonce' );
        $fields = [
            'adil_num'  => [ 'label' => 'Number Label', 'type' => 'text', 'placeholder' => '01' ],
            'adil_icon' => [ 'label' => 'Icon (Unicode symbol)', 'type' => 'text', 'placeholder' => '⬡' ],
        ];
        self::render_fields( $post, $fields );
        echo '<p class="description">Description goes in the main editor / content area.</p>';
    }

    public static function render_experience_box( WP_Post $post ): void {
        wp_nonce_field( 'adil_save_meta', 'adil_nonce' );
        $fields = [
            'adil_period'  => [ 'label' => 'Period', 'type' => 'text', 'placeholder' => 'Mar 2025 – Present' ],
            'adil_company' => [ 'label' => 'Company / Institution', 'type' => 'text', 'placeholder' => 'Mediusware Limited' ],
            'adil_type'    => [ 'label' => 'Type', 'type' => 'select', 'options' => [ 'work' => 'Work', 'education' => 'Education' ] ],
        ];
        self::render_fields( $post, $fields );
        echo '<p class="description">Description goes in the main editor / content area. Use <strong>Menu Order</strong> (Page Attributes) to control sort order.</p>';
    }

    public static function render_skill_box( WP_Post $post ): void {
        wp_nonce_field( 'adil_save_meta', 'adil_nonce' );
        $fields = [
            'adil_percentage' => [ 'label' => 'Proficiency %', 'type' => 'number', 'placeholder' => '90' ],
            'adil_category'   => [ 'label' => 'Category', 'type' => 'text', 'placeholder' => 'e.g. core, marketing, strategy' ],
        ];
        self::render_fields( $post, $fields );
    }

    public static function render_testimonial_box( WP_Post $post ): void {
        wp_nonce_field( 'adil_save_meta', 'adil_nonce' );
        $fields = [
            'adil_author'     => [ 'label' => 'Author Name',   'type' => 'text', 'placeholder' => 'John Smith' ],
            'adil_title'      => [ 'label' => 'Job Title',      'type' => 'text', 'placeholder' => 'E-commerce Manager' ],
            'adil_company'    => [ 'label' => 'Company',        'type' => 'text', 'placeholder' => 'Gulf Coast Marine Outfitters' ],
            'adil_avatar_url' => [ 'label' => 'Avatar URL',     'type' => 'url',  'placeholder' => 'https://...' ],
        ];
        self::render_fields( $post, $fields );
        echo '<p class="description">Quote goes in the main editor / content area.</p>';
    }

    // ── Generic field renderer ────────────────────────────────────────────────

    private static function render_fields( WP_Post $post, array $fields ): void {
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

    // ── Save Meta ─────────────────────────────────────────────────────────────

    public static function save_meta( int $post_id ): void {
        if ( ! isset( $_POST['adil_nonce'] ) || ! wp_verify_nonce( $_POST['adil_nonce'], 'adil_save_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $all_fields = [
            // Project
            'adil_badge', 'adil_url', 'adil_status', 'adil_tech_tags', 'adil_role', 'adil_timeline',
            // Service
            'adil_num', 'adil_icon',
            // Experience
            'adil_period', 'adil_company', 'adil_type',
            // Skill
            'adil_percentage', 'adil_category',
            // Testimonial
            'adil_author', 'adil_title', 'adil_avatar_url',
        ];

        foreach ( $all_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                $value = $key === 'adil_percentage'
                    ? absint( $_POST[ $key ] )
                    : sanitize_text_field( $_POST[ $key ] );
                update_post_meta( $post_id, $key, $value );
            }
        }

        // Checkbox (boolean)
        update_post_meta( $post_id, 'adil_featured', isset( $_POST['adil_featured'] ) );
    }
}
