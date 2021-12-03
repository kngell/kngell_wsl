<?php

declare(strict_types=1);
class UserSessionsManager extends Model
{
    protected $_table = 'user_sessions';
    protected $_colIndex = 'user_cookie';
    protected $_colID = 'usID';

    //=======================================================================
    //construct
    //=======================================================================
    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function initUserSession($userSession = null)
    {
        if ($userSession) {
            $cond = ['where' => ['user_cookie' => $userSession], 'return_mode' => 'class', 'class' => 'UserSessionsManager'];
            $us = $this->findFirst($cond);
            if ($us) {
                foreach ($us as $key => $val) {
                    $this->$key = $val;
                }
            }
        }
    }

    //=======================================================================
    //Setters
    //=======================================================================
    public function set_userSession($m = null)
    {
        if ($this->count() > 1) {
            foreach ($this->get_results() as $session) {
                $session->delete();
            }

            return $this->create_user_session($m);
            // $this->update_user_session(current($this), $m);
        }
        if ($this->count() === 1) {
            return $this->update_user_session(current($this->get_results()), $m);
        }

        return false;
    }

    public function update_user_session($s = null, $u = null)
    {
        $save = [];
        $i = 0;
        $s->id = $s->{$s->get_colID()};
        if ($u->remember_cookie) {
            if ($s->remember_cookie != $u->remember_cookie) {
                $s->remember_cookie = $u->remember_cookie ?? $this->get_unique('remember_cookie');
                $save[$i] = true;
                $i++;
            }
        }
        if ($u->user_cookie) {
            if ($s->user_cookie != $u->user_cookie) {
                $s->user_cookie = $u->user_cookie ?? $this->get_unique('user_cookie');
                $save[$i] = true;
                $i++;
            }
        }
        if ($u->email) {
            if ($s->email != $u->email) {
                $s->email = $u->email;
                $save[$i] = true;
                $i++;
            }
        }
        if ($s->user_agent != Session::uagent_no_version()) {
            $s->user_agent = Session::uagent_no_version();
            $save[$i] = true;
            $i++;
        }
        if ($s->remember_cookie != $u->remember_cookie) {
            $s->remember_cookie = $u->remember_cookie;
            $save[$i] = true;
            $i++;
        }
        if (isset($s->profileImage)) {
            unset($s->profileImage);
        }

        return in_array(true, $save) ? $s->save() : false;
    }

    public function create_user_session($user = null)
    {
        $this->userID = $user->userID ?? '';
        $this->remember_cookie = $user->remember_cookie ?? '';
        $this->email = $user->email ?? '';
        $this->user_agent = Session::uagent_no_version();
        $this->user_cookie = $user->user_cookie ?? $this->get_unique('token');

        return $this->save();
    }

    // public function check_session($obj1 = null, $obj2 = null)
    // {
    //     return array_diff_assoc((array)$obj1, [$obj2]);
    // }
}
