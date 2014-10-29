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

        add_action( 'admin_menu', array($this, 'register_menu_page'));
        add_action( 'init', array($this, 'set_user'));
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
            'resform_person_edit',
            array($this, 'person_edit')
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

        $rooms = $this->room->get($event['event_id']);

        echo $this->view->render('admin/room/list.html', array(
            'rooms' => $rooms,
            'event' => $event
        ));
    }

    function person_list() {

        $event_id = $_GET['event_id'];

        $event = $this->event->find($event_id);

        $limit = (isset($_GET['limit'])) ? $_GET['limit'] : 10;
        $page  = (isset($_GET['page_no'])) ? $_GET['page_no'] : 1;
        $orderby = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'person_id';
        $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'asc';

        $persons = $this->person->get($limit, $page, $orderby, $sort, $event['event_id']);

        echo $this->view->render('admin/person/list.html', array(
            'persons' => $persons,
            'event'   => $event
        ));
    }

    function person_edit() {

        if ($_POST) {
            $filtered = $this->person->input_filter($_POST);
            $errors   = $this->person->validate($filtered);

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
            ));
    }

    function audit_log() {


        $audit_logs = $this->audit_log->get();

        echo $this->view->render('admin/audit_log/list.html', array(
            'audit_logs' => $audit_logs
        ));
    }

}
