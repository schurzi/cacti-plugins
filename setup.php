<?php
function plugin_spinedebug_install() {
	api_plugin_register_hook ( 'spinedebug', 'device_action_array', 'spinedebug_device_action_array', 'setup.php' );
	api_plugin_register_hook ( 'spinedebug', 'device_action_prepare', 'spinedebug_device_action_prepare', 'spinedebug.php' );
	api_plugin_register_hook ( 'spinedebug', 'device_action_execute', 'spinedebug_device_action_execute', 'spinedebug.php' );
	// TODO: add authentication function
	// api_plugin_register_realm ( 'spinedebug', 'spinedebug_action.php', 'Plugin Spinedebug > Execute Spinedebug', 1 );
}
function plugin_spinedebug_uninstall() {
}
function plugin_spinedebug_check_config() {
	/* Here we will check to ensure everything is configured */
	spinedebug_check_upgrade ();
	return true;
}
function plugin_spinedebug_upgrade() {
	/* Here we will upgrade to the newest version */
	spinedebug_check_upgrade ();
	return false;
}
function plugin_spinedebug_version() {
	return spinedebug_version ();
}
function spinedebug_version() {
	return array (
			'name' => 'spinedebug',
			'version' => '0.1',
			'longname' => 'Debug spine process',
			'author' => 'Martin Schurz',
			'homepage' => 'https://github.com/schurzi/cacti-spinedebug',
			'email' => '-',
			'url' => 'https://github.com/schurzi/cacti-spinedebug' 
	);
}
function spinedebug_check_upgrade() {
	global $config;
	
	$files = array (
			'index.php',
			'plugins.php' 
	);
	if (isset ( $_SERVER ['PHP_SELF'] ) && ! in_array ( basename ( $_SERVER ['PHP_SELF'] ), $files )) {
		return;
	}
	
	$current = plugin_spinedebug_version ();
	$current = $current ['version'];
	$old = db_fetch_row ( "SELECT * FROM plugin_config WHERE directory='spinedebug'" );
	if (sizeof ( $old ) && $current != $old ["version"]) {
		/* if the plugin is installed and/or active */
		if ($old ["status"] == 1 || $old ["status"] == 4) {
			/* re-register the hooks */
			plugin_spinedebug_install ();
			
			/* perform a database upgrade */
			spinedebug_database_upgrade ();
		}
		
		/* update the plugin information */
		$info = plugin_spinedebug_version ();
		$id = db_fetch_cell ( "SELECT id FROM plugin_config WHERE directory='spinedebug'" );
		db_execute ( "UPDATE plugin_config
			SET name='" . $info ["longname"] . "',
			author='" . $info ["author"] . "',
			webpage='" . $info ["homepage"] . "',
			version='" . $info ["version"] . "'
			WHERE id='$id'" );
	}
}
function spinedebug_database_upgrade() {
}
function spinedebug_device_action_array($action) {
	$action ['plugin_spinedebug'] = 'Execute spine in debug mode';
	return $action;
}

?>
