<?php
/**
 * @package WPCASServerPlugin
 * @subpackage WPCASServerPlugin_Tests
 */

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../wp-cas-server.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

$GLOBALS['wp_tests_options']['active_plugins'][] = 'wp-cas-server/wp-cas-server.php';

require $_tests_dir . '/includes/bootstrap.php';
