<?php

use Pimple\Container;

$container = new Container();

$container["view_path"] = plugin_dir_path( __FILE__ ) . 'views';

$container['view'] = $container->factory(function ($c) {
    $loader = new Twig_Loader_Filesystem($c["view_path"]);
    return new Twig_Environment($loader);
});

$container['db'] = $container->factory(function ($c) {
    global $wpdb;
    return $wpdb;
});


$container['install_controller'] = $container->factory(function($c) {
    return new \Resform\Controller\Install($c['db']);
});

$container['admin_controller'] = $container->factory(function($c) {
    return new \Resform\Controller\Admin($c['view'], $c['db'], $c['events_collection']);
});

$container['front_controller'] = $container->factory(function($c) {
    return new \Resform\Controller\Front($c['view'], $c['db']);
});

$container['event_model'] = '\Resform\Model\Event';

$container['events_collection'] = function($c) {
    return new \Resform\Model\EventsCollection($c['db'], $c['event_model']);
};

$container['person_model'] = function($c) {
    return new \Resform\Model\Person();
};

$container['room_model'] = function($c) {
    return new \Resform\Model\Room();
};

$container['room_type_model'] = function($c) {
    return new \Resform\Model\RoomType();
};
