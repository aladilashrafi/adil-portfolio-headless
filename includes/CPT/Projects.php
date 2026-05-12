<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Projects {
    public static function register(): void {
        register_post_type( 'hpcms_project', [
            'labels'          => [
                'name'               => __( 'Projects', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Project', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Project', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Project', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Project', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Project', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Project', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Projects', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Projects found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Projects in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Projects', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Projects', 'headless-portfolio-cms' ),
            ],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => 'headless-portfolio-cms',
            'show_in_rest'    => true,
            'rest_base'       => 'hpcms-projects',
            'supports'        => [ 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ],
            'menu_icon'       => 'dashicons-portfolio',
            'rewrite'         => [ 'slug' => 'projects' ],
            'capability_type' => 'post',
            'hierarchical'    => false,
            'taxonomies'      => [ 'hpcms_project_category', 'hpcms_technology', 'hpcms_industry' ],
            'has_archive'     => false,
        ] );
    }
}
