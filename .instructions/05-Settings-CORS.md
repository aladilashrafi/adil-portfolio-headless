# Settings & CORS

## Global Settings
Managed in `HPCMS\Core\Settings`:
- **Profile Info**: Full Name, Tagline, Bio, Email, Phone, Location.
- **Social Links**: GitHub, LinkedIn, X, YouTube, Behance.
- **SEO Defaults**: Meta Title, Description, OG Image.

## API Settings
- **Enable REST API**: Master toggle.
- **Enable CORS**: Toggle for cross-origin requests.
- **Allowed Origins**: List of domains allowed to access the API.
- **API Cache Duration**: Time in seconds for transient caching.

## CORS Implementation
Implemented in `HPCMS\Core\CORS`:
- Filters `rest_pre_serve_request` or uses `rest_api_init` hooks to add headers.
- Dynamically validates origins against the settings.
