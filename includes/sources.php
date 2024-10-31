<?php

/**
 * Manual
 */
add_filter( 'pastacode_manual', '_pastacode_manual', 10, 3 );
function _pastacode_manual( $source, $atts, $content ) {
	extract( $atts );
	if ( $manual ) {
		$source['code'] = esc_html( urldecode( $manual ) );
	} elseif ( ! empty( $content ) ) {
		$reg = "/<code>(?:[\\n\\r]*)?([\\s\\S]+?)(?:[\\n\\r]*)?<\\/code>/mi";
		if ( preg_match( $reg, $content, $code ) ) {
			$source['code'] = esc_html( $code[1] );
		}
	}
	if ( isset( $atts['message'] ) && $atts['message'] ) {
		$source['name'] = esc_html( $message );
	}
	return $source;
}

/**
 * Github
 */
add_filter( 'pastacode_github', '_pastacode_github', 10, 2 );
function _pastacode_github( $source, $atts ) {
	extract( $atts );
	if ( $user && $repos && $path_id ) {
		$req  = wp_sprintf( 'https://api.github.com/repos/%s/%s/contents/%s', $user, $repos, $path_id );
		if ( isset( $revision ) && $revision ) {
			$req = add_query_arg( array( 'ref' => $revision ), $req );
		} else {
			$revision = 'master';
		}
		$code = wp_remote_get( $req, array(
			'headers' => array(
				'Accept' => 'application/vnd.github.v3.raw+json',
			),
		) );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
			$name = explode( '/', $path_id );
			$source['name'] = $name[ count( $name ) - 1 ];
			$source['code'] = esc_html( wp_remote_retrieve_body( $code ) );
			$source['url']  = wp_sprintf( 'https://github.com/%s/%s/blob/%s/%s', $user, $repos, $revision, $path_id );
			$source['raw']  = wp_sprintf( 'https://raw.github.com/%s/%s/%s/%s', $user, $repos, $revision, $path_id );
		} else {
			$req2 = wp_sprintf( 'https://raw.github.com/%s/%s/%s/%s', $user, $repos, $revision, $path_id );
			$code = wp_remote_get( $req2 );
			if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
				$name = explode( '/', $path_id );
				$source['name'] = $name[ count( $name ) - 1 ];
				$source['code'] = esc_html( wp_remote_retrieve_body( $code ) );
				$source['url']  = wp_sprintf( 'https://github.com/%s/%s/blob/%s/%s', $user, $repos, $revision, $path_id );
				$source['raw']  = $req2;
			}
		}
	}
	return $source;
}

/**
 * Gitlab
 */
add_filter( 'pastacode_gitlab', '_pastacode_gitlab', 10, 2 );
function _pastacode_gitlab( $source, $atts ) {
	extract( $atts );
	if ( $user && $repos && $path_id ) {
		if ( ! isset( $revision ) || ! $revision ) {
			$revision = 'master';
		}
		// https://gitlab.com/willybahuaud/pastacode/-/raw/master/back-dependencies.php
		$req  = wp_sprintf( 'https://gitlab.com/%s/%s/-/raw/%s/%s', $user, $repos, $revision, $path_id );
		$code = wp_remote_get( $req, array(
			'headers' => array(
				'Accept' => 'application/vnd.github.v3.raw+json',
			),
		) );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
			$name = explode( '/', $path_id );
			$source['name'] = $name[ count( $name ) - 1 ];
			$source['code'] = esc_html( wp_remote_retrieve_body( $code ) );
			$source['url']  = wp_sprintf( 'https://gitlab.com/%s/%s/-/blob/%s/%s', $user, $repos, $revision, $path_id );
			$source['raw']  = wp_sprintf( 'https://gitlab.com/%s/%s/-/raw/%s/%s', $user, $repos, $revision, $path_id );
		}
	}
	return $source;
}

/**
 * Gist
 */
