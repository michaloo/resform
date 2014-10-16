<?php

namespace Resform\Controller;


class Front {

    function __construct($view, $event) {
        $this->view = $view;

        $this->event = $event;

        add_shortcode( 'resform', array($this, 'foobar_func') );
    }

    function foobar_func( $atts ) {

        $event = $this->event->getActive();
        var_dump($event);


        echo $this->view->render('front/form/page1-general-info.html', array('event' => $event));
    }



}
