<?php

/**
 * ACF setup: where the block field groups are saved and loaded from.
 * Groups whose title starts with "Block:" live in this plugin's acf-json folder.
 *
 * @package cwl-blocks
 */

if (! defined('ABSPATH')) {
    exit; // Block direct access to this file.
}

/**
 * Save field groups titled "Block: ..." into the plugin. All other groups save where they did before.
 *
 * @param array $paths Folders ACF would save this group into.
 * @param array $post  The field group being saved.
 * @return array
 */
function cwl_blocks_acf_save_paths($paths, $post)
{
    if (isset($post['title']) && 0 === strpos($post['title'], 'Block:')) {
        return array(CWL_BLOCKS_PATH . 'acf-json');
    }
    return $paths;
}
add_filter('acf/json/save_paths', 'cwl_blocks_acf_save_paths', 10, 2);

/**
 * Also load field groups from the plugin's acf-json folder.
 *
 * @param array $paths Folders ACF loads JSON from.
 * @return array
 */
function cwl_blocks_acf_load_paths($paths)
{
    $paths[] = CWL_BLOCKS_PATH . 'acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'cwl_blocks_acf_load_paths');
