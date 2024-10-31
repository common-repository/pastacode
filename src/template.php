<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php 
	wp_enqueue_script( 'prismjs' );
	wp_enqueue_style( 'prismcss' );
	wp_enqueue_script( 'prism-normalize-whitespace' );
	wp_enqueue_style( 'prism-linenumbercss' );
	wp_enqueue_script( 'prism-linenumber' );

	if ( ! empty( $attributes['highlight'] ) && preg_match( '/([0-9-,]+)/', $attributes['highlight'] ) ) {
		$highlight_val = ' data-line="' . $attributes['highlight'] . '"';
		wp_enqueue_script( 'prism-highlight' );
		wp_enqueue_style( 'prism-highlightcss' );
	} else {
		$highlight_val = '';
	}

	if ( $attributes['lang'] == 'treeview' ) {
		wp_enqueue_script( 'prism-treeview' );
		wp_enqueue_style( 'prism-treeviewcss' );
	}
	
	$ln_class = $attributes['linenumbers'] ? ' line-numbers' : '';

	$source = pastacode_get_source( $attributes, '' );

	//Code info
	$about_code = array();
	$about_code[] = '<div class="code-embed-infos">';
	if ( isset( $source['url'] ) ) {
		$about_code[] = '<a href="' . esc_url( $source['url'] ) . '" title="' . sprintf( esc_attr__( 'See %s', 'pastacode' ), $source['name'] ) . '" target="_blank" class="code-embed-name">' . esc_html( $source['name'] ) . '</a>';
	}
	if ( isset( $source['raw'] ) ) {
		$about_code[] = '<a href="' . esc_url( $source['raw'] ) . '" title="' . sprintf( esc_attr__( 'Back to %s' ), $source['name'] ) . '" class="code-embed-raw" target="_blank">' . __( 'view raw', 'pastacode' ) . '</a>';
	}
	if ( ! isset( $source['url'] ) && ! isset( $source['raw'] ) && isset( $source['name'] ) ) {
		$about_code[] = '<span class="code-embed-name">' . $source['name'] . '</span>';
	}
	$about_code[] = '</div>';

	//Wrap
	$output = array();
	$output[] = '<div class="code-embed-wrapper">';
	$data_start = isset( $source['start'] ) && is_int( $source['start'] ) ? intval( $source['start'] ) : '1';
	$data_line_offset = isset( $source['start'] ) && is_int( $source['start'] ) ? intval( $source['start'] ) - 1 : '0';
	$output[] = '<pre class="language-' . sanitize_html_class( $attributes['lang'] ) . ' code-embed-pre' . $ln_class . '" ' . $highlight_val . ' data-start="' . $data_start . '" data-line-offset="' . $data_line_offset . '"><code class="language-' . sanitize_html_class( $attributes['lang'] ) . ' code-embed-code">'
	. str_replace( PHP_EOL, '<br/>', $source['code'] ) .
	'</code></pre>';
	$output[] = '</div>';

	$pos = ( 'top' == get_option( 'pastacode_aboutcode_pos' ) ) ? 1 : 2;
	array_splice( $output, $pos, 0, $about_code );

	$output = implode( ' ', $output );

	echo $output;
	?>
</div>
