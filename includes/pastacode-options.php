<?php

add_filter( 'pastacode_services', 'pastacode_default_services', 0 );
function pastacode_default_services( $services ) {
	$services = array(
		'manual'            => __( 'Write code', 'pastacode' ),
		'github'            => sprintf( __( 'Import code (%s)', 'pastacode' ), 'Github' ),
		'gist'              => sprintf( __( 'Import code (%s)', 'pastacode' ), 'Gist' ),
		'gitlab'            => sprintf( __( 'Import code (%s)', 'pastacode' ), 'Gitlab' ),
		'bitbucket'         => sprintf( __( 'Import code (%s)', 'pastacode' ), 'Bitbucket' ),
		'bitbucketsnippets' => sprintf( __( 'Import code (%s)', 'pastacode' ), 'Bitbucket Snippets' ),
		'pastebin'          => sprintf( __( 'Import code (%s)', 'pastacode' ), 'Pastebin' ),
		'file'              => sprintf( __( 'Import code (%s)', 'pastacode' ), __( 'File from uploads', 'pastacode' ) ),
	);
	return $services;
}

add_filter( 'pastacode_langs', 'pastacode_default_langs', 0 );
function pastacode_default_langs( $langs ) {
	$langs  = array(
		'markup'       => 'HTML', //
		'css'          => 'CSS', //
		'javascript'   => 'JavaScript', //
		'php'          => 'PHP', //
		'c'            => 'C', //
		'csharp'       => 'C#', //
		'cpp'          => 'C++', //
		'java'         => 'Java', //
		'sass'         => 'Sass', //
		'python'       => 'Python', //
		'sql'          => 'SQL', //
		'ruby'         => 'Ruby', //
		'coffeescript' => 'CoffeeScript', //
		'bash'         => 'Bash', //
		'apacheconf'   => 'Apache', //
		'less'         => 'Less', //
		'haml'         => 'HAML',
		'git'          => 'Git',
		'haskell'      => 'Haskell', //
		'markdown'     => 'Markdown', //
		'typescript'   => 'Typescript',
		'treeview'     => __( 'Arborescence', 'pastacode' ),
	);
	return $langs;
}

add_filter( 'pastacode_fields', 'pastacode_default_fields', 0 );
function pastacode_default_fields( $fields ) {
	$upload_dir = wp_upload_dir();
	$fields = array(
		'username' => array( 'classes' => array( 'github','bitbucket', 'bitbucketsnippets', 'gitlab' ), 'label' => __('User of repository', 'pastacode'), 'placeholder' => __( 'John Doe', 'pastacode' ), 'name' => 'user' ),
		'repository' => array( 'classes' => array( 'github','bitbucket', 'gitlab' ), 'label' => __('Repository', 'pastacode'), 'placeholder' => __( 'pastacode', 'pastacode' ), 'name' => 'repos' ),
		'path-id' => array( 'classes' => array( 'gist', 'pastebin', 'bitbucketsnippets' ), 'label' => __('Code ID', 'pastacode'), 'placeholder' => '123456', 'name' => 'path_id' ),
		'path-repo' => array( 'classes' => array( 'github','bitbucket','gitlab' ), 'label' => __('File path inside the repository', 'pastacode'), 'placeholder' => __( 'bin/foobar.php', 'pastebin' ), 'name' => 'path_id'  ),
		'path-up' => array( 'classes' => array( 'file' ), 'label' => sprintf( __('File path relative to %s', 'pastacode'), esc_html( $upload_dir['baseurl'] ) ), 'placeholder' => date( 'Y/m' ).'/source.txt', 'name' => 'path_id'  ),
		'revision' => array( 'classes' => array( 'github','bitbucket','gitlab' ), 'label' => __('Revision', 'pastacode'), 'placeholder' => __('master', 'pastacode'), 'name' => 'revision'  ),
		'manual' => array( 'classes' => array( 'manual' ), 'label' => __('Code', 'pastacode'), 'name' => 'manual'  ),
		'message' => array( 'classes' => array( 'manual' ), 'label' => __('Code title', 'pastacode'),'placeholder' => __('title', 'pastacode'), 'name' => 'message'  ),
		'file' => array( 'classes' => array( 'gist', 'bitbucketsnippets' ), 'label' => __('Filename (with extension)', 'pastacode'), 'placeholder' => 'foobar.txt', 'name' => 'file'  ),
		'pastacode-highlight' => array( 'classes' => array( 'manual', 'github', 'gist', 'bitbucket', 'pastebin', 'file', 'bitbucketsnippets', 'gitlab' ), 'label' => __('Highlited lines', 'pastacode'), 'placeholder' => '1,2,5-6', 'name' => 'highlight' ),
		'pastacode-lines' => array( 'classes' => array( 'github', 'gist', 'bitbucket', 'pastebin', 'file', 'bitbucketsnippets', 'gitlab' ), 'label' => __('Visibles lines', 'pastacode'), 'placeholder' => '1-20', 'name' => 'lines' )
	);
	return $fields;
}