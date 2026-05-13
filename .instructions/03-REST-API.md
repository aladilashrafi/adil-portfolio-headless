# REST API Architecture

## Namespace: `hpcms/v1`

## Endpoints
- `GET /projects`: List projects.
- `GET /projects/{slug}`: Single project.
- `GET /experience`: List experience.
- `GET /education`: List education.
- `GET /resume`: List resumes.
- `GET /skills`: List skills.
- `GET /testimonials`: List testimonials.
- `GET /profile`: Global profile settings.

## Features
- **Pagination**: Supports `?page=1&per_page=10`.
- **Filtering**: Supports `?technology=react`.
- **Sorting**: Supports `?sort=latest`.
- **Selective Fields**: Only returns necessary data for headless consumption.

## Response Structure (Example)
```json
{
  "id": 1,
  "title": "Project Title",
  "slug": "project-slug",
  "featuredImage": "url",
  "meta": {
    "client_name": "...",
    "project_url": "..."
  }
}
```
