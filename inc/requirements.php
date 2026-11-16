<?php

/**
 * Requirement checks for CWL Blocks.
 *
 * Shows an admin notice when ACF Pro is missing, because every block
 * in this plugin is built on it.
 *
 * @package CWL_Blocks
 */

// Stop direct access to this file.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Is ACF Pro installed and active?
 *
 * Both checks are needed: the ACF class exists in the free and the Pro
 * versions, and the ACF_PRO constant is true only in Pro.
 *
 * @return bool True when ACF Pro is active.
 */
function cwl_blocks_acf_pro_active()
{
    return class_exists('ACF') && defined('ACF_PRO') && ACF_PRO;
}

/**
 * Show a red notice at the top of the dashboard if ACF Pro is missing.
 *
 * Only administrators see it, because only they can fix it.
 *
 * @return void
 */
function cwl_blocks_requirements_notice()
{
    // Everything is fine: print nothing.
    if (cwl_blocks_acf_pro_active()) {
        return;
    }

    // Don't show the message to editors and other roles who can't install plugins.
    if (! current_user_can('activate_plugins')) {
        return;
    }

    // Static text only, but escaped by habit.
    echo '<div class="notice notice-error"><p><strong>'
        . esc_html('CWL Blocks')
        . '</strong> '
        . esc_html('needs ACF Pro to be installed and active. Its blocks will not work until then.')
        . '</p></div>';
}
// admin_notices runs near the top of every dashboard page.
add_action('admin_notices', 'cwl_blocks_requirements_notice');
