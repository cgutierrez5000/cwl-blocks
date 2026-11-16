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
