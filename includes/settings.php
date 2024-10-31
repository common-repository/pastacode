<?php

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pastacode_settings_action_links', 10, 2 );
function pastacode_settings_action_links( $links, $file ) {
	if ( current_user_can( 'manage_options' ) )
		array_unshift( $links, '<a href="' . admin_url( 'options-general.php?page=pastacode' ) . '">' . __( 'Settings' ) . '</a>' );
	return $links;
}

add_filter( 'plugin_row_meta', 'pastacode_plugin_row_meta', 10, 2 );
function pastacode_plugin_row_meta( $plugin_meta, $plugin_file ) {
	if ( plugin_basename( __FILE__ ) == $plugin_file ){
		$last = end( $plugin_meta );
		$plugin_meta = array_slice( $plugin_meta, 0, -2 );
		$a = array();
		$authors = array(
			array( 'name' => 'Willy Bahuaud', 'url' => 'https://wabeo.fr' ),
			array( 'name' => 'Julio Potier', 'url' => 'http://www.boiteaweb.fr' ),
		);
		foreach ( $authors as $author ) {
			$a[] = '<a href="' . $author['url'] . '" title="' . esc_attr__( 'Visit author homepage' ) . '">' . $author['name'] . '</a>';
		}
		$a = sprintf( __( 'By %s' ), wp_sprintf( '%l', $a ) );
		$plugin_meta[] = $a;
		$plugin_meta[] = $last;
	}
	return $plugin_meta;
}

add_filter( 'admin_post_pastacode_drop_transients', 'pastacode_drop_transients', 10, 2 );
function pastacode_drop_transients() {
	if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'pastacode_drop_transients' ) ) {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_pastacode_%'" );
		wp_redirect( wp_get_referer() );
	} else {
		wp_nonce_ays( '' );
	}
}

add_action( 'admin_menu', 'pastacode_create_menu' );
function pastacode_create_menu() {
	add_options_page( 'Pastacode '. __( 'Settings' ), 'Pastacode', 'manage_options', 'pastacode', 'pastacode_settings_page' );
	register_setting( 'pastacode', 'pastacode_cache_duration' );
	register_setting( 'pastacode', 'pastacode_style' );
	register_setting( 'pastacode', 'pastacode_bo_style' );
	register_setting( 'pastacode', 'pastacode_cm_style' );
	register_setting( 'pastacode', 'pastacode_linenumbers' );
	register_setting( 'pastacode', 'pastacode_showinvisible' );
	register_setting( 'pastacode', 'pastacode_aboutcode_pos' );
	register_setting( 'pastacode', 'pastacode_preview' );
	register_setting( 'pastacode', 'pastacode_comments_opt' );
	register_setting( 'pastacode', 'pastacode_legacy' );
}

function pastacode_setting_callback_function( $args ) {

	extract( $args );

	$value_old = get_option( $name );

	echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '">';
	foreach ( $options as $key => $option ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value_old==$key, true, false ) . '>' . esc_html( $option ) . '</option>';
	}
	echo '</select>';
	if ( ! empty( $desc ) ) {
		echo '<p>' . wp_kses( $desc, array( 'a' => array( 'href' => array() ), 'br' => array(), 'em' => array() ) ) . '</p>';
	}
}

function pastacode_legacy_descr() {
	_e( '3.0 release is optimised to work with block editor. In this section you can cutof compatibility with the old shortcode, or configure settings used by classic editor, comments, ajax… it wont collide with general settings.', 'pastacode' );
}

