<?php
/**
 * Runs when the plugin is deleted from WP Admin → Plugins.
 * Removes all plugin options and CPT posts.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// ── Delete all plugin options ──────────────────────────────────────────────────
$hpcms_options = [
    // Profile
    'hpcms_full_name', 'hpcms_tagline', 'hpcms_bio', 'hpcms_email',
    'hpcms_phone', 'hpcms_location', 'hpcms_avatar_url',
    // Social
    'hpcms_github', 'hpcms_linkedin', 'hpcms_twitter',
    'hpcms_youtube', 'hpcms_behance', 'hpcms_dribbble',
    // SEO
    'hpcms_meta_title', 'hpcms_meta_description', 'hpcms_og_image',
    // API & CORS
    'hpcms_enable_api', 'hpcms_enable_cors', 'hpcms_allowed_origins',
    'hpcms_cache_duration', 'hpcms_api_token', 'hpcms_frontend_url',
];

foreach ( $hpcms_options as $hpcms_opt ) {
    delete_option( $hpcms_opt );
}

// ── Delete all CPT posts and their meta ───────────────────────────────────────
$hpcms_post_types = [
    'hpcms_project',
    'hpcms_experience',
    'hpcms_education',
    'hpcms_resume',
    'hpcms_skill',
    'hpcms_testimonial',
];

foreach ( $hpcms_post_types as $hpcms_type ) {
    $hpcms_posts = get_posts( [
        'post_type'      => $hpcms_type,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    foreach ( $hpcms_posts as $hpcms_post_id ) {
        wp_delete_post( $hpcms_post_id, true ); // force delete (skip trash)
    }
}

// ── Delete custom taxonomy terms ──────────────────────────────────────────────
$hpcms_taxonomies = [
    'hpcms_project_category',
    'hpcms_technology',
    'hpcms_industry',
    'hpcms_skill_category',
];

foreach ( $hpcms_taxonomies as $hpcms_tax ) {
    $hpcms_terms = get_terms( [ 'taxonomy' => $hpcms_tax, 'hide_empty' => false ] );
    if ( ! is_wp_error( $hpcms_terms ) && ! empty( $hpcms_terms ) ) {
        foreach ( $hpcms_terms as $hpcms_term ) {
            wp_delete_term( $hpcms_term->term_id, $hpcms_tax );
        }
    }
}

flush_rewrite_rules();
