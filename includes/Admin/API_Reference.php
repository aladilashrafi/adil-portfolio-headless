<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class API_Reference {
    public static function render(): void {
        $ns = HPCMS_API_NS;
        $base = rest_url( $ns );
        ?>
        <div class="wrap hpcms-admin-wrap">
            <header class="hpcms-admin-header">
                <h1><?php esc_html_e( 'API Reference', 'headless-portfolio-cms' ); ?></h1>
                <p><?php esc_html_e( 'Endpoints available for your frontend application.', 'headless-portfolio-cms' ); ?></p>
            </header>

            <div class="hpcms-api-docs">
                <div class="hpcms-card">
                    <h3>Base URL</h3>
                    <code><?php echo esc_url( $base ); ?></code>
                </div>

                <div class="hpcms-card" style="margin-top: 20px;">
                    <h3>Endpoints</h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Entity</th>
                                <th>Endpoint</th>
                                <th>Parameters</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Projects</strong></td>
                                <td><code>/projects</code><br><code>/projects/{slug}</code></td>
                                <td><code>page</code>, <code>per_page</code>, <code>technology</code>, <code>category</code>, <code>industry</code>, <code>featured</code>, <code>sort</code></td>
                            </tr>
                            <tr>
                                <td><strong>Experience</strong></td>
                                <td><code>/experience</code><br><code>/experience/{slug}</code></td>
                                <td><code>page</code>, <code>per_page</code></td>
                            </tr>
                            <tr>
                                <td><strong>Education</strong></td>
                                <td><code>/education</code><br><code>/education/{slug}</code></td>
                                <td><code>page</code>, <code>per_page</code></td>
                            </tr>
                            <tr>
                                <td><strong>Resume</strong></td>
                                <td><code>/resume</code></td>
                                <td>None</td>
                            </tr>
                            <tr>
                                <td><strong>Skills</strong></td>
                                <td><code>/skills</code><br><code>/skills/{slug}</code></td>
                                <td><code>category</code>, <code>page</code>, <code>per_page</code></td>
                            </tr>
                            <tr>
                                <td><strong>Testimonials</strong></td>
                                <td><code>/testimonials</code></td>
                                <td>None</td>
                            </tr>
                            <tr>
                                <td><strong>Profile</strong></td>
                                <td><code>/profile</code></td>
                                <td>None</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
}