function pastacode_settings_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h1>Pastacode v<?php echo PASTACODE_VERSION; ?></h1>
	<?php
		$v5_themes = array(
			'ambiance'                => 'Ambiance',
			'3024-day'                => '3024 day',
			'3024-night'              => '3024 night',
			'abcdef'                  => 'abcdef',
			'abcdef'                  => 'abcdef',
			'base16-dark'             => 'Base16 Dark',
			'base16-light'            => 'Base16 Light',
			'bespin'                  => 'Bespin',
			'blackboard'              => 'Blackboard',
			'dracula'                 => 'Dracula',
			'eclipse'                 => 'Eclipse',
			'elegant'                 => 'Elegant',
			'erlang-dark'             => 'Erlang Dark',
			'hopscotch'               => 'Hopscotch',
			'icecoder'                => 'Icecoder',
			'isotope'                 => 'Isotope',
			'lesser-dark'             => 'Lesser Dark',
			'liquibyte'               => 'Liquibyte',
			'material'                => 'Material',
			'mbo'                     => 'Mbo',
			'midnight'                => 'Midnight',
			'monokai'                 => 'Monokai',
			'neat'                    => 'Neat',
			'neo'                     => 'Neo',
			'night'                   => 'Night',
			'paraiso-dark'            => 'Paraiso Dark',
			'paraiso-light'           => 'Paraiso Light',
			'pastel-on-dark'          => 'Pastel on Dark',
			'railscasts'              => 'railscasts',
			'rubyblue'                => 'Rubyblue',
			'seti'                    => 'Seti',
			'solarized'               => 'Solarized',
			'the-matrix'              => 'The Matrix',
			'tomorrow-night-bright'   => 'Tomorrow Night Bright',
			'tomorrow-night-eighties' => 'Tomorrow Night Eighties',
			'twilight'                => 'Twilight',
			'vibrant-ink'             => 'Vibrant Ink',
			'xq-dark'                 => 'XQ Dark',
			'xq-light'                => 'XQ Light',
			'yeti'                    => 'Yeti',
			'zenburn'                 => 'Zenburn',
		);

		$v6_themes = array(
			'abcdef'        => 'Abcdef',
			'androidstudio' => 'Androidstudio',
			'atomone'       => 'Atomone',
			'bbedit'        => 'Bbedit',
			'bespin'        => 'Bespin',
			'darcula'       => 'Darcula',
			'dracula'       => 'Dracula',
			'duotoneLight'  => 'Duotone Light',
			'duotoneDark'   => 'Duotone Dark',
			'eclispe'       => 'Eclipse',
			'githubLight'   => 'Github Light',
			'githubDark'    => 'Github Dark',
			'gruvbox-dark'  => 'Gruvbox Dark',
			'okaida'        => 'Okaidia',
			'sublime'       => 'Sublime',
			'xcodeLight'    => 'Xcode Light',
			'xcodeDark'     => 'Xcode Dark',
		);

		add_settings_section( 'pastacode_setting_section',
			__( 'General Settings', 'pastacode' ),
			'__return_false',
			'pastacode',
		);

		add_settings_section( 'pastacode_setting_legacy_section',
			__( 'Shortcode settings (classic editor compatibility)', 'pastacode' ),
			'pastacode_legacy_descr',
			'pastacode', 
			array( 'section_class' => 'legacy-section', 'before_section' => '<div class="%s">', 'after_section' => '</div>' )
		);

		add_settings_field( 'pastacode_legacy',
			__( 'Classic editor compatibility', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_legacy_section',
			array(
				'options' => array(
					'y' => __( 'Yes', 'pastacode' ),
					'n' => __( 'No', 'pastacode' ),
					),
				'name' => 'pastacode_legacy',
				'desc' => '<em>' . __( 'Select « yes » to support classic editor.' ) . '</em>'
			) );
		
		add_settings_field( 'pastacode_style',
			__( 'Syntax Coloration Style', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_section',
			array(
				'options' => array(
					'prism'          => 'Prism',
					'prism-dark'     => 'Dark',
					'prism-funky'    => 'Funky',
					'prism-coy'      => 'Coy',
					'prism-okaidia'  => 'Okaïdia',
					'prism-tomorrow' => 'Tomorrow',
					'prism-twilight' => 'Twilight',
					),
				'name' => 'pastacode_style',
			) );
		
		add_settings_field( 'pastacode_cm_style',
			__( 'Editor appareance', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_section',
			array(
				'options' => $v6_themes,
				'name' => 'pastacode_cm_style',
			) );
	
		add_settings_field( 'pastacode_aboutcode_pos',
			__( 'Code description location', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_section',
			array(
				'options' => array(
					'bottom' => __( 'Below code', 'pastacode' ),
					'top'    => __( 'Above code', 'pastacode' ),
					),
				'name' => 'pastacode_aboutcode_pos',
			) );
	
		add_settings_field( 'pastacode_linenumbers',
			__( 'Show line numbers', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_legacy_section',
			array(
				'options' => array(
					'y' => __( 'Yes', 'pastacode' ),
					'n' => __( 'No', 'pastacode' ),
					),
				'name' => 'pastacode_linenumbers',
				'class' => 'if-pastacode_legacy-n'
			) );
	
		add_settings_field( 'pastacode_showinvisible',
			__( 'Show invisible chars', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_section',
			array(
				'options' => array(
					'y' => __( 'Yes', 'pastacode' ),
					'n' => __( 'No', 'pastacode' ),
					),
				'name' => 'pastacode_showinvisible',
			) );
	
		add_settings_field( 'pastacode_cache_duration',
			__( 'Caching duration', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_section',
			array(
				'options' => array(
					HOUR_IN_SECONDS      => sprintf( __( '%s hour' ), '1' ),
					HOUR_IN_SECONDS * 12 => __( 'Twice Daily' ),
					DAY_IN_SECONDS       => __( 'Once Daily' ),
					DAY_IN_SECONDS * 7   => __( 'Once Weekly', 'pastacode' ),
					0                    => __( 'Never reload', 'pastacode' ),
					-1                   => __( 'No cache (dev mode)', 'pastacode' ),
					),
				'name' => 'pastacode_cache_duration',
			) );
	
		add_settings_field( 'pastacode_preview',
			__( 'Show preview on editor', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_legacy_section',
			array(
				'options' => array(
					'y' => __( 'Yes', 'pastacode' ),
					'n' => __( 'No', 'pastacode' ),
					),
				'name' => 'pastacode_preview',
				'class' => 'if-pastacode_legacy-n'
			) );
	
		add_settings_field( 'pastacode_comments_opt',
			__( 'Activate Pastacode for comments', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_legacy_section',
			array(
				'options' => array(
					'y' => __( 'Yes', 'pastacode' ),
					'n' => __( 'No', 'pastacode' ),
					),
				'name' => 'pastacode_comments_opt',
				'desc' => '<em>' . esc_html__( 'Experimental mode', 'pastacode' ) . '</em>',
				'class' => 'if-pastacode_legacy-n'
			) );
	
		add_settings_field( 'pastacode_bo_style',
			__( 'Editor appareance (legacy)', 'pastacode' ),
			'pastacode_setting_callback_function',
			'pastacode',
			'pastacode_setting_legacy_section',
			array(
				'options' => $v5_themes,
				'name' => 'pastacode_bo_style',
				'class' => 'if-pastacode_legacy-n'
			) );
	
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'pastacode' );
			echo '<div class="col-section__wrapper">';
			/**
			 * @todo disable legacy support if checked
			 */
			do_settings_sections( 'pastacode' );
			echo '</div>';
			$url = wp_nonce_url( admin_url( 'admin-post.php?action=pastacode_drop_transients' ), 'pastacode_drop_transients' );
			global $wpdb;
			$transients = $wpdb->get_var( "SELECT count(option_name) FROM $wpdb->options WHERE option_name LIKE '_transient_pastacode_%'" );
			echo '<p class="submit">';
				submit_button( '', 'primary large', 'submit', false );
				echo ' <a href="' . esc_attr( $url ) . '" class="button button-large button-secondary">' . esc_html__( 'Purge cache', 'pastacode' ) . ' (' . (int) $transients . ')</a>';
			echo '</p>';
			?>
		</form>
		<style>
			
			.legacy-section{
				background:#d4d4d4;
				padding:1rem 2rem;
				max-width:750px;
				border:1px solid #ccc;
			}
		</style>
		<script>
			function toggleLegacyOpts(){
				if ( elem = document.getElementById('pastacode_legacy') ) {
					var v = elem.value;
					var o = document.querySelectorAll('.if-pastacode_legacy-n');
					o.forEach(function(el){
						el.style.display = (v == 'y' ? 'table-row' : 'none');
					});
				}
			}
			document.getElementById('pastacode_legacy').addEventListener('change',toggleLegacyOpts);
			toggleLegacyOpts();
		</script>
	</div>
	<?php
}

register_activation_hook( __FILE__, 'pastacode_activation' );
function pastacode_activation() {
	add_option( 'pastacode_cache_duration', DAY_IN_SECONDS * 7 );
	add_option( 'pastacode_style', 'prism' );
	add_option( 'pastacode_showinvisible', 'n' );
	add_option( 'pastacode_linenumbers', 'n' );
	add_option( 'pastacode_preview', 'y' );
	add_option( 'pastacode_comments_opt', 'n' );
	add_option( 'pastacode_legacy', 'y' );
	add_option( 'pastacode_cm_style', 'sublime' );
}

register_uninstall_hook( __FILE__, 'pastacode_uninstaller' );
function pastacode_uninstaller() {
	delete_option( 'pastacode_cache_duration' );
	delete_option( 'pastacode_style' );
	delete_option( 'pastacode_cm_style' );
}