<?php

function resform_events_list() {
    global $wpdb, $twig;

    $query = "SELECT * FROM {$wpdb->prefix}resform_events LIMIT 10";

    $events = $wpdb->get_results($query, OBJECT);

    //require_once(plugin_dir_path( __FILE__ ) . '../views/admin/event/list.php');
    echo $twig->render('admin/event/list.html', array('events' => $events));
}

function resform_events_add() {
    global $wpdb;

    if ($_POST) {

        $keys = array('name', 'start_time', 'end_time');

        $values = array_map(function ($key) use ($wpdb) {
            return mysqli_real_escape_string($wpdb->dbh, $_POST[$key]);
        }, $keys);

        $fields = (object) array_combine($keys, $values);

        //$name = mysqli_real_escape_string($wpdb->dbh, $_POST['name']);

        $query = "INSERT INTO {$wpdb->prefix}resform_events (name) VALUES ('{$fields->name}')";
        var_dump($query);
        $wpdb->query($query);
    }

    require_once(plugin_dir_path( __FILE__ ) . '../views/admin/event/form.php');
}

function resform_transports_add() {
    global $wpdb;

    if ($_POST) {

        $name = mysqli_real_escape_string($wpdb->dbh, $_POST['name']);

        $query = "INSERT INTO {$wpdb->prefix}resform_events (name) VALUES ('$name')";
        var_dump($query);
        $wpdb->query($query);
    }

    require_once(plugin_dir_path( __FILE__ ) . '../views/admin/transport/form.php');
}

add_action( 'admin_menu', 'resform_register_menu_page' );

function resform_register_menu_page(){
    add_menu_page( 'Wydarzenia', 'Formularz rezerwacji', 'manage_options', 'resform', 'resform_events_list', 'dashicons-media-text', 30 );


    add_submenu_page(
        'resform',
        'Page Title',
        'Dodaj wydarzenie',
        'manage_options',
        'resform_add',
        'resform_events_add'
    );

    add_submenu_page(
        'resform',
        'Page Title',
        'Dodaj sposób dojazdu',
        'manage_options',
        'resform_transports_add',
        'resform_transports_add'
    );
}
