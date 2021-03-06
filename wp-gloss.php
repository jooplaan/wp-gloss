<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.jooplaan.com/
 * @since             1.0.0
 * @package           Wp_Gloss
 *
 * @wordpress-plugin
 * Plugin Name:       Glossarium
 * Plugin URI:        https://github.com/jooplaan/wp-gloss
 * Description:       Build a glossary with preferred and non-preferred terms in a specialized field of knowledge.
 * Version:           1.0.5
 * Author:            Joop Laan
 * Author URI:        https://www.jooplaan.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-gloss
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_GLOSS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_GLOSS_VERSION', '1.0.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-gloss-activator.php
 */
function activate_wp_gloss() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-gloss-activator.php';
	Wp_Gloss_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-gloss-deactivator.php
 */
function deactivate_wp_gloss() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-gloss-deactivator.php';
	Wp_Gloss_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_gloss' );
register_deactivation_hook( __FILE__, 'deactivate_wp_gloss' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-gloss.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_gloss() {

	$plugin = new Wp_Gloss();
	$plugin->run();

}
run_wp_gloss();
