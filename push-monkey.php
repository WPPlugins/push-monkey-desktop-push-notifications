<?php
/*
 * Plugin Name: Push Monkey Native Desktop Push Notifications for WordPress
 * Plugin URI: https://wordpress.org/plugins/push-monkey-desktop-push-notifications/
 * Author: moWOW Studios
 * Description: Engage & delight your readers with Desktop Push Notifications - a new subscription channel directly to the desktop of your readers, without them installing any app. To start sending, just go to <a href="https://www.getpushmonkey.com?source=plugin_desc" target="_blank">getpushmonkey.com</a>, and register. Currently Chrome, Firefox and Safari (on OS X Mavericks and above) Push Notifications are active.
 * Version: 1.6.1
 * Stable Tag: 1.6.1
 * Author URI: http://www.getpushmonkey.com/?source=plugin
 * License: GPL2
 */

/*  
Push Monkey - Desktop Push Notifications for WordPress
Copyright (C) 2014 moWOW Studios (email : hey@mowowstudios.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* CHANGELOG:
 *
 * v 1.6.1 *
 * = new, shorter, subdomain for HTTP websites: snd.tc
 *
 * v 1.6.0
 * - bugfixing
 * - new delivery system of push notifications
 *
 * v 1.5.9
 * - bugfixing
 *
 * v 1.5.8
 * - switch to new API endpoints
 *
 * v 1.5.7
 * - bugfixing
 * 
 * v 1.5.6
 * - bugfixing
 *
 * v 1.5.5
 * - added a welcome notification to all subscribers
 * - added ability to customise the welcome notification
 *
 * v 1.5.0
 * - include an image with the notifications (Chrome Only) 
 *
 * v 1.4.9
 * - prepare a major feature
 *
 * v 1.4.5
 * - further bugfixing for PHP 5.3
 *
 * v 1.4.4
 * - bugfixing for PHP 5.3
 *
 * v 1.4.3
 * - bugfixing; loading segments failed sometimes
 *
 * v 1.4.2
 * - bugfixing for newer PHP versions
 *
 * v 1.4.1
 * - bugfixing
 *
 * v 1.4.0
 * - added segmentation
 * - changed default colors
 *
 * v 1.3.3
 * - fix permalink bugs
 *
 * v 1.3.2
 * - fix notifications that don't remain on the screen
 * - other bug fixes
 *
 * v 1.3.1
 * - fix missing URL after update
 * 
 * v 1.3 
 * - major overhaul of the system and back-end. HTTPS is no longer required.
 *
 * v 1.2
 * - allow the customisation of the subscribe button
 *
 * v 1.1.1
 * - fix CSS on banner
 * - remove banner on unsupported browsers
 *
 * v 1.1
 * - fix CSS conflicts
 * - fix some missing banner positions
 * - fix the rewrite rules
 *
 * v 1.0.2
 * - remove logging
 *
 * v 1.0.1
 * - fix layout issues
 * - add some missing files
 *
 * v 1.0
 * - added Chrome integration
 * - added Firefox integration
 * - fixed some layout bugs
 * - updated the subscription banners
 *
 * v 0.9.9.9.5
 * - fix CSS conflicts
 * - test on Wordpress 4.6.1
 *
 * v 0.9.9.9.4
 * - bugfixing: scheduled posts now send push notifications again.
 *
 * v 0.9.9.9.3
 * - update stats layout
 * - added new "Notification Format" feature. You can now configure what the notification content is. Currently two options are available: Post title and Post body OR 
 * custom title and post title.
 *
 * v 0.9.9.9.2
 * - add option to disable CTA banners on homepage only, while being enabled on all other pages
 *
 * v 0.9.9.9.1
 * - fix PHP 5.2 compatibility
 *
 * v 0.9.9.9.0
 * - language adjusting
 *
 * v 0.9.9.8.9
 * - allow CTA Banner customisation of color and text
 *
 * v 0.9.9.8.8
 * - caching update
 * - update Sign Up screen
 *
 * v 0.9.9.8.7
 * - bugfixing
 *
 * v 0.9.9.8.6
 * - bugfixing
 *
 * v 0.9.9.8.5
 * - bugfixing
 *
 * v 0.9.9.8.4
 * - remove shortcodes from Push Notification
 * - fix double escaping of Custom Push Notifications
 * - add Welcome Notice
 * - fix notification before trial expires
 *
 * v 0.9.9.8.3
 * - display notification before trial plan expires
 *
 * v 0.9.9.8.2
 * - display notification when the trial plan expired
 *
 * v 0.9.9.8.1
 * - bugfix
 *
 * v 0.9.9.8
 * - fewer requests to Push Monkey API to improve page load speed
 * - allow websites to upgrade the price plan 
 * - show notification for expired plans
 *
 * v 0.9.9.7
 * - CSS bugfixing for some WP Themes
 *
 * v 0.9.9.6
 * - banner improvements: remember users who disabled the banner, improved animations
 * - you can now filter which custom post types send Desktop Push Notifications
 *
 * v 0.9.9.5
 * - more advanced granular filtering. You can now choose which custom post types send Safari Push Notifications
 *
 * v 0.9.9.4
 * - bugfixing
 *
 * v 0.9.9.3
 * - bugfixing
 *
 * v 0.9.9.2
 * - bugfixing
 *
 * v 0.9.9.1
 * - bugfixing
 *
 * v 0.9.9
 * - on-boarding workflow overhaul: now easier than ever. No more waiting. Account Key what? 
 * - layout update
 * - code cleanup
 * - bugfixing
 *
 * v 0.9.8.2
 * - bugfixing
 *
 * v 0.9.8
 * - bugfixing
 *
 * v 0.9.7
 * - bugfixing
 * - UI updates
 * - typos fixed
 *
 * v 0.9.6
 * - bugfixing
 *
 * v 0.9.5
 * - fix HTML tags in preview
 * - fix conflict with TinyMCE Advanced
 *
 * v 0.9.4
 * - fix some tags that used required the PHP setting short_open_tag to be on
 *
 * v 0.9.3
 * - fix double usage of title
 * - fix possible CSS overwrite
 *
 * v 0.9.2
 * - prepare assets and folder structure for WordPress.org SVN
 *
 * v 0.9.1
 * - replace iframes with API calls
 * - reorganize code in classes
 *
 * v 0.9
 * - add confirmation for Custom Push Notification widget
 * - minor code cleanup
 *
 * v 0.8.8
 * - add option to disable push notifications while editing a Post
 * - add push notification preview while editing a Post
 * - moved settings page to top leve
 * - test on WP 4.0
 *
 * v 0.8.7
 * - add dashboard widget for custom notifications
 *
 * v 0.8.6
 * - add menu page for configuring this plugin.
 * - add option to exclude certain post categories 
 *   from sending push notifications
 * - visual tweaks
 *
 * v 0.8.5
 * - move the endpoint URL to a more generic location.
 *
 * v 0.8.4
 * - limit $post->post_type to 'post', to filter out pages.
 * - test on WP 3.9.
 * - add uninstall.php
 */

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'includes/class_push_monkey_core.php' );

