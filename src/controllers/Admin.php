<?php

namespace Resform\Controller;


class Admin {

    function __construct($view, $db, $events, $transports, $room_types) {
        $this->view   = $view;
        $this->db     = $db;
        $this->events = $events;

        $this->transports = $transports;
        $this->room_types = $room_types;

        add_action( 'admin_menu', array($this, 'register_menu_page'));
    }

    function register_menu_page() {
        add_menu_page(
            'Wydarzenia',
            'Formularz rezerwacji',
            'manage_options',
            'resform_event_list',
            array($this, 'event_list'),
            'dashicons-media-text',
            30
        );


        add_submenu_page(
            null,
            'Page Title',
            'Dodaj wydarzenie',
            'manage_options',
            'resform_event_add',
            array($this, 'event_add')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Edytuj wydarzenie',
            'manage_options',
            'resform_event_edit',
            array($this, 'event_edit')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Dodaj sposób dojazdu',
            'manage_options',
            'resform_transports_add',
            array($this, 'transport_add')
        );


        add_submenu_page(
            null,
            'Page Title',
            'Lista Typów Pokoi',
            'manage_options',
            'resform_room_type_list',
            array($this, 'room_type_list')
        );
        add_submenu_page(
            null,
            'Page Title',
            'Dodaj wydarzenie',
            'manage_options',
            'resform_room_type_add',
            array($this, 'room_type_add')
        );
        add_submenu_page(
            null,
            'Page Title',
            'Edytuj wydarzenie',
            'manage_options',
            'resform_room_type_edit',
            array($this, 'room_type_edit')
        );
    }


    function event_list() {

        if ($_GET && isset($_GET['delete'])) {

            $id = $_GET['delete'];

            $event = $this->events->find($id);

            $event->delete();

        }

        $events = $this->events->get();

        echo $this->view->render('admin/event/list.html', array('events' => $events));
    }

    function event_add() {

        if ($_POST) {

            $event = $this->events->create($_POST);
            $event->save();
        }

        echo $this->view->render('admin/event/form.html');
    }

    function event_edit() {

        $id = $_GET['id'];

        $event = $this->events->find($id);


        if ($_POST) {
            $event->fromPost($_POST);
            $event->event_id = $id;
            $event->update();
        }

        echo $this->view->render('admin/event/form.html', array('event' => $event));
    }

    function transport_add() {
        global $wpdb;

        if ($_POST) {

            $name = mysqli_real_escape_string($wpdb->dbh, $_POST['name']);

            $query = "INSERT INTO {$wpdb->prefix}resform_events (name) VALUES ('$name')";
            var_dump($query);
            $wpdb->query($query);
        }

        require_once(plugin_dir_path( __FILE__ ) . '../views/admin/transport/form.php');
    }

    function room_type_list() {

        if ($_GET && isset($_GET['delete'])) {

            $id = $_GET['delete'];

            $event = $this->events->find($id);

            $event->delete();

        }

        $event_id = $_GET['event_id'];

        $event = $this->events->find($event_id);

        $room_types = $this->room_types->get($event->event_id);

        echo $this->view->render('admin/room_type/list.html', array(
            'room_types' => $room_types,
            'event'      => $event
        ));
    }

    function room_type_add() {

        if ($_POST) {

            $room_type = $this->room_types->create($_POST);
            $room_type->save();
        }

        echo $this->view->render('admin/room_type/form.html', array('event_id' => $_GET['event_id']));
    }

    function room_type_edit() {

        $id = $_GET['room_type_id'];

        $room_type = $this->room_types->find($id);


        if ($_POST) {
            $room_type->fromPost($_POST);
            $room_type->room_type_id = $id;
            $room_type->update();
        }

        echo $this->view->render('admin/room_type/form.html', array(
            'event_id' => $room_type->event_id,
            'room_type' => $room_type));
    }
}