add_filter( 'pastacode_gist', '_pastacode_gist', 10, 2 );
function _pastacode_gist( $source, $atts ) {
	extract( $atts );
	if ( $path_id ) {
		$req  = wp_sprintf( 'https://api.github.com/gists/%s', $path_id );
		$code = wp_remote_get( $req );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
			$data = json_decode( wp_remote_retrieve_body( $code ) );
			$source['url']  = $data->html_url;
			if ( $file && isset( $data->files->$file ) ) {
				$data = $data->files->$file;
			} else {
				$data = (array) $data->files;
				$data = reset( $data );
			}
			$source['name'] = $data->filename;
			$source['code'] = esc_html( $data->content );
			$source['raw']  = $data->raw_url;
		}
	}
	return $source;
}

/**
 * Bitbucket snippets
 */
add_filter( 'pastacode_bitbucketsnippets', '_pastacode_bitbucketsnippets', 10, 2 );
function _pastacode_bitbucketsnippets( $source, $atts ) {
	extract( $atts );
	if ( $path_id && $user ) {
		$req  = wp_sprintf( 'https://api.bitbucket.org/2.0/snippets/%s/%s', $user, $path_id );
		$code = wp_remote_get( $req );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
			$data = json_decode( wp_remote_retrieve_body( $code ) );
			if ( ! $data->is_private ) {
				if ( $file && isset( $data->files->$file ) ) {
					$source['name'] = $file;
					$data = $data->files->$file;
				} else {
					$source['name'] = key( $data->files );
					$data = (array) $data->files;
					$data = reset( $data );
				}
				$source['url']  = $data->links->html->href;
				$source['raw']  = $data->links->self->href;
				$source_code = wp_remote_get( $source['raw'] );
				if ( ! is_wp_error( $source_code ) && 200 == wp_remote_retrieve_response_code( $source_code ) ) {
					$source['code'] = esc_html( wp_remote_retrieve_body( $source_code ) );
				}
			}
		}
	}
	return $source;
}

/**
 * Bitbucket
 */
add_filter( 'pastacode_bitbucket', '_pastacode_bitbucket', 10, 2 );
function _pastacode_bitbucket( $source, $atts ) {
	extract( $atts );
	if ( $user && $repos && $path_id ) {
		$req  = wp_sprintf( 'https://bitbucket.org/api/2.0/repositories/%s/%s/src/%s/%s', $user, $repos, $revision, $path_id );

		$code = wp_remote_get( $req );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
			$source['name'] = basename( $path_id );
			$source['code'] = esc_html( wp_remote_retrieve_body( $code ) );
			$source['url']  = wp_sprintf( 'https://bitbucket.org/%s/%s/src/%s/%s', $user, $repos, $revision, $path_id );
			$source['raw']  = $req;
		}
	}
	return $source;
}

/**
 * File
 */
add_filter( 'pastacode_file', '_pastacode_file', 10, 2 );
function _pastacode_file( $source, $atts ) {
	extract( $atts );
	if ( $path_id ) {
		$upload_dir = wp_upload_dir();
		$path_id = str_replace( '../', '', $path_id );
		$req  = esc_url( trailingslashit( $upload_dir['baseurl'] ) . $path_id );
		$code = wp_remote_get( $req );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {

			$source['name'] = basename( $path_id );
			$source['code'] = esc_html( wp_remote_retrieve_body( $code ) );
			$source['url']  = ( $req );
		}
	}
	return $source;
}

/**
 * Pastebin
 */
add_filter( 'pastacode_pastebin', '_pastacode_pastebin', 10, 2 );
function _pastacode_pastebin( $source, $atts ) {
	extract( $atts );
	if ( $path_id ) {
		$req  = wp_sprintf( 'http://pastebin.com/raw.php?i=%s', $path_id );
		$code = wp_remote_get( $req );
		if ( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
			$source['name'] = $path_id;
			$source['code'] = esc_html( wp_remote_retrieve_body( $code ) );
			$source['url']  = wp_sprintf( 'http://pastebin.com/%s', $path_id );
			$source['raw']  = wp_sprintf( 'http://pastebin.com/raw.php?i=%s', $path_id );
		}
	}
	return $source;
}