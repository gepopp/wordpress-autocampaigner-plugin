<?php
/**
 * Plugin Name:       Campaign Monitor Autocampaigner
 * Plugin URI:        https://poppgerhard.at/plugins/autocampaigner/
 * Description:       Creates Campaigns from your content
 * Version:           0.0.5
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Popp Gerhard
 * Author URI:        https://poppgerhard.at
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       autocampaigner
 * Domain Path:       /languages
 */
namespace Autocampaigner;

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

$plugindata = get_plugin_data( __FILE__ );

define( 'AUTOCAMPAIGNER_VERSION', $plugindata['Version'] );
define( 'AUTOCAMPAIGNER_DIR', __DIR__ );
define( 'AUTOCAMPAIGNER_FILE', __FILE__ );
define( 'AUTOCAMPAIGNER_URL', plugin_dir_url( __FILE__ ) );

$loader = require_once( AUTOCAMPAIGNER_DIR . '/vendor/autoload.php' );
$loader->addPsr4( 'Autocampaigner\\', __DIR__ . '/classes' );

$instance = Boot::getInstance();
$instance->boot();