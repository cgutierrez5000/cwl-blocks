# CWL Blocks

A library of reusable ACF blocks by [Creative WebLand](https://creativewebland.com). Switch on only the blocks a site needs; blocks that are off load no code or CSS.

## Requirements

- WordPress 6.5 or newer
- PHP 8.1 or newer
- [ACF Pro](https://www.advancedcustomfields.com/pro/) 6.x (the blocks use repeaters and ACF's block support)

If ACF Pro is missing, administrators see a notice and no blocks are registered.

## Blocks

| Block | What it does |
| --- | --- |
| Hero | Eyebrow, heading with a highlighted word, intro text and two buttons |
| Services | A grid of cards, one of which can be highlighted |
| Process | Numbered steps, shown on a navy background |

More blocks will be added over time.

## Install

1. Download the latest release zip from the Releases page, or clone this repository into `wp-content/plugins/cwl-blocks`.
2. Activate the plugin under **Plugins**.
3. Go to **Settings > CWL Blocks** and choose which blocks to enable.
4. The blocks appear in the block editor under the CWL Blocks category.

Until the settings are saved for the first time, every block is enabled.

## How it works

- Each block lives in `blocks/<name>/` with a `block.json`, a `render.php` and a `style.css`.
- The plugin finds blocks by scanning `blocks/*/block.json`, then registers only the ones that are switched on.
- Shared component styles (sections, grid, cards, buttons) live in `assets/css/base.css` and load once, only on pages that use a block.
- Colors, fonts and spacing come from the active theme's CSS variables (for example `--wp--preset--color--primary`), with fallback values in `base.css`, so a block follows whatever theme it is used with.
- ACF field groups for the blocks are saved as JSON in `acf-json/`. Field group titles that start with "Block:" are saved here.

## Adding a block

1. Create `blocks/<name>/` with `block.json`, `render.php` and `style.css`.
2. Add `"style": ["cwl-blocks-base", "file:./style.css"]` to `block.json`.
3. Create the field group in ACF with a title starting with "Block:" and save it.
4. The block appears on the settings page automatically.

## Security

- All output is escaped, and every PHP file blocks direct access.
- The settings page requires the `manage_options` capability and a nonce.
- Submitted block names are checked against the folders that really exist, never trusted as sent.
- Links that open in a new tab get `rel="noopener noreferrer"`.

Found a security problem? Please email info@creativewebland.com instead of opening a public issue.

## License

GPL v2 or later. See [LICENSE](LICENSE).