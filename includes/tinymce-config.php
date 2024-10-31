<?php

//Button
add_action( 'admin_init', 'pastacode_button_editor' );
function pastacode_button_editor() {

	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return false;
	}

	if ( get_user_option( 'rich_editing' ) == 'true' ) {
		add_filter( 'mce_external_plugins', 'pastacode_script_tiny' );
		add_filter( 'mce_buttons', 'pastacode_register_button' );
	}
}

function pastacode_register_button( $buttons ) {
	array_splice( $buttons, -2, 1, 'pcb' );
	return $buttons;
}

function pastacode_script_tiny( $plugin_array ) {
	global $wp_version;
	if ( version_compare( $wp_version, '4.2.3', '>=' ) ) {
		$plugin_array['pcb'] = plugins_url( '/js/tinymce2.js?v=' . PASTACODE_VERSION, PASTACODE_PLUGIN );
	} else {
		$plugin_array['pcb'] = plugins_url( '/js/tinymce.js?v=' . PASTACODE_VERSION, PASTACODE_PLUGIN );
	}
	return $plugin_array;
}

add_action( 'admin_enqueue_scripts', 'pastacode_shortcodes_mce_css' );
function pastacode_shortcodes_mce_css() {

	wp_enqueue_style( 'pastacode-codemirror', plugins_url( '/js/tinymce-plugins/codemirror-wp.css', PASTACODE_PLUGIN ) );
	wp_enqueue_style( 'pastacode-tinymce', plugins_url( '/css/pastacode-tinymce.css', PASTACODE_PLUGIN ) );
	$editor_theme = get_option( 'pastacode_bo_style', 'ambiance' );
	wp_enqueue_style( 'pastacode-codemirror-theme', plugins_url( '/js/tinymce-plugins/codemirror/theme/' . $editor_theme . '.css', PASTACODE_PLUGIN ) );

	wp_register_script( 'labjs', plugins_url( '/js/LAB.min.js', PASTACODE_PLUGIN ) );
	wp_enqueue_script( 'labjs' );
	wp_register_script( 'jquery-linenumbers', plugins_url( '/js/jquery-linenumbers.js', PASTACODE_PLUGIN ), array( 'jquery' ) );
	wp_enqueue_script( 'jquery-linenumbers' );
}

add_filter( 'mce_css', 'pastacode_plugin_mce_css' );
function pastacode_plugin_mce_css( $mce_css ) {
	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}
	$mce_css .= plugins_url( '/css/pastacode-tinymce.css', PASTACODE_PLUGIN );
	return $mce_css;
}

add_action( 'admin_enqueue_scripts', 'pastacode_maybe_load_tinygut' );
function pastacode_maybe_load_tinygut() {
	ob_start();
	pastacode_text();
	$data = ob_get_clean();
	$data = preg_replace('/<\/?script>/', '', $data );
	wp_add_inline_script( 'wp-editor', $data, 'before' );
}