/**
* Perform actions when the plugin is deactivated.
*/
function deactivate() {

    flush_rewrite_rules();
}

/**
* Rewrite the URL for the service worker.
*/
function rewrite_service_worker_url() {

  $account_key = get_option( PushMonkey::ACCOUNT_KEY_KEY, NULL );
  if ( $account_key ) {
  
    add_rewrite_rule('^service-worker-'.$account_key.'.php/?', 'wp-content/plugins/push-monkey-desktop-push-notifications/templates/pages/service_worker.php', 'top');
  }
}

/**
* Perform actions when the plugin is updated
*/
function plugin_updated() {

  $current_plugin_path_name = plugin_basename( __FILE__ );
  if ( $options['action'] == 'update' && $options['type'] == 'plugin' ){

    foreach( $options['packages'] as $each_plugin ){

      if ( $each_plugin == $current_plugin_path_name ){

        rewrite_service_worker_url();
        flush_rewrite_rules();
      }
    }
  }
}

/**
 * Main function that creates and 
 * runs Push Monkey.
 */
function run_push_monkey() {

  register_deactivation_hook( __FILE__, 'deactivate' );
  add_action( 'upgrader_process_complete', 'plugin_updated', 10 );  

	$push_monkey = new PushMonkey();
	$push_monkey->run();
}

run_push_monkey();