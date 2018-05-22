<?php

/*
Plugin Name: 	Integração RD Station
Plugin URI: 	https://wordpress.org/plugins/integracao-rdstation
Description:  Integre seus formulários de contato do WordPress com o RD Station
Version:      3.2.5
Author:       Resultados Digitais
Author URI:   http://resultadosdigitais.com.br
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  integracao-rd-station

Integração RD Station is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Integração RD Station is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Integração RD Station. If not, see https://www.gnu.org/licenses/gpl-2.0.html.

*/

require_once('config.php');

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once('rd_custom_post_type.php');
require_once('metaboxes/add_custom_scripts.php');

// plugin setup
require_once('initializers/contact_form7.php');
require_once('initializers/gravity_forms.php');
require_once('settings/settings_page.php');

// setup available integrations
require_once(SRC_DIR . '/integrations/contact_form7/setup.php');
require_once(SRC_DIR . '/integrations/gravity_forms/setup.php');
require_once(SRC_DIR . '/integrations/woocommerce/setup.php');

// API client
require_once(SRC_DIR . '/client/rdsm_settings_api.php');

// Authorization tokens persistence
require_once(SRC_DIR . '/authorization/rdsm_tokens.php');
require_once(SRC_DIR . '/client/rdsm_legacy_tokens.php');

// Setup hooks
require_once(SRC_DIR . "/hooks/rdsm_uninstall_hooks.php");
require_once(SRC_DIR . "/hooks/rdsm_tracking_code_hooks.php");

$rdsm_uninstall_hook = new RDSMUninstallHooks;
register_deactivation_hook(__FILE__, array($rdsm_uninstall_hook, 'trigger'));

$rdsm_tracking_code_hook = new RDSMTrackingCodeHooks(new RDSMSettingsAPI);
$rdsm_tracking_code_hook->enable();

add_action( 'admin_enqueue_scripts', 'enqueue_rd_admin_style' );
function enqueue_rd_admin_style($hook) {
  $screen = get_current_screen();

  if ($screen->base === 'settings_page_rdstation-settings-page') {
    wp_enqueue_script( 'rd_admin_script', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js' );
  }

  if ( 'post.php' != $hook ) return;
  wp_enqueue_style( 'rd_admin_style', plugin_dir_url( __FILE__ ) . 'assets/styles/admin.css' );
}
