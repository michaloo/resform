<?php

namespace Resform\Controller;


class Install {

    function __construct($log, $db) {
        $this->log = $log;
        $this->db  = $db;
    }

    function uninstall() {
        $this->log->info('uninstall');
        remove_role( 'resform_admin' );
    }

    function install() {

        $this->log->info('install');

        add_role(
            'resform_admin',
            __( 'Administrator Zapisów' ),
            array(
                'read'           => true,
                'manage_options' => true,
                'resform_read'   => true,
                'resform_write'  => true,
            )
        );

        $directory = plugin_dir_path( __FILE__ ) . '../db/';

        $files = array_slice(scandir($directory), 2);

        $sql = implode("\n", array_map(function($f) use ($directory) {
            return file_get_contents($directory . $f);
        }, $files));

        // remove all comments
        $comment_patterns = array(
            '/\/\*.*(\n)*.*(\*\/)?/', //C comments
            '/\s*--.*\n/', //inline comments start with --
            '/\s*#.*\n/', //inline comments start with #
        );
        $sql = preg_replace($comment_patterns, "\n", $sql);

        // remove all delimiters changes, replace prefix and add '-- break' comments
        // before every 'CREATE' query
        $sql = str_replace(array(
            "DELIMITER $$",
            "DELIMITER ;",
            "$$",
            "_prefix_",
            "CREATE",
            "DROP",
            "ALTER"
        ), array(
            "",
            "",
            ";",
            $this->db->prefix . "resform_",
            "-- break \nCREATE",
            "-- break \nDROP",
            "-- break \nALTER"
        ), $sql);

        // divide commands on '-- break' comment, trim whitespaces and filter empty
        // array elements
        $commands = array_filter(array_map("trim", explode("-- break", $sql)));

        // run commands
        $results = array_map(function($query) {
            $count = $this->db->query($query);
            return $query . " " . $this->db->last_error . "\n";
        }, $commands);

        file_put_contents(plugin_dir_path( __FILE__ ) . '../db.log', $results, FILE_APPEND);
    }

}
