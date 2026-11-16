<?php

/**
 * Plugin Name:       CWL Blocks
 * Plugin URI:        https://creativewebland.com
 * Description:       A library of reusable ACF blocks by Creative WebLand. Switch on only the blocks a site needs.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Creative WebLand
 * Author URI:        https://creativewebland.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cwl-blocks
 *
 * @package cwl-blocks
 */

if (! defined('ABSPATH')) {
    exit; // Block direct access to this file.
}

// Constants other files in the plugin can use.
define('CWL_BLOCKS_VERSION', '0.1.0');
define('CWL_BLOCKS_PATH', plugin_dir_path(__FILE__)); // Server path, with a trailing slash.
define('CWL_BLOCKS_URL', plugin_dir_url(__FILE__));   // Web address, with a trailing slash.

// Registers the blocks found in /blocks (and the shared base stylesheet).
require_once CWL_BLOCKS_PATH . 'inc/loader.php';

// Saves and loads the ACF field groups from this plugin's acf-json folder.
require_once CWL_BLOCKS_PATH . 'inc/acf.php';

// Shared helper functions used by the blocks, such as the button output.
require_once CWL_BLOCKS_PATH . 'inc/helpers.php';

// Settings helpers: lists the available blocks and which ones are switched on.
require_once CWL_BLOCKS_PATH . 'inc/settings.php';
