<?php

add_action( 'wp_enqueue_scripts', 'pastacode_enqueue_prismjs' );
function pastacode_enqueue_prismjs() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_script( 'prismjs', plugins_url( '/js/prism.js', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, true );
	wp_register_script( 'prism-highlight', plugins_url( '/plugins/line-highlight/prism-line-highlight' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-normalize-whitespace', plugins_url( '/plugins/normalize-whitespace/prism-normalize-whitespace' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-linenumber', plugins_url( '/plugins/line-numbers/prism-line-numbers' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-show-invisible', plugins_url( '/plugins/show-invisibles/prism-show-invisibles' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_style( 'prismcss', plugins_url( '/css/' . get_option( 'pastacode_style', 'prism' ) . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-highlightcss', plugins_url( '/plugins/line-highlight/prism-line-highlight.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-linenumbercss', plugins_url( '/plugins/line-numbers/prism-line-numbers.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-show-invisiblecss', plugins_url( '/plugins/show-invisibles/prism-show-invisibles.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-treeviewcss', plugins_url( 'plugins/treeview/prism-treeview' . $suffix . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_script( 'prism-treeview', plugins_url( 'plugins/treeview/prism-treeview' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );

	if ( 'y' == get_option( 'pastacode_legacy' ) && apply_filters( 'pastacode_ajax', false ) ) {
		wp_enqueue_script( 'prismjs' );
		wp_enqueue_style( 'prismcss' );
		wp_enqueue_style( 'prism-highlightcss' );
		wp_enqueue_script( 'prism-normalize-whitespace' );
		wp_enqueue_script( 'prism-highlight' );

		if ( 'y' === get_option( 'pastacode_linenumbers', 'n' ) ) {
			wp_enqueue_style( 'prism-linenumbercss' );
			wp_enqueue_script( 'prism-linenumber' );
		}
		if ( 'y' === get_option( 'pastacode_showinvisible', 'n' ) ) {
			wp_enqueue_style( 'prism-show-invisiblecss' );
			wp_enqueue_script( 'prism-show-invisible' );
		}
	}
}
