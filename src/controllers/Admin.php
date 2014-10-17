<?php

namespace Resform\Controller;


class Admin {

    function __construct($view, $db, $event, $transport, $room_type, $room) {
        $this->view   = $view;
        $this->db     = $db;


        $this->event     = $event;
        $this->transport = $transport;
        $this->room_type = $room_type;
        $this->room      = $room;

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

        add_submenu_page(
            null,
            'Page Title',
            'Lista Pokoi',
            'manage_options',
            'resform_room_list',
            array($this, 'room_list')
        );
    }


    function event_list() {

        if (isset($_GET['event_id'])) {

            $id = $_GET['event_id'];

            $this->event->delete($id);
        }

        $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 10;
        $page  = (isset($_GET['page_no'])) ? $_GET['page_no'] : 1;
        $orderby = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'event_id';
        $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'asc';

        $events = $this->event->get($limit, $page, $orderby, $sort);

        echo $this->view->render('admin/event/list.html', array('events' => $events));
    }

    function event_add() {

        if ($_POST) {

            $filtered = $this->event->filter($_POST);
            $errors   = $this->event->validate($filtered);

            if (count($errors) === 0) {
                $this->event->save($filtered);
            } else {
                var_dump($errors);
            }
        }

        echo $this->view->render('admin/event/form.html');
    }

    function event_edit() {

        if ($_POST) {
            $filtered = $this->event->filter($_POST);
            $errors   = $this->event->validate($filtered);

            if (count($errors) === 0) {
                $this->event->update($filtered);
            } else {
                var_dump($errors);
            }
        }

        $id = $_GET['event_id'];

        $event = $this->event->find($id);

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

        if (isset($_GET['room_type_id'])) {

            $id = $_GET['room_type_id'];

            $this->room_type->delete($id);

        }

        $event_id = $_GET['event_id'];

        $event = $this->event->find($event_id);

        $room_types = $this->room_type->get($event['event_id']);

        echo $this->view->render('admin/room_type/list.html', array(
            'room_types' => $room_types,
            'event'      => $event
        ));
    }

    function room_type_add() {

        if ($_POST) {

            $filtered = $this->room_type->filter($_POST);
            $errors   = $this->room_type->validate($filtered);

            if (count($errors) === 0) {
                $this->room_type->save($filtered);
            } else {
                var_dump($errors);
            }
        }

        echo $this->view->render('admin/room_type/form.html', array('event_id' => $_GET['event_id']));
    }

    function room_type_edit() {

        if ($_POST) {
            $filtered = $this->room_type->filter($_POST);
            $errors   = $this->room_type->validate($filtered);

            if (count($errors) === 0) {
                $this->room_type->update($filtered);
            } else {
                var_dump($errors);
            }
        }

        $id = $_GET['room_type_id'];

        $room_type = $this->room_type->find($id);

        echo $this->view->render('admin/room_type/form.html', array(
            'event_id' => $room_type->event_id,
            'room_type' => $room_type));
    }

    function room_list() {

        if (isset($_GET['room_id'])) {

            $id = $_GET['room_id'];

            $this->room->delete($id);

        }

        $event_id = $_GET['event_id'];

        $event = $this->events->find($event_id);

        $rooms = $this->room->get($event->event_id);
var_dump($rooms);
        echo $this->view->render('admin/room/list.html', array(
            'rooms' => $rooms,
            'event' => $event
        ));
    }
}
