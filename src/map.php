<?php


// libs
require_once(plugin_dir_path( __FILE__ ) . 'lib/Controller.php');
require_once(plugin_dir_path( __FILE__ ) . 'lib/Model.php');
require_once(plugin_dir_path( __FILE__ ) . 'lib/Boolify.php');
require_once(plugin_dir_path( __FILE__ ) . 'lib/Filters.php');


// controllers
require_once(plugin_dir_path( __FILE__ ) . 'controllers/Install.php');
require_once(plugin_dir_path( __FILE__ ) . 'controllers/Admin.php');
require_once(plugin_dir_path( __FILE__ ) . 'controllers/Front.php');


// models
require_once(plugin_dir_path( __FILE__ ) . 'models/Event.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/Transport.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/Person.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/Room.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/RoomType.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/User.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/Mail.php');
require_once(plugin_dir_path( __FILE__ ) . 'models/AuditLog.php');
