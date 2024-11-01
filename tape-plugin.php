<?php

/**
 * 
 * @link              https://www.trytape.com
 * @since             1.0.0
 * @package           Tape_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Tape
 * Plugin URI:        https://www.trytape.com
 * Description:       A plugin to simply embed Tape lead response forms in your WordPress posts and pages.
 * Version:           1.0.0
 * Author:            Dave Schatz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tape-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TAPE_PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tape-plugin-activator.php
 */
function tape_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tape-plugin-activator.php';
	Tape_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tape-plugin-deactivator.php
 */
function tape_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tape-plugin-deactivator.php';
	Tape_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'tape_activate_plugin' );
register_deactivation_hook( __FILE__, 'tape_deactivate_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tape-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function tape_run_plugin() {

	$plugin = new Tape_Plugin();
	$plugin->run();

}
tape_run_plugin();

function tape_shortcode( $atts ) {
		// Attributes
		extract( shortcode_atts(
			array(
				'campaignid' => '',
				'buttontext' => "Learn More",
				'buttoncolor' => "#428BCA",
				'buttoncolorhover' => "#3276B1",
			), $atts )
        );
	 	$style = "";
		if (!empty($buttoncolor)) {
			if (empty($buttoncolorhover)) {
				$buttoncolorhover = $buttoncolor;
			}
            $style = "
                <style type=\"text/css\">
                    input.tapeButton { background-color: {$buttoncolor}; }
                    input.tapeButton:hover { background-color: {$buttoncolorhover}; }
			    </style>
            ";
        }
       return '
			'.$style.'
			<div class="tapeWidget">
                <div class="tapeInner">
                    <form action="#" id="tapeForm">
						<input type="hidden" id="tapeCampaignId" name="tapeCampaignId" class="tapeCampaignId" value="'.$campaignid.'" />
						<div class="tapeInputWrapper">
							<input type="text" id="tapeName" name="tapeName" placeholder="Your Name" />
						</div>
						<div class="tapeInputWrapper">
							<input type="tel" id="tapePhone" name="tapePhone" placeholder="Your Mobile Number" />
							<span id="tape-error-msg" class="hide tapeValidationMsg"></span>
						</div>
						<div class="tapeInputWrapper tapeRight">
							<input class="tapeButton" type="submit" id="tapeSend" value="'.$buttontext.'" />
						</div>
						<div class="tapeError" id="tapeError">
						</div>
						<div class="tapePoweredDiv tapeLeft">Powered by <a class="tapePoweredLink" href="https://www.trytape.com" target="_blank" title="Automated Lead Response Forms">Tape</a></div>
                    </form>
                </div>
			</div>
	   ';
}

add_shortcode('tape', 'tape_shortcode');
