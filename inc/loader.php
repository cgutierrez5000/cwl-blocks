<?php

/**
 * Block loader: registers the blocks found in /blocks.
 *
 * @package cwl-blocks
 */

if (! defined('ABSPATH')) {
    exit; // Block direct access to this file.
}

/**
 * Register the shared stylesheet and every enabled block.
 *
 * Runs on the "init" hook. Only blocks switched on in the settings
 * are registered, so disabled blocks never load any code or CSS.
 *
 * @return void
 */
function cwl_blocks_register()
{
    // Without ACF Pro the blocks can't render, so register nothing.
    // The admin notice (inc/requirements.php) tells the administrator why.
    if (! cwl_blocks_acf_pro_active()) {
        return;
    }

    // Shared component CSS. Registered once; each block.json lists it as a dependency.
    wp_register_style(
        'cwl-blocks-base',
        CWL_BLOCKS_URL . 'assets/css/base.css',
        array(),
        filemtime(CWL_BLOCKS_PATH . 'assets/css/base.css')
    );

    // Loop over the enabled slugs (already checked against real folders).
    foreach (cwl_blocks_get_enabled() as $slug) {
        // Each block lives in blocks/<slug>/ and contains a block.json file.
        register_block_type(CWL_BLOCKS_PATH . 'blocks/' . $slug);
    }
}
add_action('init', 'cwl_blocks_register');

/**
 * Add a "CWL Blocks" category to the block inserter.
 *
 * Blocks pick it by setting "category": "cwl-blocks" in their block.json.
 * It goes first in the list so the blocks are easy to find.
 *
 * @param array[] $categories The existing block categories.
 * @return array[] The categories with ours added at the start.
 */
function cwl_blocks_add_category($categories)
{
    array_unshift(
        $categories,
        array(
            'slug'  => 'cwl-blocks',
            'title' => 'CWL Blocks',
            'icon'  => null,
        )
    );

    // Filters must always return the value.
    return $categories;
}
// block_categories_all builds the category list for the editor.
add_filter('block_categories_all', 'cwl_blocks_add_category');
