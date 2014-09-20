<?php
/**
 * Plugin Name: resform
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Reservation Form
 * Version: 0.1
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

require_once(plugin_dir_path( __FILE__ ) . 'vendor/autoload.php');

$loader = new Twig_Loader_Filesystem(plugin_dir_path( __FILE__ ) . 'views');
$twig = new Twig_Environment($loader);

require_once(plugin_dir_path( __FILE__ ) . 'controllers/install.php');

require_once(plugin_dir_path( __FILE__ ) . 'controllers/admin.php');
