<?php

use Pimple\Container;

$container = new Container();

$container["view_path"] = plugin_dir_path( __FILE__ ) . 'views';

$container['view'] = function ($c) {

    $loader = new Twig_Loader_Filesystem($c["view_path"]);
    return new Twig_Environment($loader);
};

$container['db'] = function ($c) {
    global $wpdb;

    date_default_timezone_set('Europe/Warsaw');
    $origin_dtz = new DateTimeZone('Europe/Warsaw');
    $remote_dtz = new DateTimeZone('UTC');
    $origin_dt = new DateTime("now");
    $remote_dt = new DateTime("now", $remote_dtz);

    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
    $char = '+';
    if ($offset < 0) {
        $char = '-';
    }
    $interval = DateTime::createFromFormat('U', abs($offset));
    $mysql_interval = $interval->format($char . "H:i");
    $wpdb->query("SET time_zone = '$mysql_interval';");

    return $wpdb;
};


$container['install_controller'] = function($c) {
    return new \Resform\Controller\Install($c['db']);
};

$container['admin_controller'] = function($c) {
    return new \Resform\Controller\Admin(
        $c['view'],
        $c['db'],
        $c['event_model'],
        $c['transport_model'],
        $c['room_type_model'],
        $c['room_model'],
        $c['person_model'],
        $c['user_model'],
        $c['audit_log_model']
    );
};

$container['front_controller'] = function($c) {
    return new \Resform\Controller\Front(
        $c['view'],
        $c['event_model'],
        $c['transport_model'],
        $c['room_type_model'],
        $c['person_model']);
};

$container['validator'] = $container->factory(function($c) {
    return new \Sirius\Validation\Validator();
});

$container['filter'] = $container->factory(function($c) {
    $factory = new Sirius\Filtration\FilterFactory();
    $factory->registerFilter('boolify', '\Resform\Lib\Boolify');

    $filter = new Sirius\Filtration\Filtrator($factory);

    return $filter;
});


// Models:

$container['user_model'] = function($c) {
    return new \Resform\Model\User($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['audit_log_model'] = function($c) {
    return new \Resform\Model\AuditLog($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['event_model'] = function($c) {
    return new \Resform\Model\Event($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['room_type_model'] = function($c) {
    return new \Resform\Model\RoomType($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['transport_model'] = function($c) {
    return new \Resform\Model\Transport($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['person_model'] = function($c) {
    return new \Resform\Model\Person(
        $c['db'],
        $c['filter'],
        $c['filter'],
        $c['validator'],
        array($c['filter'], $c['filter'], $c['filter'], $c['filter']),
        array($c['validator'], $c['validator'], $c['validator'], $c['validator']));
};

$container['room_model'] = function($c) {
    return new \Resform\Model\Room($c['db'], $c['filter'], $c['filter'], $c['validator']);
};
