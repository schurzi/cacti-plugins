<?php
include_once ('./include/auth.php');
function spinedebug_device_action_execute($action) {
	if ($action == "plugin_spinedebug") {
		include_once ("./include/top_header.php");
		if (isset ( $_POST ["selected_items"] )) {
			$selected_items = unserialize ( stripslashes ( $_POST ["selected_items"] ) );
			
			$verbosity = 3;
			if (isset ( $_POST ["verbosity"] ) && is_numeric ( $_POST ["verbosity"] )) {
				$verbosity = $_POST ["verbosity"];
			}
			
			$spine = read_config_option ( "path_spine" );
			$olddir = getcwd ();
			chdir ( dirname ( read_config_option ( "path_spine" ) ) );
			
			foreach ( $selected_items as $item ) {
				input_validate_input_number ( $item );
				$sql = "SELECT description FROM host WHERE id = " . $item;
				$hostname = db_fetch_assoc ( $sql );
				
				$title = "<b>Host:</b> " . $hostname [0] ["description"];
				html_start_box ( $title, "98%", $colors ["header_panel"], "3", "center", "" );
				print "<tr><td class='textArea'><pre style='";
				print "white-space: pre-wrap;"; /* CSS 3 */
				print "white-space: -moz-pre-wrap;"; /* Mozilla, since 1999 */
				print "white-space: -pre-wrap;"; /* Opera 4-6 */
				print "white-space: -o-pre-wrap;"; /* Opera 7 */
				print "word-wrap: break-word;"; /* Internet Explorer 5.5+ */
				print "'>";
				
				print passthru ( $spine . " -R -S -V " . $verbosity . " -H " . $item );
				
				print "</pre></td></tr>";
				html_end_box ();
			}
			
			chdir ( $olddir );
		}
		include_once ("./include/bottom_footer.php");
	} else {
		return $action;
	}
	exit ();
}
function spinedebug_device_action_prepare($save) {
	if ($save ["drp_action"] == "plugin_spinedebug") {
		if (isset ( $save ["host_array"] )) {
			print "<tr><td class='textArea'>";
			print "<p>Are you sure you want to run spine in debug mode for the following hosts?</p>";
			print "<ul>" . $save ["host_list"] . "</ul></td>";
			
			$verbosity = array (
					1 => 1,
					2,
					3,
					4,
					5,
					6,
					7,
					8,
					9 
			);
			
			$verbosity_dropdown = array (
					"friendly_name" => "Verbosity",
					"description" => "How verbose the output will be",
					"method" => "drop_array",
					"value" => 3,
					"array" => $verbosity 
			);
			
			draw_edit_form ( array (
					"config" => array (
							"no_form_tag" => true 
					),
					"fields" => array (
							"verbosity" => $verbosity_dropdown 
					) 
			) );
			
			return true;
		}
	} else {
		return $save;
	}
}
?>