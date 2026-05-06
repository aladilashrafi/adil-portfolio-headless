<?php
defined( 'ABSPATH' ) || exit;

/**
 * Admin UI: custom menu, dashboard overview, settings page, API reference.
 */
class Adil_Admin_UI {

    public static function init(): void {
        add_action( 'admin_menu',         [ __CLASS__, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'admin_notices',      [ __CLASS__, 'settings_saved_notice' ] );
    }

    // ── Menu ──────────────────────────────────────────────────────────────────

    public static function register_menu(): void {
        // Top-level menu for Global Portfolio Control
        add_menu_page(
            'Portfolio CMS',
            'Portfolio CMS',
            'manage_options',
            'adil-portfolio',
            [ __CLASS__, 'render_dashboard' ],
            'dashicons-analytics',
            25
        );

        // Sub-pages
        add_submenu_page( 'adil-portfolio', 'Dashboard',    'Dashboard',    'manage_options', 'adil-portfolio',          [ __CLASS__, 'render_dashboard' ] );
        add_submenu_page( 'adil-portfolio', 'Settings',     'Settings',     'manage_options', 'adil-settings',           [ __CLASS__, 'render_settings' ] );
        add_submenu_page( 'adil-portfolio', 'Documentation','Documentation','manage_options', 'adil-documentation',      [ __CLASS__, 'render_documentation' ] );

        // We removed the individual CPT submenu pages here because they now have their own 
        // top-level menus as requested by the user.
    }

    // ── Assets ────────────────────────────────────────────────────────────────

    public static function enqueue_assets( string $hook ): void {
        if ( strpos( $hook, 'adil' ) === false && strpos( $hook, 'adil-portfolio' ) === false ) {
            // Only load on portfolio admin pages
            $screen = get_current_screen();
            if ( ! $screen || strpos( $screen->post_type ?? '', 'adil_' ) === false ) {
                if ( strpos( $hook, 'adil' ) === false ) return;
            }
        }

        wp_enqueue_style(
            'adil-admin',
            ADIL_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ADIL_VERSION
        );
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public static function render_dashboard(): void {
        $counts = [
            'adil_project'     => wp_count_posts( 'adil_project' )->publish     ?? 0,
            'adil_service'     => wp_count_posts( 'adil_service' )->publish      ?? 0,
            'adil_experience'  => wp_count_posts( 'adil_experience' )->publish   ?? 0,
            'adil_skill'       => wp_count_posts( 'adil_skill' )->publish        ?? 0,
            'adil_testimonial' => wp_count_posts( 'adil_testimonial' )->publish  ?? 0,
        ];
        $unread      = self::get_unread_count();
        $frontend    = get_option( 'adil_frontend_url', 'https://adilashrafi.com' );
        $api_base    = ADIL_SITE_URL . '/wp-json/' . ADIL_API_NS;
        $token       = get_option( 'adil_revalidate_token', '' );
        ?>
        <div class="wrap adil-wrap">
            <div class="adil-header">
                <div class="adil-header-inner">
                    <div class="adil-logo">⬡ Portfolio CMS</div>
                    <div class="adil-header-meta">
                        <span>v<?php echo ADIL_VERSION; ?></span>
                        <a href="<?php echo esc_url( $frontend ); ?>" target="_blank" class="adil-btn-sm">
                            View Site ↗
                        </a>
                    </div>
                </div>
            </div>

            <div class="adil-dashboard-grid">

                <!-- Stats -->
                <div class="adil-card adil-stats-grid">
                    <?php
                    $stat_items = [
                        [ 'label' => 'Projects',     'count' => $counts['adil_project'],     'link' => 'edit.php?post_type=adil_project',     'icon' => '◈' ],
                        [ 'label' => 'Services',     'count' => $counts['adil_service'],     'link' => 'edit.php?post_type=adil_service',     'icon' => '⬡' ],
                        [ 'label' => 'Experience',   'count' => $counts['adil_experience'],  'link' => 'edit.php?post_type=adil_experience',  'icon' => '▣' ],
                        [ 'label' => 'Skills',       'count' => $counts['adil_skill'],       'link' => 'edit.php?post_type=adil_skill',       'icon' => '△' ],
                        [ 'label' => 'Testimonials', 'count' => $counts['adil_testimonial'], 'link' => 'edit.php?post_type=adil_testimonial', 'icon' => '◉' ],
                        [ 'label' => 'Unread Msgs',  'count' => $unread,                      'link' => 'edit.php?post_type=adil_contact_log', 'icon' => '◇', 'accent' => $unread > 0 ],
                    ];
                    foreach ( $stat_items as $item ) :
                        $accent = ! empty( $item['accent'] ) ? 'adil-stat--accent' : '';
                    ?>
                        <a href="<?php echo esc_url( admin_url( $item['link'] ) ); ?>" class="adil-stat <?php echo $accent; ?>">
                            <span class="adil-stat-icon"><?php echo $item['icon']; ?></span>
                            <span class="adil-stat-num"><?php echo (int) $item['count']; ?></span>
                            <span class="adil-stat-label"><?php echo esc_html( $item['label'] ); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- API Status -->
                <div class="adil-card">
                    <h2 class="adil-card-title">API Status</h2>
                    <div class="adil-api-url">
                        <span class="adil-badge adil-badge--blue">GET</span>
                        <code><?php echo esc_html( $api_base ); ?>/portfolio</code>
                    </div>
                    <div class="adil-token-row">
                        <strong>Revalidation Token:</strong>
                        <code class="adil-token"><?php echo esc_html( $token ?: 'Not generated — save Settings.' ); ?></code>
                        <button class="adil-btn-copy button button-secondary" data-copy="<?php echo esc_attr( $token ); ?>">Copy</button>
                    </div>
                    <p class="adil-hint">Add this token to your Next.js <code>.env.local</code> as <code>REVALIDATE_TOKEN</code>.</p>
                    <p class="adil-hint"><strong>Frontend URL:</strong> <a href="<?php echo esc_url( $frontend ); ?>" target="_blank"><?php echo esc_html( $frontend ); ?></a></p>
                </div>

                <!-- Quick Add -->
                <div class="adil-card">
                    <h2 class="adil-card-title">Quick Add</h2>
                    <div class="adil-quick-links">
                        <?php
                        $quick = [
                            [ 'post-new.php?post_type=adil_project',     '◈ New Project'     ],
                            [ 'post-new.php?post_type=adil_service',     '⬡ New Service'     ],
                            [ 'post-new.php?post_type=adil_experience',  '▣ New Experience'  ],
                            [ 'post-new.php?post_type=adil_skill',       '△ New Skill'       ],
                            [ 'post-new.php?post_type=adil_testimonial', '◉ New Testimonial' ],
                        ];
                        foreach ( $quick as [$link, $label] ) :
                        ?>
                            <a href="<?php echo esc_url( admin_url( $link ) ); ?>" class="adil-quick-link">
                                <?php echo esc_html( $label ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>

        <script>
        document.querySelectorAll('.adil-btn-copy').forEach(btn => {
            btn.addEventListener('click', () => {
                navigator.clipboard.writeText(btn.dataset.copy).then(() => {
                    btn.textContent = 'Copied!';
                    setTimeout(() => btn.textContent = 'Copy', 2000);
                });
            });
        });
        </script>
        <?php
    }

    // ── Settings ──────────────────────────────────────────────────────────────

    public static function render_settings(): void {
        if ( ! current_user_can( 'manage_options' ) ) return;

        // Regenerate token action
        if ( isset( $_POST['adil_regen_token'] ) && check_admin_referer( 'adil_regen_token' ) ) {
            update_option( 'adil_revalidate_token', wp_generate_password( 48, false ) );
            add_settings_error( 'adil_messages', 'adil_token_regen', 'Revalidation token regenerated. Update your Next.js .env.local.', 'updated' );
        }

        if ( isset( $_POST['adil_settings_submit'] ) ) {
            check_admin_referer( 'adil_settings_save' );
            foreach ( [
                'adil_frontend_url','adil_contact_email',
                'adil_site_title','adil_site_tagline','adil_site_bio',
                'adil_site_location','adil_site_email','adil_site_linkedin',
                'adil_site_availability','adil_hero_stat_roas','adil_hero_stat_roas_sub',
                'adil_hero_stat_ventures','adil_hero_stat_ventures_sub',
                'adil_cta_primary_label','adil_cta_primary_href',
                'adil_cta_secondary_label','adil_cta_secondary_href',
            ] as $key ) {
                if ( isset( $_POST[ $key ] ) ) {
                    update_option( $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
                }
            }
            add_settings_error( 'adil_messages', 'adil_saved', 'Settings saved.', 'updated' );
        }

        settings_errors( 'adil_messages' );
        $token = get_option( 'adil_revalidate_token', '' );
        ?>
        <div class="wrap adil-wrap">
            <div class="adil-header">
                <div class="adil-header-inner">
                    <div class="adil-logo">⬡ Settings</div>
                </div>
            </div>

            <form method="post" class="adil-settings-form">
                <?php wp_nonce_field( 'adil_settings_save' ); ?>
                <input type="hidden" name="adil_settings_submit" value="1">

                <div class="adil-settings-grid">

                    <!-- Connection -->
                    <div class="adil-card">
                        <h2 class="adil-card-title">🔗 Next.js Connection</h2>
                        <table class="form-table">
                            <tr>
                                <th><label for="adil_frontend_url">Frontend URL</label></th>
                                <td><input type="url" id="adil_frontend_url" name="adil_frontend_url" value="<?php echo esc_attr( get_option( 'adil_frontend_url', 'https://adilashrafi.com' ) ); ?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="adil_contact_email">Contact Notification Email</label></th>
                                <td><input type="email" id="adil_contact_email" name="adil_contact_email" value="<?php echo esc_attr( get_option( 'adil_contact_email', get_option( 'admin_email' ) ) ); ?>" class="regular-text"></td>
                            </tr>
                        </table>

                        <div class="adil-token-box">
                            <strong>Revalidation Token</strong>
                            <code><?php echo esc_html( $token ?: '(none yet)' ); ?></code>
                            <div class="adil-token-actions">
                                <button class="adil-btn-copy button button-secondary" data-copy="<?php echo esc_attr( $token ); ?>">Copy Token</button>
                                <form method="post" style="display:inline">
                                    <?php wp_nonce_field( 'adil_regen_token' ); ?>
                                    <input type="hidden" name="adil_regen_token" value="1">
                                    <button type="submit" class="button button-secondary">Regenerate</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Site Meta -->
                    <div class="adil-card">
                        <h2 class="adil-card-title">🧑‍💻 Site Meta & Sections</h2>
                        <table class="form-table">
                            <?php
                            $meta_sections = [
                                'Identity' => [
                                    'adil_site_title'              => 'Display Name',
                                    'adil_site_tagline'            => 'Tagline / Focus',
                                    'adil_site_bio'                => 'Bio / Hero Description (HTML allowed)',
                                    'adil_site_location'           => 'Location',
                                ],
                                'Section: About' => [
                                    'adil_about_label'             => 'Top Label',
                                    'adil_about_title'             => 'Large Title',
                                    'adil_about_accent'            => 'Accent Title (Blue)',
                                ],
                                'Bottom Hero Stats' => [
                                    'adil_stat_1_value'            => 'Stat 1 Value (e.g. 2+)',
                                    'adil_stat_1_label'            => 'Stat 1 Label',
                                    'adil_stat_2_value'            => 'Stat 2 Value (e.g. 10+)',
                                    'adil_stat_2_label'            => 'Stat 2 Label',
                                    'adil_stat_3_value'            => 'Stat 3 Value (e.g. 3)',
                                    'adil_stat_3_label'            => 'Stat 3 Label',
                                ],
                                'Section: Services' => [
                                    'adil_services_label'          => 'Top Label',
                                    'adil_services_title'          => 'Large Title',
                                    'adil_services_accent'         => 'Accent Title (Blue)',
                                ],
                                'Section: Work' => [
                                    'adil_projects_label'          => 'Top Label',
                                    'adil_projects_title'          => 'Large Title',
                                    'adil_projects_accent'         => 'Accent Title (Blue)',
                                ],
                                'Section: Resume' => [
                                    'adil_resume_label'            => 'Top Label',
                                    'adil_resume_title'            => 'Large Title',
                                    'adil_resume_accent'           => 'Accent Title (Blue)',
                                ],
                                'Section: Contact' => [
                                    'adil_contact_label'           => 'Top Label',
                                    'adil_contact_title'           => 'Large Title',
                                    'adil_contact_accent'          => 'Accent Title (Blue)',
                                ],
                                'Section: Testimonials' => [
                                    'adil_testimonials_label'      => 'Top Label',
                                    'adil_testimonials_title'      => 'Large Title',
                                    'adil_testimonials_accent'     => 'Accent Title (Blue)',
                                ],
                                'Call to Action' => [
                                    'adil_cta_primary_label'       => 'Primary Label',
                                    'adil_cta_primary_href'        => 'Primary Link',
                                    'adil_cta_secondary_label'     => 'Secondary Label',
                                    'adil_cta_secondary_href'      => 'Secondary Link',
                                ]
                            ];
                            foreach ( $meta_sections as $section_name => $fields ) :
                            ?>
                                <tr><th colspan="2" style="padding-top:20px; border-bottom:1px solid #eee;"><strong><?php echo esc_html($section_name); ?></strong></th></tr>
                                <?php foreach ( $fields as $key => $label ) : 
                                    $val = get_option( $key, '' );
                                ?>
                                <tr>
                                    <th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
                                    <td><input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text" placeholder="Enter <?php echo strtolower($label); ?>..."></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </table>
                    </div>

                </div>

                <?php submit_button( 'Save Settings', 'primary adil-save-btn', 'submit' ); ?>
            </form>
        </div>

        <script>
        document.querySelectorAll('.adil-btn-copy').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                navigator.clipboard.writeText(btn.dataset.copy).then(() => {
                    btn.textContent = 'Copied!';
                    setTimeout(() => btn.textContent = 'Copy Token', 2000);
                });
            });
        });
        </script>
        <?php
    }

