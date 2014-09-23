<?php

namespace Resform\Controller;


class Admin {

    function __construct($view, $db, $events) {
        $this->view   = $view;
        $this->db     = $db;
        $this->events = $events;

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
            'Dodaj wydarzenie',
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
}
