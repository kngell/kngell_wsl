<?php

declare(strict_types=1);
class RestrictedController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //page index
    public function indexPage()
    {
        $this->view_instance->render('restricted' . DS . 'index');
    }

    public function badtokenPage()
    {
        $this->view_instance->render('restricted' . DS . 'badtoken');
    }

    public function Add()
    {
        $this->jsonResponse(['result' => 'error', 'msg' => FH::showMessage('info text-center', 'Connecter-vous pour envoyer des commentaires au serveur')]);
    }
}
