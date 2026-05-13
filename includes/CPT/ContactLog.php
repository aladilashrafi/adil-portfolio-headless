<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class ContactLog {
    public static function register(): void {
        register_post_type( 'hpcms_contact_log', [
            'labels'       => [
                'name'               => __( 'Contact Inbox', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Message', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Message', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Message', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'View Message', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Message', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Message', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Inbox', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No messages found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No messages in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Messages', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Contact Inbox', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => false,
            'supports'     => [ 'title' ],
            'menu_icon'    => 'dashicons-email-alt',
            'rewrite'      => false,
        ] );
    }
}
