<?php
defined( 'ABSPATH' ) || exit;

/**
 * Handles saving contact form submissions to the WordPress admin inbox.
 * Also wires up the admin list view so submissions are readable in WP admin.
 */
class Adil_Contact_Log {

    public static function init(): void {
        add_filter( 'manage_adil_contact_log_posts_columns',       [ __CLASS__, 'set_columns' ] );
        add_action( 'manage_adil_contact_log_posts_custom_column', [ __CLASS__, 'render_column' ], 10, 2 );
        add_filter( 'manage_edit-adil_contact_log_sortable_columns', [ __CLASS__, 'sortable_columns' ] );
        add_action( 'admin_head', [ __CLASS__, 'admin_head_styles' ] );
    }

    /**
     * Saves a contact submission to the WP admin inbox.
     * Returns the new post ID on success, WP_Error on failure.
     */
    public static function save( array $data ): int|WP_Error {
        $post_id = wp_insert_post( [
            'post_type'   => 'adil_contact_log',
            'post_title'  => sanitize_text_field( $data['subject'] ) . ' — ' . sanitize_text_field( $data['name'] ),
            'post_status' => 'publish',
            'post_author' => 1,
        ] );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        update_post_meta( $post_id, 'adil_contact_name',    sanitize_text_field( $data['name'] ) );
        update_post_meta( $post_id, 'adil_contact_email',   sanitize_email( $data['email'] ) );
        update_post_meta( $post_id, 'adil_contact_subject', sanitize_text_field( $data['subject'] ) );
        update_post_meta( $post_id, 'adil_contact_message', sanitize_textarea_field( $data['message'] ) );
        update_post_meta( $post_id, 'adil_contact_budget',  sanitize_text_field( $data['budget'] ?? '' ) );
        update_post_meta( $post_id, 'adil_contact_ip',      sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
        update_post_meta( $post_id, 'adil_contact_read',    false );

        return $post_id;
    }

    // ── Admin list columns ────────────────────────────────────────────────────

    public static function set_columns( array $columns ): array {
        return [
            'cb'           => $columns['cb'],
            'title'        => 'Subject',
            'contact_name' => 'Name',
            'contact_email'=> 'Email',
            'budget'       => 'Budget',
            'read_status'  => 'Status',
            'date'         => 'Date',
        ];
    }

    public static function render_column( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'contact_name':
                echo esc_html( get_post_meta( $post_id, 'adil_contact_name', true ) );
                break;
            case 'contact_email':
                $email = get_post_meta( $post_id, 'adil_contact_email', true );
                echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
                break;
            case 'budget':
                echo esc_html( get_post_meta( $post_id, 'adil_contact_budget', true ) ?: '—' );
                break;
            case 'read_status':
                $read = get_post_meta( $post_id, 'adil_contact_read', true );
                echo $read
                    ? '<span style="color:#019cff;font-weight:600;">Read</span>'
                    : '<span style="color:#fe5401;font-weight:700;">● New</span>';
                break;
        }
    }

    public static function sortable_columns( array $columns ): array {
        $columns['date'] = 'date';
        return $columns;
    }

    public static function admin_head_styles(): void {
        $screen = get_current_screen();
        if ( ! $screen || $screen->post_type !== 'adil_contact_log' ) return;
        echo '<style>
            .column-read_status { width: 80px; }
            .column-budget       { width: 120px; }
            .column-contact_email{ width: 200px; }
        </style>';
    }
}
