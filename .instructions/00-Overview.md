# Project Overview: Headless Portfolio CMS

## Purpose
An API-first headless portfolio CMS plugin for WordPress. It allows developers to manage structured portfolio data and expose it via REST APIs for modern frontend frameworks (Next.js, React, Astro, etc.).

## Vision
A complete headless developer portfolio content platform powered by WordPress.

## Core Entities
- **Projects**: Case studies, apps, websites.
- **Experience**: Job history, freelance work.
- **Education**: Academic history, certifications.
- **Resume**: Downloadable CV versions.
- **Skills**: Icons, proficiency, categories.
- **Testimonials**: Client reviews.

## Directory Structure
```txt
headless-portfolio-cms/
├── assets/         # CSS/JS/Images
├── admin/          # Admin-specific logic
├── public/         # Public-facing logic
├── includes/       # Core PSR-4 classes
│   ├── API/        # REST API Controllers
│   ├── CPT/        # Post Type Registries
│   ├── Meta/       # Meta Box Registries
│   ├── Core/       # Settings, CORS, Taxonomies
│   └── Admin/      # Admin Menu/UI
├── templates/      # PHP templates (if any)
├── build/          # Compiled assets (Vite)
├── languages/      # i18n
├── vendor/         # Composer dependencies
└── headless-portfolio-cms.php # Main entry
```