    // ── API Reference ─────────────────────────────────────────────────────────

    // ── Documentation & API Reference ─────────────────────────────────────────

    public static function render_documentation(): void {
        $base = ADIL_SITE_URL . '/wp-json/' . ADIL_API_NS;
        $endpoints = [
            [ 'GET',  '/portfolio',         'Bulk fetch for homepage ISR',       [] ],
            [ 'GET',  '/projects',           'All projects with meta',            [ 'featured=1' ] ],
            [ 'GET',  '/projects/{slug}',    'Single project case study',         [] ],
            [ 'GET',  '/portfolio-clients',  'Client logos for marquee',          [] ],
            [ 'GET',  '/services',           'All services with content',         [] ],
            [ 'GET',  '/experience',         'Experience & Education',            [] ],
            [ 'GET',  '/skills',             'Skills & Proficiency',              [] ],
            [ 'GET',  '/testimonials',       'Client testimonials',               [] ],
            [ 'GET',  '/settings',           'Global site metadata',              [] ],
            [ 'POST', '/contact',            'Contact form handler',              [] ],
            [ 'POST', '/revalidate',         'Manual Next.js revalidation',       [] ],
        ];
        ?>
        <div class="wrap adil-wrap adil-doc-wrap">
            <div class="adil-header">
                <div class="adil-header-inner">
                    <div class="adil-logo">⬡ Ecosystem Documentation</div>
                    <div class="adil-header-meta">
                        <span>v<?php echo ADIL_VERSION; ?></span>
                    </div>
                </div>
            </div>

            <div class="adil-doc-grid">
                <!-- Sidebar / Navigation -->
                <div class="adil-doc-nav">
                    <ul>
                        <li><a href="#overview">System Overview</a></li>
                        <li><a href="#content">Content Management</a></li>
                        <li><a href="#revalidation">Auto-Revalidation</a></li>
                        <li><a href="#api">API Reference</a></li>
                        <li><a href="#setup">Next.js Setup</a></li>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="adil-doc-content">
                    
                    <section id="overview" class="adil-card">
                        <h2 class="adil-card-title">System Overview</h2>
                        <p>This ecosystem uses **WordPress as a Headless CMS** and **Next.js** for the frontend. WordPress handles all data entry, media management, and contact logs, while Next.js fetches this data via the REST API to build a high-performance, SEO-optimized static site.</p>
                        <div class="adil-diagram">
                            <div class="adil-node">WordPress (Backend)</div>
                            <div class="adil-arrow">── REST API ──▶</div>
                            <div class="adil-node">Next.js (Frontend)</div>
                            <div class="adil-arrow">◀── Revalidate ──</div>
                        </div>
                    </section>

                    <section id="content" class="adil-card">
                        <h2 class="adil-card-title">Content Management</h2>
                        <p>Each section of the site is mapped to a specific Custom Post Type (CPT). Use the separate menus in the sidebar to manage your content:</p>
                        <ul class="adil-list">
                            <li><strong>Projects:</strong> Portfolio items with case studies and links.</li>
                            <li><strong>Clients:</strong> Partner logos for the animated marquee (uses Featured Image).</li>
                            <li><strong>Services:</strong> The core "compounds" you offer.</li>
                            <li><strong>Experience:</strong> Controls the "Resume" work history and education timeline.</li>
                            <li><strong>Skills:</strong> Manages the proficiency bars on your resume page.</li>
                            <li><strong>Testimonials:</strong> Client quotes and avatars.</li>
                            <li><strong>Contact Inbox:</strong> View form submissions directly in WordPress.</li>
                        </ul>
                    </section>

                    <section id="revalidation" class="adil-card">
                        <h2 class="adil-card-title">Auto-Revalidation</h2>
                        <p>The plugin automatically clears the Next.js cache whenever you update content. This means your static site updates "live" without a full redeploy.</p>
                        <p><strong>How it works:</strong> When you hit "Update" on a project, WordPress sends a "ping" to <code>/api/revalidate</code> on your frontend. Make sure your <code>REVALIDATE_TOKEN</code> is synced in both environments.</p>
                    </section>

                    <section id="api" class="adil-card">
                        <h2 class="adil-card-title">API Reference</h2>
                        <p>Base Namespace: <code><?php echo esc_html( ADIL_API_NS ); ?></code></p>
                        <table class="adil-table widefat">
                            <thead>
                                <tr><th>Method</th><th>Endpoint</th><th>Description</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $endpoints as [$method, $path, $desc, $params] ) : ?>
                                <tr>
                                    <td><span class="adil-badge adil-badge--<?php echo $method === 'GET' ? 'blue' : 'orange'; ?>"><?php echo esc_html( $method ); ?></span></td>
                                    <td><code><?php echo esc_html( $path ); ?></code></td>
                                    <td><?php echo esc_html( $desc ); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>

                    <section id="setup" class="adil-card">
                        <h2 class="adil-card-title">Next.js Setup</h2>
                        <p>Ensure your frontend <code>.env.local</code> contains the following:</p>
                        <pre class="adil-code">NEXT_PUBLIC_WP_API=<?php echo esc_html( $base ); ?>
REVALIDATE_TOKEN=<?php echo esc_html( get_option( 'adil_revalidate_token', '...' ) ); ?></pre>
                    </section>

                </div>
            </div>
        </div>
        <?php
    }

    // ── Settings saved notice ─────────────────────────────────────────────────

    public static function settings_saved_notice(): void {
        // notices are handled inline via settings_errors()
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function get_unread_count(): int {
        $posts = get_posts( [
            'post_type'      => 'adil_contact_log',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [ [ 'key' => 'adil_contact_read', 'value' => '', 'compare' => 'IN' ] ],
            'fields'         => 'ids',
        ] );
        return count( $posts );
    }
}
