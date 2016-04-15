<?php

use Pimple\Container;

$container = new Container();

$container["log"] = function($c) {

    $log = new Monolog\Logger('resform');

    $path = plugin_dir_path( __FILE__ ) . 'logs/';

    @mkdir($path);

    $log->pushProcessor(new Monolog\Processor\WebProcessor());
    $log->pushProcessor(new Monolog\Processor\MemoryUsageProcessor());

    $log->pushProcessor(function ($record) {
        $record['extra']['session_id'] = session_id();
        $record['extra']['hostname'] = gethostname();
        return $record;
    });

    if (!defined('WP_DEBUG') || WP_DEBUG !== true) {

        $token = getenv('LOGGLY_TOKEN');

        $log->pushHandler(new Monolog\Handler\LogglyHandler($token . '/tag/monolog', Monolog\Logger::DEBUG));

        Monolog\ErrorHandler::register($log, array(), Psr\Log\LogLevel::NOTICE);

        return $log;
    }

    $log->pushHandler(new Monolog\Handler\BrowserConsoleHandler(Monolog\Logger::DEBUG));

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
    return new \Resform\Controller\Install($c['log'], $c['db']);
};

$container['admin_controller'] = function($c) {
    return new \Resform\Controller\Admin(
        $c['log'],
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
        $c['log'],
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
    $factory->registerFilter('datify', '\Resform\Lib\Datify');

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
        $c['log'],
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
