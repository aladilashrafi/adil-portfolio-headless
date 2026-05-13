# API Usage Examples

## Fetching Projects
```javascript
// Example using fetch
const response = await fetch('https://your-wp-site.com/wp-json/hpcms/v1/projects');
const projects = await response.json();

// Example project object structure
/*
{
  "id": 123,
  "title": "My Awesome Project",
  "slug": "my-awesome-project",
  "content": "Full description...",
  "meta": {
    "project_url": "https://demo.com",
    "github_url": "https://github.com/...",
    "tech_stack": "React, Node.js",
    "is_featured": true
  },
  "categories": ["Web Dev", "Fintech"],
  "technologies": ["React", "TypeScript"],
  "featured_image": "https://.../image.jpg"
}
*/
```

## Fetching Profile Info
```javascript
const response = await fetch('https://your-wp-site.com/wp-json/hpcms/v1/profile');
const profile = await response.json();

// Result structure
/*
{
  "full_name": "Al Adil Ashrafi Saikat",
  "tagline": "Full Stack Developer",
  "bio": "...",
  "social": {
    "github": "...",
    "linkedin": "..."
  }
}
*/
```

## Filtering & Pagination
```bash
# Get page 2 of projects, 5 per page
GET /wp-json/hpcms/v1/projects?page=2&per_page=5

# Filter projects by technology slug
GET /wp-json/hpcms/v1/projects?technology=react
```
