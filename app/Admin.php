<?php
namespace App;


class Admin {

    public $user = null;
    //TODO Прослойка с базой нужна
    public $db = null;
    public $table = null;
    public $leads = null;


    public function run()
    {
        $this->registerActions();
        $this->registerMenuTask('Админы городов', 'Админы городов', 'awd-referal', 'actionAdminsCity', 0);
    }
    public function registerMenuTask($title, $menu_title, $menu_slug, $function, $position = 0)
    {
        add_menu_page(
            $title,
            $menu_title,
            'administrator',
            $menu_slug,
            array( $this, $function),
            '',
            $position
        );
    }

    public function actionAdminsCity()
    {
        $adminsObjs = get_users( ['role' => 'cityadmin'] );
        $partnersObjs = get_users( ['role' => 'partner'] );
        $admins = [];
        $partners = [];
        foreach ($adminsObjs as $key => $adminObj) {
            $admins[$key]['id'] = $adminObj->ID;
            $admins[$key]['name'] = $adminObj->nickname;
            $admins[$key]['city'] = get_user_meta($adminObj->ID, 'relCity');
        }
        foreach ($partnersObjs as $key => $partnersObj) {
            $partners[$key]['id'] = $partnersObj->ID;
            $partners[$key]['name'] = $partnersObj->nickname;
            $partners[$key]['city'] = get_user_meta($partnersObj->ID, 'relCity');
        }
        echo $this->render('city-admins',
            [
                'admins' => $admins,
                'partners' => $partners
            ]);
    }
    

    public function registerActions()
    {
        //TODO: Переписать используя подставновку по префиксам в цикле, подумать над логикой.
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if(strrpos($method, 'actionAppAdmin') !== false) {
                $action = str_replace('actionAppAdmin', '', $method);
                add_action("admin_post_{$action}", array( $this, $method ));
            }
            if(strrpos($method, 'actionAjaxAppAdmin') !== false) {

                $action = str_replace('actionAjaxAppAdmin', '', $method);
                var_dump($action);
                add_action("wp_ajax_{$action}", array( $this, $method ));
                add_action("wp_ajax_nopriv_{$action}", array( $this, $method ));
            }
        }
    }

    public function render($tmpName, $args = [])
    {
        if(file_exists(AWD_REFERAL_DIR.'view/'.$tmpName.'.php')) {
            ob_start();
            extract($args);
            require AWD_REFERAL_DIR.'view/'.$tmpName.'.php';
            return ob_get_clean();
        } else {
            throw new \Exception('View '.$tmpName.' not found');
        }
    }





}