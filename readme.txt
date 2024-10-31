=== Pastacode ===
Contributors: willybahuaud, juliobox
Tags: block, syntax, code, sourcecode, github, bitbucket, gist, prismjs, codemirror, Formatting, highlight, html, php, CSS, embed, bbPress, comment
Requires at least: 3.1
Tested up to: 6.1.1
Stable tag: 3.0.1
License: GPLv2 or later
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RP2CK8K32JDPE

Use Pastacode to add code into your posts with the awesome PrismJs coloration library. So, past'a code!

== Description ==

With Pastacode, you can easily add code into your posts with the awesome PrismJs coloration library. 
You can insert source code into the post editor, wrinting it directly in the editor (using a gutenberg bloc or classic editor), from a file, or from webservices like GitHub, Gitlab, Gist, Pastebin, BitBucket or BitBucket snippets. Webservices responses are cached in order to avoid too many HTTP requests.
It also work in comments and bbPress topics and replies.

Don't worry about posts updates while upgrading codes!

Pastacode allows to enhance your snippets using PrismJs plugins (highlightning lines, link functions...).

== Screenshots ==

1. Edit your code with the Pastacode block
2. How it's renderer in editor (highlighted by Prism)
3. You can import a snippets hosted on many providers (gitlab, github…)
4. If you don't use block editor, you can use another UI, base on a tinyMCE plugin (work in the classic block, or with classic editor)
5. Here the Treeview lang, to share directory structure

== Frequently Asked Questions ==

*For more information, please visit [Pastacode Wiki](http://pastacode.wabeo.fr/personnaliser-pastacode/)*

= Which programming languages are available? =

* HTML
* CSS
* JavaScript
* PHP
* C
* C#
* C++
* Java
* Sass
* Python
* SQL
* Ruby
* CoffeeScript
* Bash
* Apache
* Less
* HAML
* Git command line
* Haskell
* Markdown
* Typescript
* Treeview (directory structure)

If you use another syntax highligther plugins, migration scripts are available :)

= What is the Treeview language? =

You can use it to display a directory structure. Use pipes, backticks and hyphens like this:

<code>
root_folder/
|-- a first folder/ 
|   |-- holidays.mov
|   |-- javascript-file.js
|   &#96;-- some_picture.jpg
|-- documents/
|   |-- spreadsheet.xls
|   |-- manual.pdf
|   |-- document.docx
|   &#96;-- presentation.ppt
|         &#96;-- test
&#96;-- README.md
</code>

= How to setup a custom cache expiration ? =

Paste these lines into your functions.php theme file:
<code>
add_filter( 'option_pastacode_cache_duration', 'my_pastacode_cache_duration' );
function my_pastacode_cache_duration( $duration ) {
    $duration = DAY_IN_SECOND*3; // 3 days
    return $duration;
}
</code>

= How to change the color scheme ? =

7 different color schemes are included, you can switch theme under Settings > Pastacode.

You can also build yours:

Paste these lines into your functions.php theme file:
<code>
add_action( 'wp_enqueue_scripts', 'custom_enqueue_script', 11 );
function custom_enqueue_script() {
    $urlofmynewscheme = get_stylesheet_directory_uri() . '/prism-okaida-willy.css'; //this is an example
    wp_deregister_style( 'prismcss' );
    wp_register_style( 'prismcss', $urlofmynewscheme, false, '1', 'all' );
}
</code>

