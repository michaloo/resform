<?php

namespace Resform\Controller;


class Front {

    var $validators = array(
        array(),
        array(
            'sex',
            // 'first_name',
            // 'last_name',
            // 'birth_date',
            // 'email',
            // 'phone',
            // 'city'
        ),
        array(
            'room_type',
            'transport',
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
            // 'first_name'
            // 'last_name'
            // 'birth_date'
            // 'email'
            // 'phone'
            // 'city'
            'family_first_name',
            'family_last_name',
            'family_birth_date'
        ),
        array(
            'room_type',
            'transport',
            'comments'
        ),
        array(
            'accept_regulation',
            'accept_information'
        )
    );


    function __construct($view, $event, $front) {
        $this->view = $view;

        $this->event = $event;
        $this->front = $front;

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

        $event = $this->event->getActive();
        //var_dump($event);

        $step = (isset($_POST['step'])) ? (int) $_POST['step'] : 0;

        //var_dump($_SESSION);
        $_SESSION['test'] = true;
        var_dump($_POST);

        switch ($step) {
            default:
            case 0:
                $template = 'page1-general-info.html';
                break;

            case 1:
                $template = 'page2-personal-info.html';
                break;

            case 2:
                $template   = 'page3-details.html';
                break;

            case 3:
                $template = 'page4-regulations.html';
                break;
        }


        if (count($_POST) > 1) {
            $validators = $this->validators[$step];
            $filters    = $this->filters[$step];

            $filtered = array();
            $errors  = array();
            foreach ($filters as $key) {
                $filtered[$key] = call_user_func(array($this->front, 'filter_' . $key), $_POST[$key]);
            }

            foreach ($validators as $key) {
                $error = call_user_func(array($this->front, 'validate_' . $key), $filtered[$key]);

                if ($error) {
                    $errors[$key] = $error;
                }

            }

            var_dump($errors);
        }

        echo $this->view->render(
            'front/form/' . $template,
            array(
                'action_url' => get_page_link()
            ));
    }



}
