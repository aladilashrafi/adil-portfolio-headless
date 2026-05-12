<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Testimonials {
    public static function register(): void {
        register_post_type( 'hpcms_testimonial', [
            'labels'       => [
                'name'               => __( 'Testimonials', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Testimonial', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Testimonial', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Testimonial', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Testimonial', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Testimonial', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Testimonial', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Testimonials', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Testimonials found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Testimonials in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Testimonials', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Testimonials', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-testimonials',
            'supports'     => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
            'menu_icon'    => 'dashicons-format-quote',
            'rewrite'      => false,
        ] );
    }
}
