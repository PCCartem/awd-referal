<?php
namespace App;

class Uninstall {

    public function __construct()
    {
        $page = get_page_by_title('AWD-Profile');
        wp_delete_post($page->ID);
        $this->uninstallTables();
    }

    public function uninstallTables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "awd_referal_lead";
        $sql = "DROP TABLE IF EXISTS $table_name;";
        $wpdb->query($sql);
    }
}