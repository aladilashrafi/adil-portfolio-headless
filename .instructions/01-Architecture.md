# Plugin Architecture

## Constants
- `HPCMS_VERSION`: Current version.
- `HPCMS_PLUGIN_DIR`: File system path.
- `HPCMS_PLUGIN_URL`: Public URL.
- `HPCMS_API_NS`: API Namespace (`hpcms/v1`).

## Autoloading (PSR-4)
The plugin uses a custom spl_autoload_register in the main file:
- **Prefix**: `HPCMS\`
- **Base Directory**: `includes/`
- **Mapping**: `HPCMS\CPT\Registry` -> `includes/CPT/Registry.php`

## Bootstrap Process
Initialized on `plugins_loaded` via `hpcms_bootstrap()`:
1. `HPCMS\CPT\Registry::init()`
2. `HPCMS\Core\Taxonomies::init()`
3. `HPCMS\Meta\Registry::init()`
4. `HPCMS\Core\Settings::init()`
5. `HPCMS\Core\CORS::init()`
6. `HPCMS\API\Registry::init()`
7. `HPCMS\Admin\Menu::init()`

## Activation/Deactivation
- **Activation**: Registers CPTs, flushes rewrites, generates API token, sets default options (`enable_api`, `enable_cors`, `cache_duration`).
- **Deactivation**: Flushes rewrites.
