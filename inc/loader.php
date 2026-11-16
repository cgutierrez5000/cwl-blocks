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
 * Register each block that has a block.json inside the plugin's blocks folder.
 */
function cwl_blocks_register()
{
    // Full paths of every block.json one level down: blocks/hero/block.json, etc.
    $files = glob(CWL_BLOCKS_PATH . 'blocks/*/block.json');

    // glob() returns false on error, so make sure we have an array before looping.
    if (! is_array($files)) {
        return;
    }

    // Register the shared base stylesheet once. Each block lists its handle in block.json,
    // so WordPress prints it a single time on any page that uses at least one CWL block.
    wp_register_style(
        'cwl-blocks-base',
        CWL_BLOCKS_URL . 'assets/css/base.css',
        array(),
        filemtime(CWL_BLOCKS_PATH . 'assets/css/base.css') // Version from the file's last change, so caches refresh.
    );

    foreach ($files as $file) {
        // register_block_type() accepts the folder that holds block.json.
        register_block_type(dirname($file));
    }
}
add_action('init', 'cwl_blocks_register');
