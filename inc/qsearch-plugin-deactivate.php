<?php

/**
 * @package  QsearchPlugin
 */
class QsearchPluginDeactivate {

    public static function deactivate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $delete = $wpdb->query("DELETE FROM store_id");

        $user_info = get_userdata(1);
        $email = $user_info->user_email;

        $url = 'https://control.qsearch.ai/woocommerce/uninstall?email=' . $email;
        $request = new WP_Http;
        $result = $request->request($url, array(
            'Content-Type: text/plain'
        ));
        flush_rewrite_rules();
    }

}
