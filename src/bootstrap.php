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
    return new \Resform\Controller\Admin(
        $c['view'],
        $c['db'],
        $c['event_model'],
        $c['transport_model'],
        $c['room_type_model'],
        $c['room_model']
    );
});

$container['front_controller'] = $container->factory(function($c) {
    return new \Resform\Controller\Front($c['view'], $c['event_model']);
});

$container['validator'] = $container->factory(function($c) {
    return new JsonSchema\Validator();
});

$container['filter'] = $container->factory(function($c) {
    $factory = new Sirius\Filtration\FilterFactory();
    $factory->registerFilter('boolify', '\Resform\Lib\Boolify');

    $filter = new Sirius\Filtration\Filtrator($factory);

    return $filter;
});


// Models:

$container['event_model'] = function($c) {
    return new \Resform\Model\Event($c['db'], $c['filter'], $c['validator']);
};

$container['room_type_model'] = function($c) {
    return new \Resform\Model\RoomType($c['db'], $c['filter'], $c['validator']);
};

$container['transport_model'] = function($c) {
    return new \Resform\Model\Transport($c['db'], $c['filter'], $c['validator']);
};

$container['person_model'] = function($c) {
    return new \Resform\Model\Person();
};

$container['room_model'] = function($c) {
    return new \Resform\Model\Room($c['db'], $c['filter'], $c['validator']);
};
