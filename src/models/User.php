<?php

namespace Resform\Model;


class User extends \Resform\Lib\Model {

    function setUser() {
        $current_user = \wp_get_current_user();
        $this->db->query("SET @user = '{$current_user->user_login}';");
    }

}
