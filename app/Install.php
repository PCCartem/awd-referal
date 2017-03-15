<?php

namespace App;

class Install {

    public function __construct()
    {
        wp_insert_post( [
            'post_title' => 'AWD-Profile',
            'post_content' => '[awd-referal]',
            'post_type' => 'page',
            'post_status' => 'publish'
            ]);

        $this->installTable();
        $this->addRoles();
    }

    public function installTable()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $table_name = $wpdb->prefix . "awd_referal_lead";
        if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

            $sql = "CREATE TABLE " . $table_name . " (
	              id bigint(11) NOT NULL AUTO_INCREMENT,
	              time bigint(11) DEFAULT '0' NOT NULL,
	              name tinytext NOT NULL,
	              number tinytext NOT NULL,
	              city tinytext NOT NULL,
	              status tinytext NOT NULL,
	              ref_id bigint(11) DEFAULT '0' NOT NULL,
	              UNIQUE KEY id (id)
	            );";


            dbDelta($sql);
        }
    }

    protected function addRoles() {
        add_role('cityadmin', 'Админ города', array( 'read' => true, 'level_0' => true ) );
        add_role('partner', 'Партнер', array( 'read' => true, 'level_0' => true ) );
    }
}