<?php

/**
 * Settings helpers for CWL Blocks.
 *
 * Step 1: find which blocks exist and which ones are switched on.
 *
 * @package CWL_Blocks
 */

// Stop direct access to this file.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Get every block that ships with the plugin.
 *
 * Looks at the folders inside /blocks that contain a block.json file.
 * This list is the "allow-list": only names found here are ever valid.
 *
 * @return string[] Folder names, such as array( 'hero', 'process', 'services' ).
 */
function cwl_blocks_get_available()
{
    $available = array();

    // Find every blocks/*/block.json file.
    $files = glob(CWL_BLOCKS_PATH . 'blocks/*/block.json');

    // glob() returns false on error, so make sure we have an array.
    if (! is_array($files)) {
        return $available;
    }

    foreach ($files as $file) {
        // The folder name is the block's slug, such as "hero".
        $slug = basename(dirname($file));

        // Only accept simple names: lowercase letters, numbers, dashes.
        if (preg_match('/^[a-z0-9-]+$/', $slug)) {
            $available[] = $slug;
        }
    }

    // Alphabetical order so the settings page is predictable.
    sort($available);

    return $available;
}

/**
 * Get the blocks that are switched on.
 *
 * Until the settings are saved for the first time, every block is on.
 * Saved values are always checked against the real list of blocks.
 *
 * @return string[] Slugs of the enabled blocks.
 */
function cwl_blocks_get_enabled()
{
    $available = cwl_blocks_get_available();

    // false means the option has never been saved: turn everything on.
    $saved = get_option('cwl_blocks_enabled', false);

    if (false === $saved) {
        return $available;
    }

    // If the stored value is somehow not an array, treat it as "none".
    if (! is_array($saved)) {
        return array();
    }

    // Keep only slugs that really exist (drops old or tampered values).
    return array_values(array_intersect($available, $saved));
}
