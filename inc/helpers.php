<?php

/**
 * Helper functions the blocks use.
 *
 * @package cwl-blocks
 */

if (! defined('ABSPATH')) {
    exit; // Block direct access to this file.
}

/**
 * Print a button from an ACF Link field.
 *
 * @param array|null $link  ACF link array: url, title, target. Does nothing if empty.
 * @param string     $style Button variant: 'accent', 'outline' or 'dark'.
 */
function cwl_blocks_the_button($link, $style = 'accent')
{
    // No link set, or no URL: print nothing.
    if (! is_array($link) || empty($link['url'])) {
        return;
    }

    // Only allow the three variants we have CSS for.
    $allowed = array('accent', 'outline', 'dark');
    $style   = in_array($style, $allowed, true) ? $style : 'accent';

    // Links that open in a new tab need rel="noopener noreferrer" so the new page can't control this one.
    $target = '';
    if (! empty($link['target']) && '_blank' === $link['target']) {
        $target = ' target="_blank" rel="noopener noreferrer"';
    }

    printf(
        '<a class="btn btn--%s" href="%s"%s>%s</a>',
        esc_attr($style),
        esc_url($link['url']),
        $target, // Built above from fixed strings only.
        esc_html($link['title'])
    );
}
