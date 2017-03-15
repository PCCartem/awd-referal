<?php
namespace App;


class App {

    public $user = null;
    //TODO Прослойка с базой нужна
    public $db = null;
    public $table = null;
    public $leads = null;
    public $paidLeads = null;


    public function run()
    {
        if(is_null($this->db)) {
            global $wpdb;
            $this->db = $wpdb;
        }
        if(is_null($this->table)) {
            $this->table = $this->db->prefix.'awd_referal_lead';
        }
        if(is_null($this->user)) {
            $this->user = new User();
        }
        if(is_null($this->leads) && $this->user->id > 0) {
            $this->leads = $this->getLeads();
        }
        if(is_null($this->paidLeads) && $this->user->id > 0) {
            $this->paidLeads = $this->getPaidLeads();
        }
        $profile = function () {
            if($this->user->id > 0) {
                if($this->user->roles[0] == 'cityadmin' || $this->user->roles[0] == 'administrator') {
                    echo $this->render('referal-tabs-admin', [
                        'user' => $this->user,
                        'leads' => $this->leads,
                        'paidLeads' => $this->paidLeads,
                        'role' => $this->user->roles[0]
                    ]);
                } else {
                        echo $this->render('referal-tabs', [
                            'user' => $this->user,
                            'leads' => $this->leads,
                            'paidLeads' => $this->paidLeads,
                            'role' => $this->user->roles[0]
                        ]);
                }

            } else {
                echo $this->render('login', [
                ]);
            }

        };
        Shortcode::addShortcode('awd-referal', $profile);
        Shortcode::registerShortcodes();
        $this->registerActions();

    }

