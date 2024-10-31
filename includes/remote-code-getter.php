<?php
/**
 * Get sources
 */
add_action( 'rest_api_init', 'pastacode_rest_route' );
function pastacode_rest_route() {
	register_rest_route( 'pastacode/v2', '/retrieve_code', [
		'methods'   => 'POST',
		'callback' => 'pastacode_read_source',
		'permission_callback' => function() { return ''; }
	] );
}

function pastacode_read_source( $request ) {
	$args = array(
		'provider'      => $request->get_param('provider'),
		'user'          => $request->get_param('user'),
		'path_id'       => $request->get_param('path_id'),
		'repos'         => $request->get_param('repos'),
		'revision'      => $request->get_param('revision') ?? 'master',
		'lines'         => $request->get_param('lines'),
		'message'       => $request->get_param('message'),
		'file'          => $request->get_param('file'),
	);
	$reponse = pastacode_get_source( $args, '' );
	if ( $reponse ) {
		return wp_send_json( $reponse );
	}
}

function pastacode_get_source( $atts, $content  = '') {
	if ( empty( $atts['provider'] ) && ! empty( $content ) ) {
		$atts['provider'] = md5( $content );
	}

	$code_embed_transient = 'pastacode_' . substr( md5( serialize( $atts ) ), 0, 14 );

	$time = get_option( 'pastacode_cache_duration', DAY_IN_SECONDS * 7 );

	if ( 'manual' == $atts['provider'] ) {
		$time = -1;
	}

	if ( -1 == $time || ! $source = get_transient( $code_embed_transient ) ) {

		$source = apply_filters( 'pastacode_'.$atts['provider'], array(), $atts, $content );

		if ( ! empty( $source['code'] ) ) {
			$source['code'] = rtrim( $source['code'], "\n" );

			//Wrap lines
			if ( ! empty( $atts['lines'] ) && ! empty( $atts['provider'] ) && ( $lines = $atts['lines'] ) && ( 'manual' != $atts['provider'] ) ) {
				$lines = array_map( 'intval', explode( '-', $lines ) );
				if ( ! isset( $lines[1] ) && isset( $lines[0] ) ) {
					$lines[1] = $lines[0];
				}
				$source['code'] = implode( "\n", array_slice( preg_split( '/\r\n|\r|\n/', $source['code'] ), $lines[0] - 1, ( $lines[1] - $lines[0] ) + 1 ) );
				$source['start'] = $lines[0];
			}
			if ( $time >- 1 ) {
				set_transient( $code_embed_transient, $source, $time );
			}
		}
	}
	return $source;
}

add_action( 'wp_ajax_pastacode-get-source-code', 'pastacode_ajax_get_source_code' );
add_action( 'wp_ajax_nopriv_pastacode-get-source-code', 'pastacode_ajax_get_source_code' );
function pastacode_ajax_get_source_code() {
	$args = $_POST;
	unset( $args['action'] );
	$source = pastacode_get_source_code_ajax( $args );
	wp_send_json_success( $source );
}

function pastacode_get_source_code_ajax( $args ) {
	$args = wp_parse_args( $args, array(
		'provider'      => '',
		'user'          => '',
		'path_id'       => '',
		'repos'         => '',
		'revision'      => 'master',
		'lines'         => '',
		'lang'          => 'markup',
		'highlight'     => '',
		'message'       => '',
		'file'          => '',
		'manual'        => '',
		'linenumbers'   => 'n',
		'showinvisible' => 'n',
	) );

	$source = pastacode_get_source( $args );

	if ( ! empty( $source['code'] ) ) {
		$code = preg_split( '/\r\n|\r|\n/', $source['code'] );
		$source['more'] = count( $code ) > 10;
		$source['code'] = implode( PHP_EOL, array_slice( $code, 0, 10 ) );
		$source['code'] = stripslashes_deep( $source['code'] );
	}

	return $source;
}