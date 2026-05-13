# Development Utilities

## Meta Field Helper (`HPCMS\Meta\Helper`)
Use `Helper::render_fields( $post, $fields )` to consistently render meta boxes in the admin.

### Usage Example:
```php
$fields = [
    '_hpcms_client_name' => [
        'label'       => __( 'Client Name', 'headless-portfolio-cms' ),
        'type'        => 'text',
        'placeholder' => 'e.g. Acme Corp',
    ],
    '_hpcms_employment_type' => [
        'label'   => __( 'Employment Type', 'headless-portfolio-cms' ),
        'type'    => 'select',
        'options' => [
            'full-time' => 'Full-time',
            'part-time' => 'Part-time',
            'freelance' => 'Freelance',
        ],
    ],
];
Helper::render_fields( $post, $fields );
```

## Registry Pattern
- **CPT Registry**: `HPCMS\CPT\Registry` manages all post type registrations.
- **Meta Registry**: `HPCMS\Meta\Registry` manages all meta box registrations.
- **API Registry**: `HPCMS\API\Registry` manages all REST API route registrations.

When adding a new feature, ensure it is registered in the appropriate `Registry::init()` method.
