<?php
/*
Plugin Name: BuddyPress Wiki Page State
Plugin URI: http://wordpress.org/extend/plugins/bp-wiki-pagestate/
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X7SZG3SM4JYGY
Description: Allows setting a page state "Broken" or "Works" for bp-wiki pages
Version: 0.5.4
Requires at least: WP 2.9.2, BuddyPress 1.2.3
Tested up to: WP 3.0, BuddyPress 1.2.4.1
License: GPL
Author: Normen Hansen
Author URI: http://www.bitwaves.de/
Site Wide Only: true
*/

function bp_wiki_pagestate_init() {
    require( dirname( __FILE__ ) . '/include/bp-wiki-pagestate-widget.php' );
    bp_wiki_pagestate_register_widgets();
}
add_action( 'bp_init', 'bp_wiki_pagestate_init' );

function bp_wiki_pagestate_add_js() {
    wp_enqueue_script( 'bp-wiki-pagestate-js', WP_PLUGIN_URL . '/bp-wiki-pagestate/include/js/pagestate.js' );
}
add_action( 'init', 'bp_wiki_pagestate_add_js')

?>