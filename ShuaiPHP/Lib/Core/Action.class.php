<?php

class Action {

    public $vals = array();
    public $view;

    function Action() {
        $this->view = new View ();
    }

    public function assign($name, $val) {
        $this->view->assign($name, $val);
    }

    public function fetch($tpl = '') {
        return $this->view->fetch($tpl);
    }

    public function display($tpl = '') {
        echo $this->fetch($tpl);
    }

}
