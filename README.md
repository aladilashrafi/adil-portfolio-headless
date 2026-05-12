# Headless Portfolio CMS

## API-First Headless Content Platform for Developers

> **Version 1.0.0** — A complete headless developer portfolio content platform powered by WordPress. Manage structured portfolio data and expose it through clean REST APIs for modern frontend frameworks like Next.js, React, Astro, Nuxt, and Gatsby.

**Plugin Name:** Headless Portfolio CMS  
**API Namespace:** `hpcms/v1`  
**API Base URL:** `https://your-site.com/wp-json/hpcms/v1`  
**Requires WordPress:** 6.5+  
**Requires PHP:** 8.1+  
**License:** GPLv2 or later

---

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Plugin Structure](#plugin-structure)
- [Content Architecture](#content-architecture)
- [REST API Reference](#rest-api-reference)
- [Security & Performance](#security--performance)
- [Admin UX](#admin-ux)
- [Uninstall Cleanup](#uninstall-cleanup)
- [Author](#author)

---

## Overview

This plugin is not merely a portfolio showcase plugin or a theme addon; it is a **structured headless content platform**. It allows professionals, agencies, and developers to use WordPress as a powerful backend while maintaining full control over the frontend using modern JAMstack tools.

---

## Architecture

The CMS is built around 6 core entities:
1. **Projects**: Case studies, client work, and applications.
2. **Experience**: Job history, freelance work, and internships.
3. **Education**: Academic history, certifications, and training.
4. **Resume**: Downloadable resume versions and files.
5. **Skills**: Proficiency levels, icons, and skill categories.
6. **Testimonials**: Client reviews and endorsements.

---

## Plugin Structure

```txt
headless-portfolio-cms/
├── admin/                 # Admin UI and dashboard classes
├── assets/                # Admin CSS and JS assets
├── includes/              # Core logic: Post types, Meta fields, API, Settings
├── languages/             # Translation files
├── headless-portfolio-cms.php  # Main bootstrap file
└── uninstall.php          # Deep cleanup routine
```

---

## Content Architecture

### 1. Projects (`hpcms_project`)
- **Meta Fields:** Client Name, Project URL, GitHub URL, Completion Date, Tech Stack, Gallery, SEO Title/Description.
- **Taxonomies:** `hpcms_project_category`, `hpcms_technology`, `hpcms_industry`.

### 2. Experience (`hpcms_experience`)
- **Meta Fields:** Company Name, Position, Employment Type, Dates, Location, Company URL.

### 3. Education (`hpcms_education`)
- **Meta Fields:** Institution, Degree, Field of Study, Dates, Grade, Certificate URL.

### 4. Resume (`hpcms_resume`)
- **Meta Fields:** Resume File, Version, Resume Type (Developer, Designer, etc.), Last Updated.

### 5. Skills (`hpcms_skill`)
- **Meta Fields:** Skill Level, Icon, Experience Years, Official URL.
- **Taxonomy:** `hpcms_skill_category`.

### 6. Testimonials (`hpcms_testimonial`)
- **Meta Fields:** Client Name/Position, Company, Rating, Client Image.

---

## REST API Reference

**Namespace:** `hpcms/v1`

| Endpoint | Method | Description |
|---|---|---|
| `/projects` | GET | List projects with pagination and filtering. |
| `/projects/{slug}` | GET | Fetch a single project case study. |
| `/experience` | GET | List work history. |
| `/education` | GET | List academic history. |
| `/resume` | GET | List available resume versions. |
| `/skills` | GET | List skills with proficiency levels. |
| `/testimonials` | GET | List client reviews. |
| `/profile` | GET | Global settings (Bio, Social Links, SEO). |

### API Features
- **Pagination:** `/projects?page=1&per_page=10`
- **Filtering:** `/projects?technology=react`
- **Sorting:** `/projects?sort=latest`

---

## Security & Performance

- **Security:** Strict input sanitization (`sanitize_text_field`), output escaping (`esc_html`), Nonce verification, and Capability checks (`current_user_can`).
- **Performance:** Optimized `WP_Query` calls, selective field responses, and configurable API cache.
- **CORS:** Configurable origins through the Admin settings to support headless frontend integration.

---

## Admin UX

The plugin features a custom **Dashboard** and **Settings** area:
- **Dashboard:** At-a-glance content counters and API status.
- **API Settings:** Toggle REST API, enable CORS, manage allowed origins, and set cache duration.
- **Profile & Social:** Centralized management for personal branding data.

---

## Uninstall Cleanup

The `uninstall.php` routine ensures a clean exit by:
1. Deleting all Custom Post Type posts and their associated metadata.
2. Removing all custom taxonomy terms.
3. Clearing all plugin-specific options from the database.
4. Flushing rewrite rules.

---

## Author

**Al Adil Ashrafi Saikat**
- [adilashrafi.com](https://adilashrafi.com)
- [markimist.com](https://markimist.com)
- [banglatrack.com](https://banglatrack.com)