add_action( 'before_wp_tiny_mce', 'pastacode_text' );
function pastacode_text() {
	// I10n
	$text = json_encode( array(
		'window-title'       => __( 'Past\'a code', 'pastacode' ),
		'label-provider'     => __( 'Select a provider', 'pastacode' ),
		'label-langs'        => __( 'Select a syntax', 'pastacode' ),
		'image-placeholder'  => plugins_url( '/images/pastacode-placeholder.png', PASTACODE_PLUGIN ),
		'window-manuel-full' => __( 'Manual Code Editor', 'pastacode' ),
		'label-lines'        => __( 'Lines:', 'pastacode' ),
		'label-title'        => __( 'Title:', 'pastacode' ),
		'label-lang'         => __( 'Syntax:', 'pastacode' ),
		'label-type'         => __( 'Provider:', 'pastacode' ),
	) );

	// Services
	$services = apply_filters( 'pastacode_services', array() );

	// Languages
	$langs = apply_filters( 'pastacode_langs', array() );

	$upload_dir = wp_upload_dir();

	// Other fields
	$fields = apply_filters( 'pastacode_fields', array() );

	$new_fields = array();
	$new_langs = array();
	foreach ( $langs as $k => $s ) {
		$new_langs[] = array( 'text' => $s, 'value' => $k );
	}
	$new_fields[] = array( 'type' => 'listbox', 'label' => __( 'Select a syntax', 'pastacode' ), 'name' => 'lang', 'values' => $new_langs );

	$pvars['providers'] = $services;

	$pvars['scripts'] = array(
		'codemirror'    => plugins_url( 'js/tinymce-plugins/codemirror/lib/codemirror.js', PASTACODE_PLUGIN ),
		// 'comment'       => plugins_url( 'js/tinymce-plugins/codemirror/addon/comment/comment.js', PASTACODE_PLUGIN ),
		'matchbrackets' => plugins_url( 'js/tinymce-plugins/codemirror/addon/edit/matchbrackets.js', PASTACODE_PLUGIN ),
		// 'matchtags'     => plugins_url( 'js/tinymce-plugins/codemirror/addon/edit/matchtags.js', PASTACODE_PLUGIN ),
		'coffeescript'  => plugins_url( 'js/tinymce-plugins/codemirror/mode/coffeescript/coffeescript.js', PASTACODE_PLUGIN ),
		'css'           => plugins_url( 'js/tinymce-plugins/codemirror/mode/css/css.js', PASTACODE_PLUGIN ),
		'clike'         => plugins_url( 'js/tinymce-plugins/codemirror/mode/clike/clike.js', PASTACODE_PLUGIN ),
		'htmlmixed'     => plugins_url( 'js/tinymce-plugins/codemirror/mode/htmlmixed/htmlmixed.js', PASTACODE_PLUGIN ),
		'haml'          => plugins_url( 'js/tinymce-plugins/codemirror/mode/haml/haml.js', PASTACODE_PLUGIN ),
		'javascript'    => plugins_url( 'js/tinymce-plugins/codemirror/mode/javascript/javascript.js', PASTACODE_PLUGIN ),
		'php'           => plugins_url( 'js/tinymce-plugins/codemirror/mode/php/php.js', PASTACODE_PLUGIN ),
		'python'        => plugins_url( 'js/tinymce-plugins/codemirror/mode/python/python.js', PASTACODE_PLUGIN ),
		'ruby'          => plugins_url( 'js/tinymce-plugins/codemirror/mode/ruby/ruby.js', PASTACODE_PLUGIN ),
		'sass'          => plugins_url( 'js/tinymce-plugins/codemirror/mode/sass/sass.js', PASTACODE_PLUGIN ),
		'shell'         => plugins_url( 'js/tinymce-plugins/codemirror/mode/shell/shell.js', PASTACODE_PLUGIN ),
		'sql'           => plugins_url( 'js/tinymce-plugins/codemirror/mode/sql/sql.js', PASTACODE_PLUGIN ),
		'xml'           => plugins_url( 'js/tinymce-plugins/codemirror/mode/xml/xml.js', PASTACODE_PLUGIN ),
		);

	$pvars['preview'] = get_option( 'pastacode_preview', 'y' );

	$pvars['language_mode'] = array(
		'php'          => array(
			'libs'      => array( 'xml', 'css', 'htmlmixed', 'clike', 'php' ),
			'mode'      => 'application/x-httpd-php',
			),
		'css'          => array(
			'libs'      => array( 'css' ),
			'mode'      => 'text/css',
			),
		'javascript'   => array(
			'libs'      => array( 'javascript' ),
			'mode'      => 'text/javascript',
			),
		'c'            => array(
			'libs'      => array( 'clike' ),
			'mode'      => 'text/x-csrc',
			),
		'cpp'          => array(
			'libs'      => array( 'clike' ),
			'mode'      => 'text/x-c++src',
			),
		'java'         => array(
			'libs'      => array( 'clike' ),
			'mode'      => 'text/x-java',
			),
		'sass'         => array(
			'libs'      => array( 'sass' ),
			'mode'      => 'text/x-sass',
			),
		'python'       => array(
			'libs'      => array( 'python' ),
			'mode'      => 'text/x-python',
			),
		'sql'          => array(
			'libs'      => array( 'sql' ),
			'mode'      => 'text/x-sql',
			),
		'ruby'         => array(
			'libs'      => array( 'ruby' ),
			'mode'      => 'text/x-ruby',
			),
		'haml'         => array(
			'libs'      => array( 'haml' ),
			'mode'      => 'text/x-haml',
			),
		'markup'       => array(
			'libs'      => array( 'xml', 'css', 'javascript', 'htmlmixed' ),
			'mode'      => 'htmlmixed',
			),
		'coffeescript' => array(
			'libs'      => array( 'coffeescript' ),
			'mode'      => 'text/x-coffeescript',
			),
		'apacheconf'   => array(
			'libs'      => array( 'shell' ),
			'mode'      => 'text/x-sh',
			),
		'bash'         => array(
			'libs'      => array( 'shell' ),
			'mode'      => 'text/x-sh',
			),
		'less'         => array(
			'libs'      => array( 'css' ),
			'mode'      => 'text/x-less',
			),
		'markdown'     => array(
			'libs'      => array( 'xml', 'markdown' ),
			'mode'      => 'text/x-markdown',
			),
		);

	foreach ( $fields as $k => $f ) {
		$field = array(
			'type' => 'textbox',
			'name' => $f['name'],
			'label' => $f['label'],
			'classes' => 'field-to-test field pastacode-args ' . implode( ' ', $f['classes'] ),
			);
		if ( ! isset( $f['placeholder'] ) ) {
			$field['multiline'] = true;
			$field['minWidth'] = 300;
			$field['minHeight'] = 100;
		} else {
			$field['tooltip'] = $f['placeholder'];
		}
		$new_fields[] = $field;
	}

	$pvars['fields']      = $new_fields;
	$pvars['extendIcon']  = plugins_url( 'images/expand-editor.png', PASTACODE_PLUGIN );
	$pvars['extendText']  = __( 'Expand editor', 'pastacode' );
	$pvars['base']        = plugins_url( '/', PASTACODE_PLUGIN );
	$pvars['textLang']    = $langs;
	$pvars['editorTheme'] = get_option( 'pastacode_bo_style', 'ambiance' );
	$pvars['tooltip']     = __( 'Insert a code', 'pastacode' );

	// Print Vars
	$pvars = json_encode( apply_filters( 'pastacode_tinymcevars', $pvars ) );
	echo '<script>var pastacodeText=' . $text . ';var pastacodeVars=' . $pvars . ';</script>';
}

