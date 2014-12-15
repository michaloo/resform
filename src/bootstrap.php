<?php

use Pimple\Container;

$container = new Container();

$container["log"] = function($c) {
    $log = new Monolog\Logger('name');

    @mkdir(plugin_dir_path( __FILE__ ) . 'logs');
    $log->pushHandler(new Monolog\Handler\StreamHandler(__DIR__ . '/logs/debug.log', Monolog\Logger::DEBUG));
    $log->pushHandler(new Monolog\Handler\StreamHandler(__DIR__ . '/logs/warning.log', Monolog\Logger::WARNING));

    $log->addInfo('Initialize logger');
    return $log;
};

$container["view_path"] = plugin_dir_path( __FILE__ ) . 'views';

$container['view'] = function ($c) {

    $loader = new Twig_Loader_Filesystem($c["view_path"]);
    $twig = new Twig_Environment($loader);

    $filter = new Twig_SimpleFilter('age', array('\Resform\Lib\Filters', 'age'));
    $twig->addFilter($filter);

    $filter = new Twig_SimpleFilter('ajaxurl', array('\Resform\Lib\Filters', 'ajaxurl'));
    $twig->addFilter($filter);

    $filter = new Twig_SimpleFilter('translate', array('\Resform\Lib\Filters', 'translate'));
    $twig->addFilter($filter);

    $filter = new Twig_SimpleFilter('format_price', array('\Resform\Lib\Filters', 'format_price'));
    $twig->addFilter($filter);

    return $twig;
};

$container['db'] = function ($c) {
    global $wpdb;

    setlocale(LC_ALL, 'pl_PL');

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
        $c['person_model'],
        $c['mail_model']);
};

$container['validator'] = $container->factory(function($c) {
    $ruleFactory = new \Sirius\Validation\RuleFactory;

    // register new validation class
    //$ruleFactory->register('map', '\Resform\Lib\Map');

    return new \Sirius\Validation\Validator($ruleFactory);
});

$container['filter'] = $container->factory(function($c) {
    $factory = new Sirius\Filtration\FilterFactory();
    $factory->registerFilter('boolify', '\Resform\Lib\Boolify');

    $filter = new Sirius\Filtration\Filtrator($factory);

    return $filter;
});

$container['mail'] = $container->factory(function($c) {

    $mail = new PHPMailer;

    return $mail;
});


// Models:

$container['user_model'] = function($c) {
    return new \Resform\Model\User($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['audit_log_model'] = function($c) {
    return new \Resform\Model\AuditLog($c['db'], $c['filter'], $c['filter'], $c['validator']);
};

$container['event_model'] = function($c) {
    return new \Resform\Model\Event($c['db'], $c['filter'], $c['filter'], $c['validator'], $c['view_path']);
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

$container['mail_model'] = function($c) {
    return new \Resform\Model\Mail($c['db'], $c['mail']);
};

$container['log'];