Get inspired of [the default scheme](https://raw.githubusercontent.com/willybahuaud/pastacode-samples/master/default-style.css) to build your schemes

= How to filter supported languages ? =

Paste these lines into your functions.php theme file:
<code>
//If you just want php, html, css and javascript support
add_filter( 'pastacode_langs', '_pastacode_langs' );
function _pastacode_langs( $langs ) {
    $langs  = array(
        'php'          => 'PHP',
        'markup'       => 'HTML',
        'css'          => 'CSS',
        'javascript'   => 'JavaScript', );
    return $langs;
}
</code>

= Ajax compatibility =

To enable Pastacode on ajax based websites, it need two steps:

1. Turn on Legacy support in the settings panel
2. Paste this line into your functions.php theme file: `add_filter( 'pastacode_ajax', '__return_true' );`
3. After each change on your DOM, you will have to run this javascript function: `Prism.highlightAll();`

= How to add a new provider ? =

Paste these lines into your functions.php theme file:
<code>
//Take WordPress SVN, for example
//register a provider
add_filter( 'pastacode_services', '_pastacode_services' );
function _pastacode_services( $services ) {
    $services['wordpress'] = 'core.svn.wordpress.org';
    return $services;
}

//Define pastabox lightbox inputs
add_action( 'pastacode_fields', '_pastacode_fields' );
function _pastacode_fields( $fields ) { 
    $fields['wordpress'] = array(  // 'wordpress' or 'whatever'
        'classes'     => array( 'wordpress' ), // same value as the key
        'label'       => sprintf( __('File path relative to %s', 'pastacode'), 'http://core.svn.wordpress.org/' ), 
        'placeholder' =>'trunk/wp-config-sample.php', //if placeholder isn't defined, it will be a textarea
        'name'        => 'path_id' //these value return shortcode attribute (path_id, repos, name, user, version)
        );
    $fields['pastacode-lines']['classes'][] = 'wordpress'; // Add ability to select lines
    $fields['pastacode-highlight']['classes'][] = 'wordpress'; // Add ability to highlight somes

    return $fields;
}

//Build the function to retrieve the code
// "pastacode_wordpress" hook name (1st param) = "pastacode_" + "wordpress" or "whatever"
add_action( 'pastacode_wordpress', '_pastacode_wordpress', 10, 2 );
function _pastacode_wordpress( $source, $atts ) {
    extract( $atts );
    if( $path_id ) {
        $req  = wp_sprintf( 'http://core.svn.wordpress.org/%s', str_replace( 'http://core.svn.wordpress.org/', '', $path_id ) );
        $code = wp_remote_get( $req );
        if( ! is_wp_error( $code ) && 200 == wp_remote_retrieve_response_code( $code ) ) {
            $data = wp_remote_retrieve_body( $code );
            $source[ 'url' ]  = $req; //url to view source
            $source[ 'name' ] = basename( $req ); //filename
            $source[ 'code' ] = esc_html( $data ); //the code !!   
            //$source[ 'raw' ] contain raw source code. But there are no raw source code delivered by Wordpress SVN             
        }
    }
    return $source;
}
</code>

Do not add you root website!! A contributor can add the shortcode to point your "wp-config.php" to read it!!

== Installation ==

1. Unzip Pastacode into your plugin folder
2. Go to Pastacode settings, and configure your color scheme and cache expiration
3. Host your snippets on repositories (or localy)
4. To use:
  * With the block editor, use the Pastacode block
  * With classic-editor, use *Past'a code* button to embed your source code into articles

== Third Party ==

Pastacode use some third party components

- [PrismJS - by Lea Verou, Golmote, James DiGioia, Michael Schmidt & other contributors](https://github.com/PrismJS/prism/graphs/contributors)
- [WordPress create-block](https://www.npmjs.com/package/@wordpress/create-block)
- [CodeMirror 6](https://github.com/codemirror/CodeMirror)
- [CodeMirror6 Component for React](https://uiwjs.github.io/react-codemirror/)
- [He.js - by Mathias Bynens](https://github.com/mathiasbynens/he)

== Changelog ==

= 3.0.1 =
* 24 november 2022
* Fix: improve compatibility with html minifier, where line breaks are stripped from codes snippets 
* Fix: bug with apachconf language in legacy mod (shortcode)
* Thanks to WP marmite for notifying me these bugs

= 3.0 =
* 15 november 2022
* Gutenberg support! 🎉 Use the new shinny block to write/insert your code snippets
* Migrate automatically from the old shortcode to Gutenberg
* Support legacy if you use classic editor or classic block

= 2.1 =
* 9 august 2019
* fix issue with bitbucket api 1.0 depreciation
* gutenberg compatibility is coming soon . . .

= 2.0 =
* 15 december 2016
* compatibility with WordPress comments
* normalize withespace in PrismJs
* hide empty titles in manual snippets
* codemirror editing improvments
* hdpi icons
* TinyMCE smartphone compatibility
* PrismJS stylesheets and TinyMCE improvments
* new method to retrieve GitHub snippets (without base 64 encryption)
* fancy new website for demos
* fix: resolve bug while old shortcode conversion
* fix: conflict between manual code and `%`

= 1.8 =
* 22 august 2016
* Pastacode preview mode on tinyMCE views

= 1.7 =
* 19 august 2016
* Pastacode now compatible with bbPress

= 1.6 =
* 27 may 2016
* [CodeMirror](http://codemirror.net) is now used for editing manual code on backend
* manual shortcode improvements (this version will converts old « manual code » shortcodes to new ones. You’re invited to save your database before upgrade). 
This solves [problem reported by users with new lines feeds](https://wordpress.org/support/topic/pastacode-introducing-extra-line-feeds).
* support [Bitbucket snippets](https://confluence.atlassian.com/bitbucket/snippets-719095082.html) as a provider
* line-numbers css improvements
* fix bug with empty lines at the end of a snippet.

= 1.5.1 =
* 24 july 2015
* fix bug of code wrapper not removed [support](https://wordpress.org/support/topic/not-removed)

= 1.5 =
* 23 july 2015
* API views implementation. 
* fix bug when creating new shortcodes (persistent values)

= 1.4.2 =
* 21 january 2015
* can target a specific file inside a gist
* remove prismJS plugin demo file (index.html, inside the plugin rep)

= 1.4.1 =
* 20 january 2015
* Color Scheme optimisation (line number compatibility, space above and below…)
* You can select to [display only 1 line of code](https://wordpress.org/support/topic/unique-line-number?replies=1)
* New [website for documentation](http://pastacode.wabeo.fr) !

= 1.4 =
* 16 january 2015
* New feature: you can now edit your manual code into a full screen window
* update prism.js and prism plugins
* New option for display code description above or below code

= 1.3 =
* 5 may 2014
* TinyMCE Editor support improvment (visual placeholder on editor mode, new tinyMCE button...)
* Github API restriction fallback (support now more than 30 requests / hour)
* New ajax compatibility (using hook pastacode_ajax)
* Fix bug: No more disgracefull linebreaks on code view.

= 1.2.1 =
* 21 nov 2013
* Fix bug: when manual provider is selected, no cache.

= 1.2 =
* 15 oct 2013
* The modification of the cache duration do not purge cache anymore
* New button "Purge Cache" in option page, use it to delete all transients (they contains the responded source codes)
* Fix bug when updating option

= 1.1 =
* 12 oct 2013
* Hooks, hooks and hooks.
* Update shortcode format ("type" became "provider", and add "/" before the closing tag)

= 1.0 =
* 10 oct 2013
* Initial release
* Insert codes using a nice lightbox
* Import codes from file, Github, Gist, Pastebin or BitBucket
* 13 languages available
* 6 color schemes
* Cache support for webservices (default duration: 1 week)
