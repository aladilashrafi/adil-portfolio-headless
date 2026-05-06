# Adil Portfolio — Headless WordPress CMS Plugin

**Version:** 1.2.0  
**Author:** Al Adil Ashrafi  
**Requires WordPress:** 6.0+  
**Requires PHP:** 8.0+

---

## Overview

A purpose-built headless WordPress plugin that powers the Next.js portfolio at **adilashrafi.com**. It registers all custom post types, meta fields, REST API endpoints, CORS headers, and an admin UI — everything WordPress needs to act as a headless CMS for a server-side rendered Next.js frontend.

---

## Installation

1. Upload the `adil-portfolio-headless` folder to `/wp-content/plugins/`
2. Activate the plugin from **Plugins → Installed Plugins**
3. Go to **Portfolio CMS → Settings** and enter your Next.js frontend URL
4. Copy the **Revalidation Token** into your Next.js `.env.local` as `REVALIDATE_TOKEN`

---

## Custom Post Types

| Post Type | REST Base | Description |
|---|---|---|
| `adil_project` | `/portfolio-projects` | Portfolio projects (with categories) |
| `adil_service` | `/portfolio-services` | Services offered (separate page ready) |
| `adil_experience` | `/portfolio-experience` | Work history |
| `adil_skill` | `/portfolio-skills` | Skills with percentage |
| `adil_testimonial` | `/portfolio-testimonials` | Client testimonials |
| `adil_contact_log` | *(private)* | Contact form inbox |

---

## Admin Structure

The plugin now provides a **separate top-level menu** for every post type in the WordPress sidebar for better organization. The **Portfolio CMS** menu is reserved for global settings, the dashboard, and the API reference.

---

## REST API Endpoints

Base URL: `https://your-wordpress.com/wp-json/adil/v1`

| Method | Endpoint | Description |
|---|---|---|
| GET | `/portfolio` | All data in one request (SSG) |
| GET | `/projects` | All projects (`?featured=1` for featured only) |
| GET | `/projects/{slug}` | Single project by slug |
| GET | `/services` | All services |
| GET | `/experience` | All experience entries |
| GET | `/skills` | All skills |
| GET | `/testimonials` | All testimonials |
| GET | `/settings` | Global site meta |
| POST | `/contact` | Contact form submission |
| POST | `/revalidate` | Trigger Next.js ISR revalidation |

### Project Response Shape

The `/projects` and `/projects/{slug}` endpoints return:

```json
{
  "id": 1,
  "slug": "bangla-track",
  "name": "Bangla Track",
  "description": "Plain text excerpt for cards...",
  "content": "<p>Rich HTML content for case study page...</p>",
  "badge": "Plugin · WooCommerce · Bangladesh",
  "url": "https://banglatrack.com",
  "status": "live",
  "featured": true,
  "tech_tags": ["WooCommerce", "PHP", "WordPress"],
  "image_url": "https://...",
  "order": 1,
  "role": "Lead Developer",
  "timeline": "2024 - Present"
}
```

- `description` — Plain text (HTML stripped) for preview cards
- `content` — Full HTML (via `the_content` filter) for case study detail pages
- `role` — Your role in the project (optional)
- `timeline` — Project timeline (optional)

---

## Next.js Integration

### `.env.local`
```env
NEXT_PUBLIC_WP_API=https://your-wordpress.com/wp-json/adil/v1
REVALIDATE_TOKEN=your-token-from-plugin-settings
```

### `lib/api.ts`
```ts
const WP_API = process.env.NEXT_PUBLIC_WP_API;

export async function getPortfolioData() {
  const res = await fetch(`${WP_API}/portfolio`, {
    next: { revalidate: 3600 }
  });
  return res.json();
}

export const getProjects     = () => fetch(`${WP_API}/projects`).then(r => r.json());
export const getServices     = () => fetch(`${WP_API}/services`).then(r => r.json());
export const getExperience   = () => fetch(`${WP_API}/experience`).then(r => r.json());
export const getSkills       = () => fetch(`${WP_API}/skills`).then(r => r.json());
export const getTestimonials = () => fetch(`${WP_API}/testimonials`).then(r => r.json());
```

### `app/page.tsx` (App Router SSG)
```ts
import { getPortfolioData } from '@/lib/api';

export const revalidate = 3600; // ISR every hour

export default async function HomePage() {
  const { projects, services, experience, skills, meta } = await getPortfolioData();
  // ...render
}
```

### `app/api/revalidate/route.ts`
```ts
import { NextRequest, NextResponse } from 'next/server';
import { revalidatePath } from 'next/cache';

export async function POST(req: NextRequest) {
  const { path, secret } = await req.json();
  if (secret !== process.env.REVALIDATE_TOKEN) {
    return NextResponse.json({ error: 'Invalid token' }, { status: 401 });
  }
  revalidatePath(path);
  return NextResponse.json({ revalidated: true, path });
}
```

---

## Auto-Revalidation

When you **publish or update** any portfolio content in WordPress, the plugin automatically fires a POST request to your Next.js `/api/revalidate` endpoint — keeping the static site fresh without a full rebuild.

Pages revalidated automatically:
- `/` (homepage)
- `/projects`
- `/resume`
- `/projects/[slug]` (for individual project updates)

---

## Contact Form

The `/contact` endpoint accepts:

```json
{
  "name": "Client Name",
  "email": "client@email.com",
  "subject": "Project Inquiry",
  "message": "I'd like to work with you on...",
  "budget": "$500–$1000"
}
```

- Sends email notification to your configured contact email
- Sends auto-reply to the submitter
- Saves submission to the **Inbox** in WP admin
- Rate limited to **5 requests per IP per hour**

---

## File Structure

```
adil-portfolio-headless/
├── adil-portfolio-headless.php   # Main plugin entry point
├── uninstall.php                 # Cleanup on deletion
├── README.md
├── includes/
│   ├── class-post-types.php      # CPT + taxonomy registration
│   ├── class-meta-fields.php     # Meta boxes + REST meta
│   ├── class-rest-api.php        # All REST endpoints
│   ├── class-cors.php            # CORS headers for Next.js
│   ├── class-contact-handler.php # Contact form logic
│   ├── class-contact-log.php     # Contact log CPT
│   └── class-settings.php       # Plugin settings
├── admin/
│   └── class-admin-ui.php        # Dashboard, settings, API reference
└── assets/
    └── css/
        └── admin.css             # Admin UI styles
```
