<?php

namespace Resform\Controller;


class Front {

    var $validators = array(
        array(),
        array(
            'sex',
            'first_name',
            'last_name',
            'birth_date',
            'email',
            // 'phone',
            // 'city',
            'family_first_name',
            'family_last_name',
            'family_birth_date'
        ),
        array(
            'room_type_id',
            'transport_id',
            'comments'
        ),
        array(
            'accept_regulation',
            'accept_information'
        )
    );

    var $filters = array(
        array(),
        array(
            'sex',
            'first_name',
            'last_name',
            'birth_date',
            'email',
            // 'phone'
            // 'city'
            'family_first_name',
            'family_last_name',
            'family_birth_date'
        ),
        array(
            'room_type_id',
            'transport_id',
            'comments'
        ),
        array(
            'accept_regulation',
            'accept_information'
        )
    );


    function __construct($view, $event, $person) {
        $this->view = $view;

        $this->event  = $event;
        $this->person = $person;

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
        if (count($events) === 0) {
            $template = 'none.html';
        } else {

            $event = array_pop($events);

            if ($event['free_space_count'] == '0') {
                $template = 'closed.html';
            } else {

                $step = (isset($_POST['step'])) ? (int) $_POST['step'] : 0;

                $values = array_merge($_SESSION, $_POST);

                if ($step > 1) {
                    $validators = $this->validators[$step - 1];
                    $filters    = $this->filters[$step - 1];

                    $filtered = array();
                    $errors  = array();
                    foreach ($filters as $key) {
                        $filtered[$key] = call_user_func(array($this->person, 'filter_' . $key), $values[$key]);
                    }

                    foreach ($validators as $key) {
                        $error = call_user_func(array($this->person, 'validate_' . $key), $filtered[$key]);

                        if ($error) {
                            $errors[$key] = $error;
                        }

                    }

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
                        break;

                    case 1:
                        $template = 'page2-personal-info.html';
                        break;

                    case 2:
                        $template = 'page3-details.html';
                        break;

                    case 3:
                        $template = 'page4-regulations.html';
                        break;

                    case 4:
                        //$_SESSION = array();
                        $register_errors = $this->person->register($values);

                        if (array_search("Column 'room_id' cannot be null", $register_errors) !== false) {
                            $errors['register'] = "Brak dostępnych pokoi albo wybrany pokój jest za mały";
                        } elseif (count($register_errors)) {
                            $errors['register'] = "Wystąpił błąd zapisu, spróbuj ponownie";
                        } else {
                            $_SESSION = array();
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
                'action_url' => get_page_link(),
                'errors'     => $errors,
                'values'     => $values
            ));
    }

}
