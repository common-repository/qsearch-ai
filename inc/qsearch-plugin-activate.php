<?php
/**
 * @package QSearch.ai
 */

class QsearchPluginActivate
{
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate(); 

		if($wpdb->get_var( "show tables like 'store_id'" ) != 'store_id') {
			$sql = "CREATE TABLE store_id (
				id int(11) NOT NULL auto_increment,
				store varchar(50) NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			dbDelta($sql);
		}

		flush_rewrite_rules();
	}
}