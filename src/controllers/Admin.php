<?php

namespace Resform\Controller;


class Admin {

    function __construct($view, $db, $event, $transport, $room_type, $room, $person, $user, $audit_log) {
        $this->view   = $view;
        $this->db     = $db;

        $this->event     = $event;
        $this->transport = $transport;
        $this->room_type = $room_type;
        $this->room      = $room;
        $this->person    = $person;
        $this->user      = $user;
        $this->audit_log = $audit_log;

        $this->assetsUrl = str_replace('controllers/', '', plugin_dir_url(__FILE__) . 'assets/');

        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            setcookie('resform_back_link', $_SERVER["HTTP_REFERER"]);
        }
        $this->back_link = $_COOKIE["resform_back_link"];

        add_action( 'admin_menu', array($this, 'register_menu_page'));
        add_action( 'init', array($this, 'set_user'));

        add_action('admin_enqueue_scripts', array($this, "enqueue_script"));

        add_action( 'wp_ajax_resform_person_inline_update', array($this, "person_inline_update") );
        add_action( 'wp_ajax_resform_room_inline_update', array($this, "room_inline_update") );

        add_action( 'wp_ajax_resform_person_get', array($this, "person_get") );
        add_action( 'wp_ajax_resform_person_get_family', array($this, "person_get_family") );
        add_action( 'wp_ajax_resform_person_search', array($this, "person_search") );

