<?php

function resform_install() {
    global $wpdb;

    $filename = "db.sql";

    $sql = file_get_contents(plugin_dir_path( __FILE__ ) . $filename);

    $sql = str_replace("DELIMITER $$", '', $sql);

    $sql = str_replace("DELIMITER ;", '', $sql);

    $sql = str_replace("$$", ';', $sql);

    $sql = str_replace("_prefix_", $wpdb->prefix . "resform_", $sql);

    $commands = explode("-- break", trim($sql));

    $results = array_map(function($query) use ($wpdb) {
        return $query . " " . $wpdb->query($query) . "\n";
    }, $commands);

    //$results = mysqli_multi_query($wpdb->dbh, $sql);

    file_put_contents(plugin_dir_path( __FILE__ ) . 'db.log', $results);
}

register_activation_hook( __FILE__, 'resform_install' );