/**
 * pastacode_bbpress_compat
 *
 * @since  1.7 Pastacode now comaptible with bbPress
 */
add_action( 'template_redirect', 'pastacode_bbpress_compat' );
function pastacode_bbpress_compat() {
	if ( ! is_admin() && function_exists( 'is_bbpress' ) && is_bbpress() ) {
		add_filter( 'bbp_after_get_the_content_parse_args', 'pastacode_bbpress_tinymce_settings' );
		function pastacode_bbpress_tinymce_settings( $r ) {
			$r['tinymce']   = true;
			$r['teeny']     = false;
			$r['quicktags'] = false;
			return $r;
		}

		add_filter( 'pastacode_ajax', '__return_true' );

		add_filter( 'bbp_get_topic_content', 'do_shortcode' );
		add_filter( 'bbp_get_reply_content', 'do_shortcode' );

		wp_enqueue_script( 'mce-view' );
		pastacode_shortcodes_mce_css();

		add_filter( 'mce_buttons', 'bbp_pastacode_register_button', 10, 2 );
		add_filter( 'mce_buttons_2', '__return_empty_array' );
		add_filter( 'mce_external_plugins', 'pastacode_script_tiny' );
	}
}

function bbp_pastacode_register_button( $buttons, $editor_id ) {
	array_push( $buttons, 'pcb' );
	foreach ( array( 'formatselect', 'alignleft', 'aligncenter', 'alignright', 'wp_more', 'hr', 'fullscreen', 'wp_adv' ) as $elem ) {
		if ( false !== ( $key = array_search( $elem, $buttons ) ) ) {
			unset( $buttons[ $key ] );
		}
	}
	return $buttons;
}

add_action( 'template_redirect', 'pastacode_on_comments' );
function pastacode_on_comments() {
	if ( ! is_admin() && is_singular() && 'y' == get_option( 'pastacode_comments_opt' ) ) {
		add_filter( 'comment_form_field_comment', 'wabeo_pastacode_comment_editor' );
		add_filter( 'comment_text', 'pastacode_shortcode_in_comments', 9 );
	}
}

function wabeo_pastacode_comment_editor() {
	global $post;
	wp_enqueue_script( 'mce-view' );
	wp_enqueue_script( 'pastacode-move-comment-form', plugins_url( 'js/front-pastacode-comments.js', PASTACODE_PLUGIN ), false, PASTACODE_VERSION );
	pastacode_shortcodes_mce_css();
	add_filter( 'pastacode_ajax', '__return_true' );
	add_filter( 'mce_buttons', 'bbp_pastacode_register_button', 10, 2 );
	add_filter( 'mce_buttons_2', '__return_empty_array' );
	add_filter( 'mce_external_plugins', 'pastacode_script_tiny' );
	ob_start();

	wp_editor( '', 'comment', array(
		'teeny'         => false,
		'quicktags'     => false,
		'media_buttons' => false,
		'tinymce'       => true,
	) );

	$editor = ob_get_contents();

	ob_end_clean();

	$editor = str_replace( 'post_id=0', 'post_id=' . get_the_ID(), $editor );

	return $editor;
}

function pastacode_shortcode_in_comments( $content ) {
	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	$tagnames = array( 'pastacode' );
	$content = do_shortcodes_in_html_tags( $content, false, $tagnames );

	$pattern = get_shortcode_regex( $tagnames );
	$content = preg_replace_callback( "/$pattern/", 'do_shortcode_tag', $content );
	$content = unescape_invalid_shortcodes( $content );

	return $content;
}

add_filter( 'wp_editor_settings', 'pastacode_admin_comment_editor_settings', 10, 2 );
function pastacode_admin_comment_editor_settings( $settings, $editor_id ) {
	if ( is_admin() && 'content' == $editor_id ) {
		$screen = get_current_screen();
		if ( 'comment' == $screen->base && 'edit-comments' == $screen->parent_base ) {
			wp_enqueue_script( 'mce-view' );
			add_filter( 'mce_buttons_2', '__return_empty_array' );
			add_filter( 'mce_external_plugins', 'pastacode_script_tiny' );
			$settings = array(
				'teeny'         => false,
				'quicktags'     => false,
				'media_buttons' => false,
				'tinymce'       => true,
			);
		}
	}
	return $settings;
}
