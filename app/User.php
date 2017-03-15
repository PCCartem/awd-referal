<?php
namespace App;

class User {
    public $id;
    public $name;
    public $email;
    public $balance;
    public $number;
    public $typeAccount;
    public $numberAccount;
    public $leads;
    public $paidLeads;
    public $referalLinc;
    public $roles;
    public $relCity;

    public function __construct()
    {
        if(is_user_logged_in()){
            $current = wp_get_current_user();
            $data = get_userdata($current->ID);
            //Иницилизируем переменные для профиля
            $this->id = !empty($data->ID) ? $data->ID : NULL;
            $this->initUserMeta($data);
            $this->name = !empty($data->first_name) ? $data->first_name : "";
            $this->email = !empty($data->user_email) ? $data->user_email : "";
            $this->referalLinc = !empty($data->ID) ? get_site_url()."/?ref=".$data->ID : "";
            $this->balance = !empty($data->balance) ? $data->balance : 0;
            $this->number = !empty($data->number) ? $data->number : "";
            $this->typeAccount = !empty($data->typeAccount) ? $data->typeAccount : "";
            $this->numberAccount = !empty($data->numberAccount) ? $data->numberAccount : "";
            $this->roles = !empty($data->roles) ? $data->roles : "";
        }


    }

    public function addRole($id, $role)
    {
        $user = new \WP_User( $id );
        $user->add_role( $role );
    }

    public function removeRole($id, $role)
    {
        $user = new \WP_User( $id );
        $user->remove_role( $role );
    }

    private function addMeta($key, $value)
    {
        return update_user_meta($this->id, $key, $value);
    }

    private function getMeta($key)
    {
        $result =  get_user_meta($this->id, $key);

        return !empty($result) ?  $result[0] : null;
    }

    public function initUserMeta($data)
    {
        if(is_null($this->getMeta('balance'))) {

            $this->addMeta('balance', "0");
        }
        if(is_null($this->getMeta('relCity'))) {
            $this->addMeta('relCity', "");
        }
        if(is_null($this->getMeta('number'))) {
            $this->addMeta('number', "");
        }
        if(is_null($this->getMeta('typeAccount'))) {
            $this->addMeta('typeAccount', 0);
        }
        if(is_null($this->getMeta('numberAccount'))) {
            $this->addMeta('numberAccount', "");
        }
    }

    public function save()
    {
        $options = [
            'ID' => $this->id
            ,'first_name' => $this->name
            ,'user_email' => $this->email
        ];
        $this->addMeta('number', $this->number);
        $this->addMeta('typeAccount', $this->typeAccount);
        $this->addMeta('numberAccount', $this->numberAccount);
        $this->addMeta('relCity', $this->relCity);
        wp_update_user($options);
    }

    public function authenticate($login, $password, $remember = false) {
        return wp_signon([
            'user_login' => $login,
            'user_password' => $password,
            'remember' => $remember
        ]);
    }


    public function register($login, $email, $pass) {
        $user = wp_create_user($login, $pass, $email);
        $this->authenticate($login, $pass);
        return $user;
    }



}