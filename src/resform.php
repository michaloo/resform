<?php
/**
 * Plugin Name: resform
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Reservation Form
 * Version: 1.8
 * Author: Michał Rączka, Artur Kopacz
 * Author URI: http://URI_Of_The_Plugin_Author
 * License: GPL2
 */

/*  Copyright 2014  Michał Rączka, Artur Kopacz

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
define( 'WP_USE_EXT_MYSQL', false);
ini_set('eaccelerator.enable', '0');
ini_set('eaccelerator.optimizer', '0');

require_once(plugin_dir_path( __FILE__ ) . 'vendor/autoload.php');

require_once(plugin_dir_path( __FILE__ ) . 'map.php');

require_once(plugin_dir_path( __FILE__ ) . 'bootstrap.php');


$AdminController   = $container['admin_controller'];
$InstallController = $container['install_controller'];
$FrontController   = $container['front_controller'];

register_activation_hook(__FILE__, array($InstallController, 'install'));
register_deactivation_hook( __FILE__, array($InstallController, 'uninstall') );
