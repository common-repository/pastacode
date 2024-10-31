<?php

add_action( 'admin_enqueue_scripts', 'pastacode_cgb_block_assets' );
function pastacode_cgb_block_assets() { // phpcs:ignore
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_script( 'prismjs', plugins_url( 'js/prism.js', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, true );
	wp_register_script( 'prism-highlight', plugins_url( 'plugins/line-highlight/prism-line-highlight' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-normalize-whitespace', plugins_url( 'plugins/normalize-whitespace/prism-normalize-whitespace' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-linenumber', plugins_url( 'plugins/line-numbers/prism-line-numbers' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-show-invisible', plugins_url( 'plugins/show-invisibles/prism-show-invisibles' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'prism-treeview', plugins_url( 'plugins/treeview/prism-treeview' . $suffix . '.js', PASTACODE_PLUGIN ), array( 'prismjs' ), PASTACODE_VERSION, true );
	wp_register_script( 'pastacode-editor-script', plugins_url( 'build/index.js', PASTACODE_PLUGIN ), array( 'prismjs', 'prism-highlight', 'prism-normalize-whitespace', 'prism-linenumber', 'prism-treeview'), PASTACODE_VERSION, true );
	wp_register_style( 'prismcss', plugins_url( 'css/' . get_option( 'pastacode_style', 'prism' ) . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-highlightcss', plugins_url( 'plugins/line-highlight/prism-line-highlight' . $suffix . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-linenumbercss', plugins_url( 'plugins/line-numbers/prism-line-numbers' . $suffix . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-show-invisiblecss', plugins_url( 'plugins/show-invisibles/prism-show-invisibles' . $suffix . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'prism-treeviewcss', plugins_url( 'plugins/treeview/prism-treeview' . $suffix . '.css', PASTACODE_PLUGIN ), false, PASTACODE_VERSION, 'all' );
	wp_register_style( 'pastacode-editor-style', plugins_url( 'build/style-index.css', PASTACODE_PLUGIN ), array( 'prismcss', 'prism-highlightcss', 'prism-linenumbercss', 'prism-treeviewcss' ), PASTACODE_VERSION, 'all' );
}

function pastacode_gut_custom_vars( $metadata ) {
	$services = apply_filters( 'pastacode_services', array() );
	$providers = [];
	foreach ( $services as $k => $v ) {
		$providers[] = array( 'label' => $v, 'value' => $k );
	}

	$langs = apply_filters( 'pastacode_langs', array() );
	$languages = [];
	foreach ( $langs as $k => $v ) {
		$languages[] = array( 'label' => $v, 'value' => $k );
	}

	$metadata = array(
		'fields'   => apply_filters( 'pastacode_fields', array() ),
		'langs'    => $languages,
		'services' => $providers,
		'posInfo'  => get_option( 'pastacode_aboutcode_pos', 'top' ),
		'cmStyle'  => get_option( 'pastacode_cm_style', 'sublime' ),
	);
	wp_localize_script( 'pastacode-editor-script', 'pastaGutVars', $metadata );
	
}
add_filter( 'admin_enqueue_scripts', 'pastacode_gut_custom_vars' );