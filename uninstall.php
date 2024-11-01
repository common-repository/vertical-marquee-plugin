<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option('vm_title');
delete_option('vm_setting1');
delete_option('vm_setting2');
delete_option('vm_setting3');
 
// for site options in Multisite
delete_site_option('vm_title');
delete_site_option('vm_setting1');
delete_site_option('vm_setting2');
delete_site_option('vm_setting3');

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}verticalmarquee");