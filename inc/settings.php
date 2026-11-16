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

/**
 * Clean the list of blocks submitted from the settings page.
 *
 * WordPress calls this automatically every time the setting is saved.
 * Only block names that really exist on disk can get through, so a
 * forged request can't enable something that isn't there.
 *
 * @param mixed $input Whatever was submitted: an array of slugs, or null.
 * @return string[] Clean list of slugs that exist in /blocks.
 */
function cwl_blocks_sanitize_enabled($input)
{
    // Nothing ticked: browsers send nothing at all, so $input is not an array.
    if (! is_array($input)) {
        return array();
    }

    // Keep only plain strings (drops nested arrays and other odd values).
    $input = array_filter($input, 'is_string');

    // Lowercase and strip anything except letters, numbers, dashes, underscores.
    $input = array_map('sanitize_key', $input);

    // Allow-list: keep only slugs that match a real folder in /blocks.
    return array_values(array_intersect(cwl_blocks_get_available(), $input));
}

/**
 * Tell WordPress about the "cwl_blocks_enabled" setting.
 *
 * register_setting() connects the option to our sanitize function, so
 * every save goes through it. We set no default on purpose: an unsaved
 * option means "all blocks on" (see cwl_blocks_get_enabled()).
 *
 * @return void
 */
function cwl_blocks_register_setting()
{
    register_setting(
        'cwl_blocks_settings',  // Option group: the name the form will use.
        'cwl_blocks_enabled',   // Option name stored in the database.
        array(
            'type'              => 'array',
            'sanitize_callback' => 'cwl_blocks_sanitize_enabled',
        )
    );
}
// admin_init runs on every admin page load, the right time to register settings.
add_action('admin_init', 'cwl_blocks_register_setting');

/**
 * Get a friendly name for a block.
 *
 * Reads the "title" from the block's block.json, and falls back to the
 * folder name if the file can't be read. The slug passed in must already
 * come from cwl_blocks_get_available(), so the path is safe.
 *
 * @param string $slug Block folder name, such as "hero".
 * @return string Display name, such as "Hero".
 */
function cwl_blocks_get_title($slug)
{
    // wp_json_file_decode() reads and decodes a JSON file (WordPress 5.9+).
    $data = wp_json_file_decode(
        CWL_BLOCKS_PATH . 'blocks/' . $slug . '/block.json',
        array('associative' => true)
    );

    // Use the title if there is one; otherwise show the folder name.
    if (is_array($data) && ! empty($data['title']) && is_string($data['title'])) {
        return $data['title'];
    }

    return $slug;
}

/**
 * Add "CWL Blocks" under the Settings menu in the dashboard.
 *
 * The 'manage_options' capability means only administrators see it.
 *
 * @return void
 */
function cwl_blocks_add_menu()
{
    add_options_page(
        'CWL Blocks',            // Browser tab title.
        'CWL Blocks',            // Label in the Settings menu.
        'manage_options',        // Who can open it: administrators only.
        'cwl-blocks',            // URL slug: options-general.php?page=cwl-blocks
        'cwl_blocks_render_page' // Function that prints the page.
    );
}
// admin_menu runs when WordPress builds the dashboard menu.
add_action('admin_menu', 'cwl_blocks_add_menu');

/**
 * Print the settings page: one checkbox per available block.
 *
 * The form posts to options.php, which handles saving. settings_fields()
 * adds the hidden nonce field that proves the request came from this
 * page, and options.php checks it, plus the user's capability, before
 * running our sanitize function.
 *
 * @return void
 */
function cwl_blocks_render_page()
{
    // Second lock: check the capability again in case this is reached another way.
    if (! current_user_can('manage_options')) {
        wp_die('You do not have permission to view this page.', '', array('response' => 403));
    }

    // Which blocks exist, and which are currently switched on.
    $available = cwl_blocks_get_available();
    $enabled   = cwl_blocks_get_enabled();
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>Switch on only the blocks this site needs. Blocks that are off do not load any code or CSS.</p>

        <form method="post" action="options.php">
            <?php
            // Prints the hidden nonce and tells options.php which setting group this is.
            settings_fields('cwl_blocks_settings');
            ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <?php foreach ($available as $slug) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html(cwl_blocks_get_title($slug)); ?></th>
                            <td>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="cwl_blocks_enabled[]"
                                        value="<?php echo esc_attr($slug); ?>"
                                        <?php checked(in_array($slug, $enabled, true)); ?>>
                                    Enabled
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
<?php
}
