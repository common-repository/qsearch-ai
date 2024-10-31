<?php

/**
 * @package QSearch.ai
 * @version 1.0.0
 */
/*
  Plugin Name: QSearch.ai
  Plugin URI: https://www.qsearch.ai/
  Description: QSearch is an app that will simplify and improve every Woocommerce websites search performance. Site search capabilities include: Smart Navigation, Rich Auto-Complete, Semantic Search, Faceting & Filtering, Big Data Product Recommendations, Reporting and analytics
  Version: 1.0.0
  Author: NextLogic
  Author URI: http://nextlogic.ro
  License: GPLv2 or later
  Text Domain: qsearch.ai
 */

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

if (!class_exists('QsearchPlugin')) {

    class QsearchPlugin {

        public $plugin;
        private $store;

        function __construct() {
            $this->plugin = plugin_basename(__FILE__);
        }

        function register() {
            if (is_admin()) {
                add_action('admin_menu', array($this, 'add_admin_pages'));
                add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));
            }
            add_action('wp_head', array($this, 'changeSearch'));
        }

        public function changeSearch() {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if (is_plugin_active('qsearch-ai/qsearch-plugin.php')) {
                global $wpdb;
                $results = $wpdb->get_results("SELECT store FROM store_id");
                if (count($results) > 0 && $results[0]->store) {
                    $this->_setargs($results[0]->store);
                    $this->callQsjscript();
                } else {
                    $user_info = get_userdata(1);
                    $email = $user_info->user_email;
                    $url = 'https://control.qsearch.ai/woocommerce/email?email=' . $email;
                    $request = new WP_Http;
                    $result = $request->request($url, array(
                        'Content-Type: text/plain'
                    ));
                    if ($result && $result['body'] && strpos($result['body'], "error") === false) {
                        $wpdb->insert('store_id', array('store' => $result['body']), array('%s'));
                        $this->_setargs($result['body']);
                        $this->callQsjscript();
                    }
                }
            }
        }

        public function settings_link($links) {
            //  return array_merge(array('configure' => '' . __('Configure') . ''), $actions);
            $settings_link = '<a href="admin.php?page=qsearch_plugin">Settings</a>';
            array_push($links, $settings_link);
            return $links;
        }

        public function add_admin_pages() {
            add_menu_page('Qsearch Plugin', 'Qsearch', 'manage_options', 'qsearch_plugin', array($this, 'admin_index'), 'dashicons-store', 110);
        }

        public function admin_index() {
            require_once plugin_dir_path(__FILE__) . 'templates/admin.php';
        }

        function saveemaildata() {
            global $wpdb;
            $store_id = $_POST['store_id'];
            if (isset($store_id)) {
                $results = $wpdb->get_results("SELECT id, store FROM store_id");
                if (count($results) > 0 && $results[0]->store && $results[0]->store != $store_id) {
                    $wpdb->update('store_id', array('store' => $store_id), array('id' => $results[0]->id), array('%s'));
                } else
                    $wpdb->insert('store_id', array('store' => $store_id), array('%s'));
            }
            $ddata = array("success" => "true");
            header('Content-Type: text/html;charset=utf-8');
            http_response_code(200);
            echo json_encode($ddata);
        }

        private function callQsjscript() {
            wp_enqueue_script('quick_search_script', 'https://set.qsearch.ai/main.js?qsid=' . $this->_getargs(), array('jquery'), false, false);
            wp_add_inline_script('quick_search_script', 'document.addEventListener("DOMContentLoaded", function () {__qs_options.AWS_store_id="' . $this->_getargs() . '"; do_qSearchLoad();});');
        }

        private function _setargs($data) {
            $this->store = $data;
        }

        private function _getargs() {
            return $this->store;
        }

        function activate() {
            require_once plugin_dir_path(__FILE__) . 'inc/qsearch-plugin-activate.php';
            QsearchPluginActivate::activate();
        }

    }

    $QsearchPlugin = new QsearchPlugin();
    $QsearchPlugin->register();
// action
    add_action('wp_ajax_saveemaildata', array($QsearchPlugin, 'saveemaildata'));
    add_action('wp_ajax_nopriv_saveemaildata', array($QsearchPlugin, 'saveemaildata'));

// activation
    register_activation_hook(__FILE__, array($QsearchPlugin, 'activate'));

// deactivation
    require_once plugin_dir_path(__FILE__) . 'inc/qsearch-plugin-deactivate.php';
    register_deactivation_hook(__FILE__, array('QsearchPluginDeactivate', 'deactivate'));
}
