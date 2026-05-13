# AI Prompting Context & Token Saving

## How to use this folder
Before starting any task, read the relevant file in `.instructions/`.
- For UI/Meta changes: `02-Content-Model.md`
- For API changes: `03-REST-API.md`
- For Core/Bootstrapping: `01-Architecture.md`

## Token Saving Strategy
1. **Don't re-read the whole project**: Use `list_dir` to find specific files and `view_file` with line ranges.
2. **Refer to .instructions**: Use these files as the source of truth for architecture and field names.
3. **Keep edits focused**: Only modify the necessary lines.
4. **Assume PSR-4**: If you need a class, look in `includes/` based on the namespace.

## Critical Namespaces
- `HPCMS\CPT`: Custom Post Types.
- `HPCMS\Meta`: Meta boxes and fields.
- `HPCMS\API`: REST API controllers.
- `HPCMS\Core`: Central logic (CORS, Settings).
- `HPCMS\Admin`: Admin UI/Menu.
