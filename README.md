# Adil Portfolio — Headless CMS

> **Version 1.2.0** — A purpose-built WordPress plugin that serves as the content backend for the Next.js portfolio at [adilashrafi.com](https://adilashrafi.com). Registers Custom Post Types, meta fields, a full REST API, CORS handling, contact form processing, auto-revalidation of the Next.js frontend, and a polished admin dashboard.

**Plugin Name:** Adil Portfolio — Headless CMS  
**API Namespace:** `adil/v1`  
**API Base URL:** `https://api.adilashrafi.com/wp-json/adil/v1`  
**Requires WordPress:** 6.0+  
**Requires PHP:** 8.0+  
**License:** GPL-2.0-or-later

---

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Installation](#installation)
- [Plugin Structure](#plugin-structure)
- [Custom Post Types](#custom-post-types)
- [Meta Fields Reference](#meta-fields-reference)
- [REST API Reference](#rest-api-reference)
- [Contact Form System](#contact-form-system)
- [Auto-Revalidation](#auto-revalidation)
- [CORS Configuration](#cors-configuration)
- [Admin Interface](#admin-interface)
- [Plugin Settings](#plugin-settings)
- [Uninstall Behaviour](#uninstall-behaviour)
- [Pairing with the Next.js Frontend](#pairing-with-the-nextjs-frontend)
- [Contributing](#contributing)
- [Author](#author)

---

## Overview

This WordPress plugin turns a standard WordPress installation into a **headless CMS** specifically built to power the [adil-portfolio](https://github.com/aladilashrafi/adil-portfolio) Next.js frontend. It does not render any public-facing WordPress pages — instead it exposes all portfolio data through a custom REST API and keeps the Next.js frontend in sync automatically via on-demand ISR revalidation.

Key responsibilities of this plugin:

- Register all portfolio **Custom Post Types** (Projects, Services, Experience, Skills, Testimonials, Clients, Contact Log)
- Expose structured **REST API endpoints** under the `adil/v1` namespace
- Handle **contact form submissions** with rate limiting, validation, admin notification emails, and auto-replies
- **Automatically ping the Next.js revalidation endpoint** whenever portfolio content is published or updated
- Manage **CORS headers** so the Next.js frontend (and local development) can safely consume the API
- Provide a **custom admin dashboard** with a content overview, settings page, and API reference

---

## Architecture

```
WordPress Admin (WP Dashboard)
        │
        │  Editor creates/updates content
        ▼
Adil Portfolio Plugin
        │
        ├── Registers CPTs + meta boxes
        ├── Exposes REST API  ──────────────────────▶ Next.js frontend (ISR fetch)
        ├── Handles contact form POSTs
        ├── Sends admin email + auto-reply
        └── On save_post → pings Next.js /api/revalidate (fire-and-forget)
                                │
                                ▼
                    Next.js revalidatePath() purges stale cache
```

The plugin is intentionally **stateless from the frontend's perspective** — the Next.js app treats WordPress purely as a data source. WordPress is not responsible for rendering any HTML.

---

## Installation

### Option A — Upload ZIP (Recommended)

1. Download `adil-portfolio-headless.zip` from this repository.
2. In your WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP file and click **Install Now**.
4. Click **Activate Plugin**.

### Option B — Manual (FTP / SSH)

1. Clone or download this repository.
2. Copy the `adil-portfolio-headless` folder into your WordPress `wp-content/plugins/` directory.
3. In your WordPress admin, go to **Plugins** and activate **Adil Portfolio — Headless CMS**.

### Post-Activation

On first activation the plugin will:

- Register all Custom Post Types and flush rewrite rules
- **Auto-generate a secure 48-character revalidation token** (stored in `wp_options` as `adil_revalidate_token`)

Copy this token from **Portfolio CMS → Dashboard** and add it to your Next.js `.env.local`:

```env
REVALIDATE_TOKEN=<your-generated-token>
```

Then configure the plugin settings under **Portfolio CMS → Settings** (see [Plugin Settings](#plugin-settings)).

---

## Plugin Structure

```
adil-portfolio-headless/
├── adil-portfolio-headless.php   # Plugin entry point — constants, autoload, bootstrap hooks
├── uninstall.php                 # Cleanup on plugin deletion
├── adil-portfolio-headless.zip   # Distributable zip for WP upload
├── admin/
│   └── class-admin-ui.php        # Admin dashboard, settings page, documentation page
├── assets/
│   └── css/
│       └── admin.css             # Styles for the custom admin UI
└── includes/
    ├── class-post-types.php      # Registers all Custom Post Types and taxonomies
    ├── class-meta-fields.php     # Registers meta boxes, REST-exposed meta fields, save_post handler
    ├── class-rest-api.php        # All REST endpoint callbacks + auto-revalidation on save_post
    ├── class-settings.php        # Plugin settings registration + get_all() helper
    ├── class-cors.php            # CORS header injection + OPTIONS preflight handling
    ├── class-contact-handler.php # Contact form: rate limiting, validation, email notifications
    └── class-contact-log.php     # Contact Log CPT: save to WP inbox, admin list columns
```

### Bootstrap Order

The plugin bootstraps via the `plugins_loaded` action, initialising each class in order:

```
Adil_Post_Types → Adil_Meta_Fields → Adil_Settings → Adil_CORS
→ Adil_Contact_Log → Adil_REST_API → Adil_Admin_UI
```

---

## Custom Post Types

All CPTs are registered under the `adil_` prefix. They appear as separate top-level menu items in the WordPress admin.

| CPT Slug | Label | Public | REST Base | Supports |
|---|---|---|---|---|
| `adil_project` | Projects | Yes | `portfolio-projects` | title, editor, thumbnail, page-attributes, excerpt |
| `adil_service` | Services | Yes | `portfolio-services` | title, editor, page-attributes, thumbnail |
| `adil_experience` | Experience | No | `portfolio-experience` | title, editor, page-attributes |
| `adil_skill` | Skills | No | `portfolio-skills` | title, page-attributes |
| `adil_testimonial` | Testimonials | No | `portfolio-testimonials` | title, editor, thumbnail, page-attributes |
| `adil_client` | Clients | No | `portfolio-clients` | title, thumbnail, page-attributes |
| `adil_contact_log` | Contact Inbox | No | *(not REST-exposed)* | title |

> **Ordering:** All CPTs use WordPress's native `menu_order` (Page Attributes → Order) for sort order. Lower numbers appear first.

### Taxonomy

| Taxonomy | Label | Applies to |
|---|---|---|
| `adil_project_cat` | Project Category | `adil_project` |

---

## Meta Fields Reference

All meta fields are registered with `show_in_rest: true` (except contact log fields, which are private). They appear both in the Gutenberg sidebar and as classic meta boxes.

### Project (`adil_project`)

| Meta Key | Type | Description | Example |
|---|---|---|---|
| `adil_badge` | string | Category/badge label shown on card | `Plugin · WooCommerce` |
| `adil_url` | string | Live project URL | `https://banglatrack.com` |
| `adil_status` | string | `live` or `development` | `live` |
| `adil_featured` | boolean | Whether to include in featured query | `true` |
| `adil_tech_tags` | string | Comma-separated technology tags | `WooCommerce, PHP, WordPress` |
| `adil_role` | string | Your role on the project | `Lead Strategist` |
| `adil_timeline` | string | Project duration | `2023 - Present` |

> **Content field:** The main WordPress editor content is used as the project's long-form case study HTML (passed through `apply_filters('the_content', ...)` in the API response).

### Service (`adil_service`)

| Meta Key | Type | Description | Example |
|---|---|---|---|
| `adil_num` | string | Display number label | `01` |
| `adil_icon` | string | Unicode icon character | `⬡` |

> **Content field:** The editor content is used as the service description.

### Experience (`adil_experience`)

| Meta Key | Type | Description | Example |
|---|---|---|---|
| `adil_period` | string | Date range | `Mar 2025 – Present` |
| `adil_company` | string | Company or institution name | `Mediusware Limited` |
| `adil_type` | string | `work` or `education` | `work` |

> **Title field:** Used as the role/job title. **Content field:** Used as the description/summary.

### Skill (`adil_skill`)

| Meta Key | Type | Description | Example |
|---|---|---|---|
| `adil_percentage` | integer | Proficiency level (0–100) | `90` |
| `adil_category` | string | Skill category grouping | `core`, `marketing` |

> **Title field:** Used as the skill name.

### Testimonial (`adil_testimonial`)

| Meta Key | Type | Description | Example |
|---|---|---|---|
| `adil_author` | string | Client's full name | `John Smith` |
| `adil_title` | string | Client's job title | `E-commerce Manager` |
| `adil_company` | string | Client's company | `Gulf Coast Marine Outfitters` |
| `adil_avatar_url` | string | Avatar image URL (fallback to featured image) | `https://...` |

> **Content field:** Used as the testimonial quote text.

### Client (`adil_client`)

| Field | Source | Description |
|---|---|---|
| `name` | Post title | Client/brand name |
| `logo` | Featured image | Client logo (full size) |

### Contact Log (`adil_contact_log`)

Stored in WordPress and **not exposed via REST API**. Fields (all private):

`adil_contact_name`, `adil_contact_email`, `adil_contact_subject`, `adil_contact_message`, `adil_contact_budget`, `adil_contact_ip`, `adil_contact_read`

---

## REST API Reference

**Base URL:** `https://api.adilashrafi.com/wp-json/adil/v1`

All endpoints are publicly accessible (no authentication required for `GET` requests). Responses are JSON.

---

### `GET /portfolio`

Returns all content in a single request — designed for the Next.js homepage ISR fetch to avoid multiple round-trips.

**Response shape:**
```json
{
  "projects":     [ ...Project[] ],
  "services":     [ ...Service[] ],
  "experience":   [ ...ExperienceItem[] ],
  "skills":       [ ...Skill[] ],
  "testimonials": [ ...Testimonial[] ],
  "clients":      [ ...Client[] ]
}
```

---

### `GET /projects`

Returns all published projects, ordered by `menu_order ASC`.

**Query Parameters:**

| Parameter | Type | Description |
|---|---|---|
| `featured` | `1` | If present, returns only projects with `adil_featured = true` |

**Example:** `GET /projects?featured=1`

**Single project shape:**
```json
{
  "id":          123,
  "slug":        "bangla-track",
  "name":        "Bangla Track",
  "description": "Plain text excerpt...",
  "content":     "<p>Full HTML content...</p>",
  "badge":       "SaaS · Bangladesh",
  "url":         "https://banglatrack.com",
  "status":      "live",
  "featured":    true,
  "tech_tags":   ["WooCommerce", "PHP", "WordPress"],
  "image_url":   "https://api.adilashrafi.com/wp-content/uploads/...",
  "order":       1,
  "role":        "Founder & Lead Developer",
  "timeline":    "2023 - Present",
  "categories":  [{ "id": 5, "name": "SaaS", "slug": "saas" }]
}
```

---

### `GET /projects/{slug}`

Returns a single project by its URL slug.

**Returns:** Single project object (same shape as above)  
**404:** `{ "code": "not_found", "message": "Project not found." }` with HTTP 404

---

### `GET /services`

Returns all published services, ordered by `menu_order ASC`.

**Single service shape:**
```json
{
  "id":          10,
  "slug":        "seo-strategy",
  "num":         "01",
  "icon":        "⬡",
  "name":        "SEO Strategy",
  "description": "Plain text...",
  "content":     "<p>Full HTML...</p>",
  "order":       1
}
```

---

### `GET /experience`

Returns all published experience items, ordered by `menu_order ASC`.

**Single experience shape:**
```json
{
  "id":          20,
  "period":      "Mar 2025 – Present",
  "role":        "Digital Marketing Lead",
  "company":     "Mediusware Limited",
  "description": "Responsible for...",
  "type":        "work",
  "order":       1
}
```

---

### `GET /skills`

Returns all published skills, ordered by `menu_order ASC`.

**Single skill shape:**
```json
{
  "id":         30,
  "name":       "SEO & Content Strategy",
  "percentage": 92,
  "category":   "core",
  "order":      1
}
```

---

### `GET /testimonials`

Returns all published testimonials, ordered by `menu_order ASC`.

**Single testimonial shape:**
```json
{
  "id":         40,
  "quote":      "Adil transformed our digital presence...",
  "author":     "John Smith",
  "title":      "E-commerce Manager",
  "company":    "Gulf Coast Marine Outfitters",
  "avatar_url": "https://..."
}
```

---

### `POST /contact`

Handles contact form submissions.

**Request body (JSON):**
```json
{
  "name":    "Jane Doe",
  "email":   "jane@example.com",
  "subject": "Project Inquiry",
  "message": "I'd love to work together on...",
  "budget":  "$1,000 – $5,000"
}
```

**Required fields:** `name`, `email`, `subject`, `message` (minimum 10 characters)

**Success response (HTTP 200):**
```json
{
  "success": true,
  "message": "Thanks Jane! Your message has been received. I'll get back to you shortly."
}
```

**Error responses:**

| HTTP Status | Cause |
|---|---|
| 422 | Validation failed (missing required fields, invalid email, short message) |
| 429 | Rate limit exceeded (5 submissions per IP per hour) |
| 500 | Could not save to WordPress inbox |

---

### `POST /revalidate`

Triggers a Next.js ISR revalidation for a given path. This endpoint is typically called internally by the plugin's auto-revalidation system, but can also be called externally.

**Request body (JSON):**
```json
{
  "secret": "your-revalidate-token",
  "path":   "/projects/bangla-track"
}
```

**Success response (HTTP 200):**
```json
{
  "revalidated": true,
  "path": "/projects/bangla-track"
}
```

**Error (HTTP 403):** Invalid or missing token.

---

## Contact Form System

The contact form pipeline is handled by `Adil_Contact_Handler` and `Adil_Contact_Log`:

1. **Rate limiting** — Each IP address is limited to **5 submissions per hour**, enforced via WordPress transients. Exceeding this returns HTTP 429.
2. **Validation** — All four required fields are checked. Email is validated with `is_email()`. Message must be at least 10 characters.
3. **Save to inbox** — Valid submissions are saved as `adil_contact_log` posts in the WordPress admin, visible under **Contact Inbox** with columns for Name, Email, Budget, Read Status, and Date.
4. **Admin notification email** — Sent to the address configured in Settings (falls back to the WordPress admin email), with a `Reply-To` header pointing to the submitter.
5. **Auto-reply to submitter** — A confirmation email is sent to the person who filled out the form, including a copy of their message and links to the portfolio.

**Read tracking:** New submissions are marked **● New** in the admin inbox. Opening and editing the post allows manually tracking read status via the `adil_contact_read` meta field.

---

## Auto-Revalidation

Whenever a portfolio post is **published or updated** in WordPress, the plugin automatically fires a `POST /api/revalidate` request to the Next.js frontend. This keeps the live site in sync without waiting for the 1-hour ISR window to expire.

### Path mapping

The plugin knows which Next.js paths each CPT affects:

| CPT | Paths Revalidated |
|---|---|
| `adil_project` | `/`, `/projects`, `/projects/{slug}` |
| `adil_service` | `/` |
| `adil_experience` | `/`, `/resume` |
| `adil_skill` | `/`, `/resume` |
| `adil_testimonial` | `/` |

**Conditions for revalidation:**
- Post is not a revision or autosave
- Post status is `publish`
- Post type is one of the mapped CPTs above

**Implementation:** Uses `wp_remote_post` with `blocking: false` (fire-and-forget). The 8-second timeout means a slow Next.js response will not block the WordPress save action.

**Prerequisite:** `adil_revalidate_token` must be set in plugin settings. If it's empty, revalidation is skipped silently.

---

## CORS Configuration

`Adil_CORS` manages cross-origin access so the Next.js frontend (and local development environment) can call the API from the browser.

**Allowed origins (dynamic):**
- The configured frontend URL from plugin settings (e.g. `https://adilashrafi.com`)
- `https://adilashrafi.com` (hardcoded fallback)
- `http://localhost:3000` (Next.js local dev)
- `http://localhost:3001`

**Headers set on all `adil/v1` responses:**
```
Access-Control-Allow-Origin: <matched origin>
Access-Control-Allow-Methods: GET, POST, OPTIONS
Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce
Access-Control-Allow-Credentials: true
Vary: Origin
```

**Preflight handling:** `OPTIONS` requests are intercepted before WordPress routing, and a `204 No Content` response is returned immediately with `Access-Control-Max-Age: 86400` (24-hour preflight cache).

To allow a new origin, add it to the `allowed_origins()` array in `includes/class-cors.php`.

---

## Admin Interface

The plugin adds a **Portfolio CMS** top-level menu (with the analytics dashicon) containing three sub-pages:

### Dashboard

A visual overview of all portfolio content with:

- **Content counters** — Published count for Projects, Services, Experience, Skills, Testimonials, and unread Contact messages (highlighted in orange if non-zero)
- **API Status card** — Shows the live API base URL and the revalidation token (with a one-click Copy button), plus a hint to add it to `.env.local`
- **Quick Add shortcuts** — One-click links to create new posts for each CPT

### Settings

A settings form (registered under `adil_settings_group`) with fields for:

- Frontend URL (used for CORS and revalidation pings)
- Contact notification email address
- Revalidation token (auto-generated on activation, can be regenerated here)
- Site identity (title, tagline, bio, location, email, LinkedIn, availability)
- Hero statistics (ROAS, ventures, and three bottom stats with labels and values)
- Section header copy (label, title, accent for each section)
- CTA button labels and URLs

### Documentation

An in-admin API reference page listing all endpoints, their methods, and example responses — useful for development without leaving the WordPress admin.

---

## Plugin Settings

All settings are stored in `wp_options` and managed via the **Portfolio CMS → Settings** page.

| Option Key | Description | Default |
|---|---|---|
| `adil_frontend_url` | Next.js frontend URL | `https://adilashrafi.com` |
| `adil_contact_email` | Email for admin notifications | WordPress admin email |
| `adil_revalidate_token` | Shared secret for ISR webhook | *(auto-generated on activation)* |
| `adil_site_title` | Site owner name | `Al Adil Ashrafi` |
| `adil_site_tagline` | Tagline | `The Marketing Alchemist` |
| `adil_site_bio` | Bio (HTML allowed) | *(empty)* |
| `adil_site_location` | Location | `Mohammadpur, Dhaka, Bangladesh` |
| `adil_site_email` | Public contact email | `hello@adilashrafi.com` |
| `adil_site_linkedin` | LinkedIn profile URL | *(Adil's LinkedIn)* |
| `adil_site_availability` | Availability status string | `Available for Freelance` |
| `adil_hero_stat_roas` | Hero ROAS stat value | `6.5×` |
| `adil_hero_stat_ventures` | Hero ventures count | `3` |
| `adil_stat_1/2/3_label` | Bottom hero stat labels | `Years Leading Teams`, etc. |
| `adil_stat_1/2/3_value` | Bottom hero stat values | `2+`, `10+`, `3` |
| `adil_*_label/title/accent` | Section header copy per section | *(see Settings page)* |
| `adil_cta_primary/secondary_*` | Hero CTA button labels and URLs | *(see Settings page)* |

---

## Uninstall Behaviour

When the plugin is **deleted** (not just deactivated) from **Plugins → Delete**, `uninstall.php` runs and:

1. **Deletes all plugin options** from `wp_options` (all `adil_*` keys including the revalidation token)
2. **Permanently deletes** all CPT posts and their meta for: `adil_project`, `adil_service`, `adil_experience`, `adil_skill`, `adil_testimonial`, `adil_contact_log`
3. **Flushes rewrite rules**

> ⚠️ **Warning:** Deletion is irreversible. Export or back up your portfolio content before deleting the plugin.

Deactivating the plugin (without deleting) preserves all data.

---

## Pairing with the Next.js Frontend

This plugin is designed to work together with the [adil-portfolio](https://github.com/aladilashrafi/adil-portfolio) Next.js repository. Here is the full setup checklist:

**On the WordPress (this plugin) side:**
1. Install and activate the plugin
2. Set **Frontend URL** in Settings to your Next.js domain (e.g. `https://adilashrafi.com`)
3. Copy the **Revalidation Token** from the Dashboard

**On the Next.js side:**
1. In `.env.local`, set:
   ```env
   NEXT_PUBLIC_WP_API=https://your-wordpress.com/wp-json/adil/v1
   REVALIDATE_TOKEN=<paste token here>
   ```
2. In `next.config.js`, ensure your WordPress domain is listed under `images.remotePatterns`

**Revalidation flow:**
```
Editor publishes/updates content in WP
        ↓
Plugin's save_post hook fires
        ↓
ping_nextjs_revalidation() → POST {frontend}/api/revalidate
        ↓
Next.js revalidatePath() purges cached page
        ↓
Next visitor gets freshly generated page
```

---

## Contributing

1. Fork this repository
2. Create a feature branch: `git checkout -b feature/your-change`
3. Make your changes and test against a local WordPress install (WordPress 6.0+, PHP 8.0+)
4. Commit: `git commit -m "feat: describe your change"`
5. Push and open a Pull Request against `main`

Please include a description of your changes and any relevant test steps.

---

## Author

**Al Adil Ashrafi**

- Website: [adilashrafi.com](https://adilashrafi.com)
- Email: [hello@adilashrafi.com](mailto:hello@adilashrafi.com)
- LinkedIn: [linkedin.com/in/al-adil-ashrafi](https://linkedin.com/in/al-adil-ashrafi/)
- GitHub: [@aladilashrafi](https://github.com/aladilashrafi)
- Markimist: [markimist.com](https://markimist.com)
- Bangla Track: [banglatrack.com](https://banglatrack.com)
