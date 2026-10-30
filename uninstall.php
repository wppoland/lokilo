<?php

/**
 * Locator uninstall routine.
 *
 * Removes plugin options when the user deletes the plugin. Store locations are
 * left intact (they are user content in a custom post type); the merchant can
 * delete them manually before removing the plugin if desired.
 *
 * @package Locator
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

/**
 * Remove this plugin's options from the current site.
 */
function locator_uninstall_site(): void
{
    delete_option('locator_settings');
    delete_option('locator_db_version');
}

// Options are per site, so a network uninstall has to visit every site.
if (is_multisite()) {
    foreach (get_sites(['fields' => 'ids', 'number' => 0]) as $locator_site_id) {
        switch_to_blog((int) $locator_site_id);
        locator_uninstall_site();
        restore_current_blog();
    }
    unset($locator_site_id);
} else {
    locator_uninstall_site();
}

// The PRO banner's dismissal is stored per user, so it belongs to the
// plugin rather than to the site content. User meta is global, not
// per-site, which is why this uses delete_metadata's \$delete_all rather
// than a loop over the users of one blog.
delete_metadata('user', 0, 'locator_pro_banner_dismissed', '', true);
