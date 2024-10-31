<?php
/**
 * Plugin Name:       Pastacode
 * Description:       Share code snippets into your posts. Embed GitHub, Gitlab, Gist, Pastebin, Bitbucket or whatever remote files and even write your own code into block editor.
 * Requires at least: 4.0
 * Requires PHP:      7.0
 * Version:           3.0.1
 * Author:            Willy Bahuaud
 * Author URI:        https://wabeo.fr
 * Contributors:      willybahuaud, juliobox
 * Text Domain:       pastacode
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PASTACODE_VERSION', '3.0.1' );
define( 'PASTACODE_PLUGIN', __FILE__ );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function wab_pastacode_block_init() {
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'wab_pastacode_render_callback',
		)
	);
}
add_action( 'init', 'wab_pastacode_block_init' );

/**
 * Render callback function.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      Block instance.
 *
 * @return string The rendered output.
 */
function wab_pastacode_render_callback( $attributes, $content, $block ) {
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'build/template.php';
	return ob_get_clean();
}

require_once plugin_dir_path( __FILE__ ) . 'includes/pastacode-options.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/front.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/remote-code-getter.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/sources.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/back-dependencies.php';
// Legacy
if ( 'y' == get_option( 'pastacode_legacy' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/tinymce-config.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';
}

add_action( 'plugins_loaded', 'pastacode_load_languages' );
function pastacode_load_languages() {
	load_plugin_textdomain( 'pastacode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
