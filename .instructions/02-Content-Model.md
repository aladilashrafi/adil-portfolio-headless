# Content Model & Meta Fields

## 1. Projects (`hpcms_project`)
- **Meta Fields**:
    - `_hpcms_client_name`: Client name.
    - `_hpcms_project_url`: Live URL.
    - `_hpcms_github_url`: Source code.
    - `_hpcms_completion_date`: Date.
    - `_hpcms_featured`: Boolean.
    - `_hpcms_tech_stack`: Array/String of tech.
    - `_hpcms_gallery`: Image IDs.
    - `_hpcms_seo_title`: SEO Meta.
    - `_hpcms_seo_description`: SEO Meta.
- **Taxonomies**: `hpcms_project_category`, `hpcms_technology`, `hpcms_industry`.

## 2. Experience (`hpcms_experience`)
- **Meta Fields**:
    - `_hpcms_company_name`
    - `_hpcms_position`
    - `_hpcms_employment_type`
    - `_hpcms_start_date`, `_hpcms_end_date`
    - `_hpcms_current_position` (bool)
    - `_hpcms_company_url`
    - `_hpcms_location`

## 3. Education (`hpcms_education`)
- **Meta Fields**: `_hpcms_institution`, `_hpcms_degree`, `_hpcms_field_of_study`, `_hpcms_grade`, `_hpcms_certificate_url`.

## 4. Resume (`hpcms_resume`)
- **Meta Fields**: `_hpcms_resume_file` (URL/ID), `_hpcms_resume_version`, `_hpcms_resume_type` (Developer, Marketing, etc.).

## 5. Skills (`hpcms_skill`)
- **Meta Fields**: `_hpcms_skill_level` (proficiency), `_hpcms_skill_icon`, `_hpcms_experience_years`, `_hpcms_skill_url`.
- **Taxonomy**: `hpcms_skill_category` (Frontend, Backend, etc.).

## 6. Testimonials (`hpcms_testimonial`)
- **Meta Fields**: `_hpcms_client_name`, `_hpcms_client_position`, `_hpcms_company`, `_hpcms_rating`, `_hpcms_client_image`.