        add_action( 'wp_ajax_resform_person_export', array($this, "person_export") );
    }

    function person_export() {

        $event_id = $_GET["event_id"];
        $results = $this->person->export($event_id);
        die();
    }

    function person_search() {

        $results = $this->person->like($_GET["search"]);

        echo json_encode($results);

        die();
    }

    function person_get_family() {

        $results = $this->person->get_family($_GET["family_person_id"]);

        echo json_encode($results);

        die();
    }

    function person_get() {

        $results = $this->person->find($_GET["person_id"]);

        echo json_encode($results);

        die();
    }

    function person_inline_update() {
        $data = array();
        $data[$_POST["name"]] = $_POST["value"];
        $data["person_id"]    = $_POST["pk"];

        var_dump($this->person->update($data, array($_POST["name"] => 1)));
        die();
    }

    function room_inline_update() {
        $data = array();
        $data[$_POST["name"]] = $_POST["value"];
        $data["room_id"]      = $_POST["pk"];

        $filtered = $this->room->input_filter($data);
        $errors   = $this->room->validate($filtered);

        if (count($errors) === 0) {
            $to_save = $this->room->output_filter($filtered);
            var_dump($this->room->update($to_save));
        }
        var_dump($filtered);
        die();
    }

    function set_user() {
        $this->user->setUser();
    }

    function register_menu_page() {
        add_menu_page(
            'Wydarzenia',
            'Formularz rezerwacji',
            'resform_read',
            'resform_event_list',
            array($this, 'event_list'),
            'dashicons-media-text',
            30
        );

        add_submenu_page(
            'resform_event_list',
            'Page Title',
            'Logi',
            'resform_read',
            'resform_audit_log',
            array($this, 'audit_log')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Dodaj wydarzenie',
            'resform_read',
            'resform_event_add',
            array($this, 'event_add')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Edytuj wydarzenie',
            'resform_write',
            'resform_event_edit',
            array($this, 'event_edit')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Dodaj sposób dojazdu',
            'resform_read',
            'resform_transport_list',
            array($this, 'transport_list')
        );
        add_submenu_page(
            null,
            'Page Title',
            'Dodaj wydarzenie',
            'resform_read',
            'resform_transport_add',
            array($this, 'transport_add')
        );
        add_submenu_page(
            null,
            'Page Title',
            'Edytuj wydarzenie',
            'resform_write',
            'resform_transport_edit',
            array($this, 'transport_edit')
        );


        add_submenu_page(
            null,
            'Page Title',
            'Lista Typów Pokoi',
            'resform_read',
            'resform_room_type_list',
            array($this, 'room_type_list')
        );
        add_submenu_page(
            null,
            'Page Title',
            'Dodaj wydarzenie',
            'resform_write',
            'resform_room_type_add',
            array($this, 'room_type_add')
        );
        add_submenu_page(
            null,
            'Page Title',
            'Edytuj wydarzenie',
            'resform_write',
            'resform_room_type_edit',
            array($this, 'room_type_edit')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Lista Pokoi',
            'resform_read',
            'resform_room_list',
            array($this, 'room_list')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Dodaj sposób dojazdu',
            'resform_read',
            'resform_person_list',
            array($this, 'person_list')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Dodaj sposób dojazdu',
            'resform_read',
            'resform_person_add',
            array($this, 'person_add')
        );

        add_submenu_page(
            null,
            'Page Title',
            'Dodaj sposób dojazdu',
            'resform_read',
            'resform_person_edit',
            array($this, 'person_edit')
        );
    }

    function enqueue_script() {

        wp_enqueue_style('resform-admin', $this->assetsUrl . 'css/admin.css');

        wp_enqueue_script('jquery-fallback', $this->assetsUrl . 'vendor/jquery/jquery.min.js');
        wp_enqueue_script('resform-jqueryui', $this->assetsUrl . 'vendor/jquery-ui/jquery-ui.min.js', array('jquery-fallback'), true);


        wp_enqueue_script('resform-x-editable', $this->assetsUrl . 'vendor/x-editable/jqueryui-editable/js/jqueryui-editable.min.js', array('resform-jqueryui'), true);
        wp_register_style( 'resform-x-editable', $this->assetsUrl . 'vendor/x-editable/jqueryui-editable/css/jqueryui-editable.css');
        wp_enqueue_style( 'resform-x-editable' );

        wp_enqueue_script('resform-select2', $this->assetsUrl . 'vendor/select2/select2.js', array('resform-jqueryui'), true);
        wp_enqueue_script('resform-select2-pl', $this->assetsUrl . 'vendor/select2/select2_locale_pl.js', array('resform-select2'), true);
        wp_register_style( 'resform-select2', $this->assetsUrl . 'vendor/select2/select2.css');
        wp_enqueue_style( 'resform-select2' );

        wp_enqueue_script('resform-tooltipsy', $this->assetsUrl . 'vendor/tooltipsy/tooltipsy.min.js', array('jquery-fallback'), true);

        wp_enqueue_script('resform-admin', $this->assetsUrl . 'js/admin.js', array('jquery-fallback'), true);

        wp_register_style( 'resform-fontawesome', $this->assetsUrl . 'vendor/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style( 'resform-fontawesome' );
        //
        // wp_enqueue_script('resform-spectrum', $this->assetsUrl . 'vendor/spectrum/spectrum.js', array('jquery-fallback'), true);
        // wp_register_style('resform-spectrum', $this->assetsUrl . 'vendor/spectrum/spectrum.css');
        // wp_enqueue_style( 'resform-spectrum' );
    }

    function event_list() {

        if (isset($_GET['event_id'])) {

            $id = $_GET['event_id'];

            $this->event->delete($id);
        }

        // $limit   = (isset($_GET['limit'])) ? $_GET['limit'] : 10;
        // $page    = (isset($_GET['page_no'])) ? $_GET['page_no'] : 1;
        // $orderby = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'event_id';
        // $sort    = (isset($_GET['sort'])) ? $_GET['sort'] : 'asc';

        $events = $this->event->get();

        echo $this->view->render('admin/event/list.html', array('events' => $events));
    }

    function event_add() {

        $errors = array();

        if ($_POST) {

            $filtered = $this->event->input_filter($_POST);
            $errors   = $this->event->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->event->output_filter($filtered);
                $this->event->save($to_save);
            } else {
            }
        }

        echo $this->view->render('admin/event/form.html',
            array('errors' => $errors)
        );
    }

    function event_edit() {
        $errors = array();
        if ($_POST) {
            $filtered = $this->event->input_filter($_POST);
            $errors   = $this->event->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->event->output_filter($filtered);
                $this->event->update($to_save);
            } else {
                var_dump($errors);
            }
        }

        $id = $_GET['event_id'];

        $event = $this->event->find($id);

        echo $this->view->render('admin/event/form.html', array(
            'event'  => $event,
            'errors' => $errors
        ));
    }

    function transport_list() {

        if (isset($_GET['transport_id'])) {

            $id = $_GET['transport_id'];

            $this->transport->delete($id);

        }

        $event_id = $_GET['event_id'];

        $event = $this->event->find($event_id);

        $transports = $this->transport->get($event['event_id']);

        echo $this->view->render('admin/transport/list.html', array(
            'transports' => $transports,
            'event'      => $event
        ));
    }

    function transport_add() {

        if ($_POST) {

            $filtered = $this->transport->input_filter($_POST);
            $errors   = $this->transport->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->transport->output_filter($filtered);
                $this->transport->save($to_save);
            } else {
                var_dump($errors);
            }
        }

        echo $this->view->render('admin/transport/form.html', array('event_id' => $_GET['event_id']));
    }

    function transport_edit() {

        if ($_POST) {
            $filtered = $this->transport->input_filter($_POST);
            $errors   = $this->transport->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->transport->output_filter($filtered);
                $this->transport->update($to_save);
            } else {
                var_dump($errors);
            }
        }

        $id = $_GET['transport_id'];

        $transport = $this->transport->find($id);

        echo $this->view->render('admin/transport/form.html', array(
            'event_id' => $transport['event_id'],
            'transport' => $transport));
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

            $filtered = $this->room_type->input_filter($_POST);
            $errors   = $this->room_type->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->room_type->output_filter($filtered);
                $this->room_type->save($to_save);
            } else {
                var_dump($errors);
            }
        }

        echo $this->view->render('admin/room_type/form.html', array('event_id' => $_GET['event_id']));
    }

    function room_type_edit() {

        if ($_POST) {
            $filtered = $this->room_type->input_filter($_POST);
            $errors   = $this->room_type->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->room_type->output_filter($filtered);
                $this->room_type->update($to_save);
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

        $event = $this->event->find($event_id);

        $pager = array(
            'limit'   => 10000,
            'current' => 1,
            'sort'    => 'asc',
            'orderby' => 'person_id'
        );

        $persons = $this->person->get($pager, $event['event_id']);

        if (count($_POST) > 0) {
            $persons_to_edit = $this->person->getPersonsToEdit($_POST["person_id"], $persons);

            $this->person->updateRoomId($persons_to_edit);
            $persons = $this->person->get($pager, $event['event_id']);
        }

        $rooms_list = $this->room->get($event['event_id']);
        $rooms = $this->person->getGroupedByRooms($persons, $rooms_list);

        echo $this->view->render('admin/room/list.html', array(
            'rooms' => $rooms,
            'event' => $event
        ));
    }

    function person_list() {

        $event_id = $_GET['event_id'];

        $event = $this->event->find($event_id);

        $view  = (isset($_GET['view'])) ? $_GET['view'] : 'general';

        $pager = $this->getPager($_GET);

        $persons = $this->person->get($pager, $event['event_id']);

        echo $this->view->render("admin/person/list-{$view}.html", array(
            'persons' => $persons,
            'event'   => $event,
            'view'    => $view
        ));
    }

    function person_add() {

        if ($_POST) {
            $filtered = $this->person->input_filter($_POST);
            $errors   = $this->person->validate($filtered);

            if (count($errors) === 0) {
                $to_save = $this->person->output_filter($filtered);
                var_dump($to_save);
                $this->person->register($to_save);
            } else {
                var_dump($errors);
            }
        }

        $event_id = $_GET['event_id'];

        $room_types = $this->room_type->get($event_id);
        $transports = $this->transport->get($event_id);

        echo $this->view->render('admin/person/form.html', array(
            'room_types' => $room_types,
            'transports' => $transports,
            'back_link'  => $this->back_link
        ));
    }

    function person_edit() {

        if ($_POST) {
            $filtered = $this->person->input_filter($_POST);
            //$errors   = $this->person->validate($filtered);
            $errors = array();

            if (count($errors) === 0) {
                $to_save = $this->person->output_filter($filtered);
                var_dump($to_save);
                $this->person->update($to_save);
            } else {
                var_dump($errors);
            }
        }

        $id = $_GET['person_id'];

        $person = $this->person->find($id);

        $room_types = $this->room_type->get($person['event_id']);
        $transports = $this->transport->get($person['event_id']);

        echo $this->view->render('admin/person/form.html', array(
            'person' => $person,
            'room_types' => $room_types,
            'transports' => $transports,
            'back_link'  => $this->back_link
            ));
    }

    function audit_log() {


        $audit_logs = $this->audit_log->get();

        echo $this->view->render('admin/audit_log/list.html', array(
            'audit_logs' => $audit_logs
        ));
    }

    function getPager($get) {

        $defaults = array(
            'limit'   => 20,
            'current' => 1,
            'orderby' => 'person_id',
            'sort'    => 'desc'
        );

        return array_merge($defaults, $get);
    }

}
