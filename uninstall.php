<?php
/**
 * Runs when the plugin is deleted from WP Admin → Plugins.
 * Removes all plugin options and optionally CPT posts.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// ── Delete all plugin options ─────────────────────────────────────────────────
$options = [
    'adil_frontend_url',
    'adil_contact_email',
    'adil_revalidate_token',
    'adil_site_title',
    'adil_site_tagline',
    'adil_site_bio',
    'adil_site_location',
    'adil_site_email',
    'adil_site_linkedin',
    'adil_site_availability',
    'adil_hero_stat_roas',
    'adil_hero_stat_roas_sub',
    'adil_hero_stat_ventures',
    'adil_hero_stat_ventures_sub',
    'adil_cta_primary_label',
    'adil_cta_primary_href',
    'adil_cta_secondary_label',
    'adil_cta_secondary_href',
];

foreach ( $options as $opt ) {
    delete_option( $opt );
}

// ── Delete all CPT posts and their meta ───────────────────────────────────────
$post_types = [
    'adil_project',
    'adil_service',
    'adil_experience',
    'adil_skill',
    'adil_testimonial',
    'adil_contact_log',
];

foreach ( $post_types as $type ) {
    $posts = get_posts( [
        'post_type'      => $type,
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );

    foreach ( $posts as $post_id ) {
        wp_delete_post( $post_id, true ); // true = force delete (skip trash)
    }
}

// ── Flush rewrite rules ───────────────────────────────────────────────────────
flush_rewrite_rules();
