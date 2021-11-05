<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.jooplaan.com/
 * @since      1.0.0
 *
 * @package    Wp_Gloss
 * @subpackage Wp_Gloss/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Gloss
 * @subpackage Wp_Gloss/includes
 * @author     Joop Laan <wp-gloss@interconnecting.systems>
 */
class Wp_Gloss_Activator {

	/**
	 * Run when plugin is activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Flush the cache on plugin activation.
		flush_rewrite_rules();
	}

}