    public function registerActions()
    {

        //TODO: Переписать используя подставновку по префиксам в цикле, подумать над логикой.
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if(strrpos($method, 'actionApp') !== false) {
                $action = str_replace('actionApp', '', $method);
                add_action("admin_post_nopriv_{$action}", array( $this, $method ));
                add_action("admin_post_{$action}", array( $this, $method ));
            }
            if(strrpos($method, 'actionAppAdmin') !== false) {
                $action = str_replace('actionAppAdmin', '', $method);
                add_action("admin_post_{$action}", array( $this, $method ));
            }
            if(strrpos($method, 'actionAjaxApp') !== false) {
                $action = str_replace('actionAjaxApp', '', $method);
                add_action("wp_ajax_{$action}", array( $this, $method ));
                add_action("wp_ajax_nopriv_{$action}", array( $this, $method ));
            }
            if(strrpos($method, 'actionAjaxAppAdmin') !== false) {
                $action = str_replace('actionAjaxAppAdmin', '', $method);
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

    public function updateUser(array $options)
    {
        if($this->user->ID) {
            $userdata['ID'] = $this->user->ID;
            foreach ($options as $key => $option) {
                if(!empty($this->user->$key)) {
                    $userdata[$key] = $option;
                }
            }
            $result = wp_update_user($userdata);
            $result = is_wp_error( $result ) ? true : false;
        } else {
            $result = false;
        }
        return $result;
    }

    public function changeBalanceUser($id, $type, $summ)
    {
        $balance =  get_user_meta($id, 'balance')[0];
        if($type === 'plus') {
            update_user_meta($id, 'balance',  $balance + $summ);
        } elseif($type === 'minus') {
            $res =  $balance - $summ;
            if($res < 0) {$res = 0;}
            update_user_meta($id, 'balance',  $res);
        }

    }

    public function changeStatusLead($id, $status)
    {
        if(!empty($this->user->id)){
            $lead = $this->db->get_row("SELECT * FROM $this->table WHERE id=".$id." AND ref_id=".$this->user->id);
            if($lead->status !== 4) {
                $this->db->update( $this->table, array( 'status' => $status ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
            }
        }
    }

    public function addLead($data)
    {
       $this->db->insert(
            $this->table,
        array(
            'name' => $data['name'],
            'number' => $data['number'],
            'city' => $data['city'],
            'ref_id' => !empty($data['ref']) ? $data['ref'] : null ,
            'status' => 1 ,
            'time' => time() ),
        array( '%s', '%s', '%s', '%d' )
    );
    }

    public function updateLead()
    {

    }

    public function getLeads()
    {
        if($this->user->roles[0] == 'cityadmin' || $this->user->roles[0] == 'administrator') {
            $city = $this->user->relCity;
            if(!empty($city)) {
                return $this->db->get_results("SELECT * FROM $this->table WHERE city=".$city, ARRAY_A);
            } else {
                return $this->db->get_results("SELECT * FROM $this->table", ARRAY_A);
            }
        } else {
            return $this->db->get_results("SELECT * FROM $this->table WHERE ref_id=".$this->user->id, ARRAY_A);
        }
    }

    public function getPaidLeads()
    {
        if($this->user->roles[0] == 'cityadmin' || $this->user->roles[0] == 'administrator') {
            $city = $this->user->relCity;
            if(!empty($city)) {
                return $this->db->get_results("SELECT * FROM $this->table WHERE city=".$city." AND status = 4", ARRAY_A);
            } else {
                return $this->db->get_results("SELECT * FROM $this->table WHERE status = 4", ARRAY_A);
            }
        } else {
            return $this->db->get_results("SELECT * FROM $this->table WHERE ref_id=".$this->user->id." AND status = 4", ARRAY_A);
        }
    }

    public function getLead($id)
    {
        return $this->db->get_results("SELECT * FROM $this->table WHERE id=".$id, ARRAY_A);
    }

    public function actionAppAwdUpdateUser()
    {
        $this->user->name = esc_attr($_POST['username']);
        $this->user->email = esc_attr($_POST['email']);
        $this->user->number = esc_attr($_POST['phone']);
        $this->user->typeAccount = esc_attr($_POST['type-account']);
        $this->user->numberAccount = esc_attr($_POST['number-account']);
        $this->user->save();
        wp_redirect('/awd-profile/');
    }

    public function actionAppAwdLogin()
    {

        $login = esc_attr($_POST['log']);
        $password = esc_attr($_POST['pwd']);
        $remember = !empty($_POST['rememberme']) ? esc_attr($_POST['rememberme']) : false;
        $this->user->authenticate($login, $password, $remember);

        wp_redirect('/awd-profile/');
    }

    public function actionAppAwdRegister()
    {

        $login = esc_attr($_POST['name']);
        $email = esc_attr($_POST['email']);
        $password = esc_attr($_POST['pass']);
        $this->user->register($login, $email, $password);
        $txt = "Доброго времени.
Ваш логин: $login. Пароль от страницы партнера: $password";
        wp_mail( $email, 'Данные пользователя.', $txt );
        wp_redirect('/awd-profile/');
    }

    public function actionAjaxAppAwdUpdateStatus()
    {
        $lead = esc_attr($_POST['lead-id']);
        $status = esc_attr($_POST['status']);

        $this->changeStatusLead($lead, $status);
        die();
    }

    public function actionAjaxAppAwdAddLead()
    {
        $name = esc_attr($_POST['name']);
        $number = esc_attr($_POST['number']);
        $city = esc_attr($_POST['city']);
        $refId = esc_attr($_POST['ref']);
        $this->addLead([
            'name' => $name,
            'city' => $city,
            'number' => $number,
            'ref' => $refId
        ]);

        die();
    }

    public function actionAjaxAppAwdCloseLead()
    {
        $leadId = esc_attr($_POST['lead-id']);
        $status = 4;
        if(!empty($leadId)) {


        $lead = $this->getLead($_POST['lead-id']);
        if(!empty($lead[0]['ref_id'])) {
            if($lead[0]['status'] !== 4) {
                $this->changeBalanceUser($lead[0]['ref_id'], 'plus', 5000);
                $this->changeStatusLead($leadId, $status);
            }
        }
        }
        die();

    }

    public function actionAjaxAppAdminAwdAddCityAdmin()
    {
        $id= esc_attr($_POST['id']);
        $city = esc_attr($_POST['city']);

        $this->addCityAdmin($id, $city);
        die(1);
    }

    public function actionAjaxAppAdminAwdChangeCityAdmin()
    {
        $id= esc_attr($_POST['id']);
        $city = esc_attr($_POST['city']);
        $this->user->relCity = $city;
        update_user_meta($id, 'relCity', $city);
        die(1);
    }

    public function actionAjaxAppAdminAwdDelCityAdmin()
    {
        $id= esc_attr($_POST['id']);
        $this->user->removeRole($id, 'cityadmin' );
        $this->user->addRole($id, 'partner' );
        die(1);
    }

    public function addCityAdmin($id, $city)
    {
        $this->user->addRole($id, 'cityadmin' );
        $this->user->removeRole($id, 'partner' );
        $this->user->relCity = $city;
        $this->user->save();
    }

    public function actionAjaxAppAdminAwdGetMePaid($id)
    {
        $user = get_userdata($id);
        $balance = 0;
        $typeAccount = 'ЯндексКошелек';
        $numberAccount = '123';
        $txt = "Партнер $user->nickname id: $user->ID запрашивает сумму на кошелек типа - $typeAccount c номером $numberAccount, в размере $balance рублей";
        $result = wp_mail( $user->email, 'Запрос на вывод средств.', $txt );
        if($result) {
            echo 1;
        } else {
            echo 0;
        }
        die();
    }


}