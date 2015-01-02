<?php

namespace Resform\Controller;


class Front {

    function __construct($view, $event, $transport, $room_type, $person, $Mail) {
        $this->view = $view;

        $this->event     = $event;
        $this->transport = $transport;
        $this->room_type = $room_type;
        $this->person    = $person;
        $this->Mail      = $Mail;

        $this->assetsUrl = str_replace('controllers/', '', plugin_dir_url(__FILE__) . 'assets/');

        add_shortcode( 'resform', array($this, 'show_form') );

        add_action('init', array($this, 'register_session'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_style'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_script'));
    }

    function register_session(){
        if( !session_id() ) { //checking if session already exists
            session_start();
        }
    }

    function enqueue_style() {
        wp_enqueue_style('resform-main', $this->assetsUrl . 'css/main.css');
    }

    function enqueue_script() {
        // theme we are working on uses CDN jQuery which makes offline development testing impossible
        wp_enqueue_script('jquery-fallback', includes_url() . '/js/jquery/jquery.js');
        wp_enqueue_script('resform-main', $this->assetsUrl . 'js/main.js', array('jquery-fallback'), true);
    }

    function show_form($atts) {

        $events = $this->event->getActive();
        $room_types = array();
        $transports = array();

        if (count($events) === 0) {
            $template = 'none.html';
        } else {

            $event = array_pop($events);

            if ($event['free_space_count'] == '0') {
                $template = 'closed.html';
            } else {

                $step = (isset($_POST['step'])) ? (int) $_POST['step'] : 0;

                $values = array_merge($_SESSION, $_POST);
                $values["event_id"] = $event["event_id"];

                if ($step > 1) {

                    $filtered = $this->person->input_filter_step($step, $values);
                    $errors   = $this->person->validate_step($step, $filtered);

                    if (count($errors) > 0) {
                        $step--;
                    }

                    $values   = array_merge($values, $filtered);
                    $_SESSION = $values;
                }

                switch ($step) {
                    default:
                    case 0:
                        $template = 'page1-general-info.html';
                        $_SESSION = array();
                        break;

                    case 1:
                        $template = 'page2-personal-info.html';
                        break;

                    case 2:
                        $room_types = $this->room_type->getFree($event['event_id'], $this->person->countFamily($values));
                        $transports = $this->transport->get($event['event_id']);
                        $template = 'page3-details.html';
                        break;

                    case 3:
                        $template = 'page4-regulations.html';
                        break;

                    case 4:
                        $to_register = $this->person->output_filter($values);

                        $register_result = $this->person->register($to_register);
                        $register_errors = $register_result["errors"];

                        $person_id = $register_result["person_id"];

                        if (
                            array_search("Column 'room_id' cannot be null", $register_errors) !== false
                            || array_search("This room is not available", $register_errors) !== false
                        ) {
                            $errors['register'] = "Brak dostępnych pokoi albo wybrany pokój jest za mały";
                        } elseif (count($register_errors)) {
                            $errors['register'] = "Wystąpił błąd zapisu, spróbuj ponownie";
                        } else {
                            $_SESSION = array();
                            $this->sendMail($person_id, $values, $event);
                        }

                        $template = 'done.html';

                        break;
                }
            }
        }

        echo $this->view->render(
            'front/form/' . $template,
            array(
                'event'      => $event,
                'room_types' => $room_types,
                'transports' => $transports,
                'action_url' => get_page_link(),
                'errors'     => $errors,
                'values'     => $values
            ));
    }


    function sendMail($person_id, $values, $event) {
        $person = $this->person->getPriceForId($person_id);

        $message = $this->view->render(
            'events/' . $event['event_id'] . '/success_mail_template',
            $person
        );
        $name = $values['first_name'] . ' ' . $values['last_name'];
        $this->Mail->send($values['email'], $name, $message);
    }
}
