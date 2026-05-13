<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class Documentation {
    public static function render(): void {
        $ns = HPCMS_API_NS;
        $base = rest_url( $ns );
        ?>
        <div class="wrap hpcms-admin-wrap">
            <header class="hpcms-admin-header">
                <h1><?php esc_html_e( 'Frontend Integration Guide', 'headless-portfolio-cms' ); ?></h1>
                <p><?php esc_html_e( 'End-to-end documentation on integrating APIs and data with your frontend.', 'headless-portfolio-cms' ); ?></p>
            </header>

            <div class="hpcms-api-docs">
                <div class="hpcms-card">
                    <h3>1. Setup & Environment</h3>
                    <p>In your frontend application (e.g., Next.js, React, or Vue), you need to define the base URL for the WordPress REST API.</p>
                    <p>Create an <code>.env.local</code> file in your frontend root directory and add:</p>
                    <pre><code>NEXT_PUBLIC_WP_API_URL=<?php echo esc_url( $base ); ?></code></pre>
                </div>

                <div class="hpcms-card" style="margin-top: 20px;">
                    <h3>2. Fetching Data</h3>
                    <p>Create a utility function to fetch data from the REST API endpoints. Since this API is read-only (except for the contact form), you don't need authentication for GET requests.</p>
                    <pre><code>export async function fetchAPI(endpoint: string, options = {}) {
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json',
    },
    // Optional: add next: { revalidate: 3600 } for Next.js ISR
  };

  const mergedOptions = { ...defaultOptions, ...options };
  const res = await fetch(`${process.env.NEXT_PUBLIC_WP_API_URL}${endpoint}`, mergedOptions);

  if (!res.ok) {
    throw new Error(`API Error: ${res.status}`);
  }

  return res.json();
}</code></pre>
                </div>

                <div class="hpcms-card" style="margin-top: 20px;">
                    <h3>3. Integrating Specific Endpoints</h3>
                    
                    <h4 style="margin-top: 15px;">Projects</h4>
                    <p>Fetch all projects (supports pagination, filtering by taxonomy):</p>
                    <pre><code>const projects = await fetchAPI('/projects?per_page=10&page=1');</code></pre>
                    <p>Project objects include custom fields like <code>keyResults</code>, <code>techStack</code>, and taxonomies (<code>industries</code>, <code>technologies</code>).</p>

                    <h4 style="margin-top: 15px;">Profile & Global Settings</h4>
                    <p>The profile endpoint aggregates global settings like SEO, Social Links, and Resume URL.</p>
                    <pre><code>const profile = await fetchAPI('/profile');</code></pre>

                    <h4 style="margin-top: 15px;">Submitting Contact Forms</h4>
                    <p>The contact endpoint requires a POST request with specific JSON fields. It includes built-in rate limiting.</p>
                    <pre><code>const submitContactForm = async (formData) => {
  return await fetchAPI('/contact', {
    method: 'POST',
    body: JSON.stringify({
      name: formData.name,
      email: formData.email,
      subject: formData.subject || 'Website Inquiry',
      message: formData.message,
      budget: formData.budget
    })
  });
}</code></pre>
                </div>

                <div class="hpcms-card" style="margin-top: 20px;">
                    <h3>4. Handling SVGs & HTML Content</h3>
                    <p>Some endpoints return HTML (like the <code>content</code> field) or raw SVGs (like <code>icon</code> in Services).</p>
                    <p>In React/Next.js, you must use <code>dangerouslySetInnerHTML</code> to render these securely.</p>
                    <pre><code>&lt;div dangerouslySetInnerHTML={{ __html: service.icon }} /&gt;</code></pre>
                    <p><strong>Note:</strong> The CMS safely sanitizes SVGs on the backend using a custom whitelist, making them safe to render.</p>
                </div>
            </div>
        </div>
        <?php
    }
}
