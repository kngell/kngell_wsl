<?php

declare(strict_types=1);

class UsersController extends Controller
{
    public function __construct()
    {
    }

    //=======================================================================
    //Account
    //=======================================================================
    public function accountPage($data)
    {
        $page = array_pop($data);
        $this->view_instance->set_pageTitle(ucfirst($page));
        $this->view_instance->set_siteTitle(ucfirst($page));
        $this->view_instance->set_viewData($this->container->make(UsersManager::class));
        if (in_array($page, ['account', 'profile', 'payment', 'login', 'register'])) {
            if (isset(AuthManager::$currentLoggedInUser)) {
                !in_array($page, ['login', 'register']) ? $this->view_instance->render('users' . DS . 'account' . DS . $page) : '';
            } else {
                if ($page == 'register') {
                    $temp = file_get_contents(VIEW . $this->filePath . 'users' . DS . 'account' . DS . 'partials' . DS . '_register.php');
                    $temp = str_replace('{{link}}', PROOT . 'users' . DS . 'account' . DS . 'login', $temp);
                    $temp = str_replace('{{csrf}}', FH::csrfInput('csrftoken', $this->token->generate_token(8)), $temp);
                } else {
                    $temp = file_get_contents(VIEW . $this->filePath . 'users' . DS . 'account' . DS . 'partials' . DS . '_login.php');
                    $temp = str_replace('{{link}}', PROOT . 'users' . DS . 'account' . DS . 'register', $temp);
                    $temp = str_replace('{{csrf}}', FH::csrfInput('csrftoken', $this->token->generate_token(8)), $temp);
                }
                $this->view_instance->log_file = $temp;
                $this->view_instance->render('users' . DS . 'account' . DS . 'login');
                $temp = '';
            }
        } else {
            Rooter::redirect('restricted' . DS . 'index');
        }
    }

    //=======================================================================
    //Checkout
    //=======================================================================
    public function checkoutPage()
    {
        $this->view_instance->set_pageTitle('Checkout');
        $this->view_instance->set_siteTitle('K\'nGELL Ingenierie Logistique');
        if (isset(AuthManager::$currentLoggedInUser)) {
            $this->view_instance->user_data = $this->container->make(UsersManager::class)->get_single_user(AuthManager::$currentLoggedInUser->userID);
            $this->view_instance->shipping_class = $this->container->make(ShippingClassManager::class)->getAllItem(['return_mode' => 'class']);
            $this->view_instance->stripe_key = YamlFile::get('paymentgateawaykeys');
            $this->view_instance->pmt_getaway = $this->container->make(PaymentGatewayManager::class)->getAllItem(['return_mode' => 'class']);
            if (!null == $this->view_instance->user_cart[5]['amount']) {
                $this->view_instance->user_cart[2][1] = $this->money->getAmount($this->view_instance->user_cart[5]['amount'])->plus($this->view_instance->user_cart[2][1]);
            }
            $this->view_instance->form = $this->container->make(Form::class);
            $this->view_instance->credit_cardForm = $this->response->get_credit_card($this->view_instance->form);
            $this->view_instance->address_book = str_replace('{{content}}', $this->container->make(AddressBookManager::class)->get_userAddressHtml(), file_get_contents(FILES . 'template' . DS . 'e_commerce' . DS . 'account' . DS . 'addessTemplate.php', true));
            $this->open_userCheckoutSession();
            $this->render('users' . DS . 'checkout' . DS . 'checkout', ['frm' => $this->frm_params($this->view_instance)]);
        } else {
            // $this->render('errors' . DS . '_errors', ['exception' => $e]);
            throw new CheckoutNavigationException('Merci de vous reconnecter à nouveau');
        }
        //     catch (\Exception $e) {
        //         $this->container->rooter->getResponse()->setStatusCode($e->getCode());
        //         Rooter::redirect('errors' . DS . '_errors', ['exception' => $e]);

        // }
    }

    //=======================================================================
    //Profile
    //=======================================================================
    public function profilePage()
    {
        // dd(($this->get_model('UsersManager')['users'])->get_Tables_Column('commandes'));
        $this->view_instance->set_pageTitle('Profile');
        $this->view_instance->set_siteTitle('Profile');
        $this->view_instance->render('users' . DS . 'account' . DS . 'profile');
    }

    //=======================================================================
    //Email verified results
    //=======================================================================
    public function emailverifiedPage($data)
    {
        $msg = '';
        foreach ($data as $value) {
            $msg .= $value;
        }
        $this->view_instance->msg = $msg;
        $this->view_instance->set_pageTitle('Email Verification');
        $this->view_instance->render('users' . DS . 'account' . DS . 'emailverified');
    }

    //=======================================================================
    //Reset password
    //=======================================================================
    public function resetpassword($data)
    {
        $this->view_instance->set_pageTitle('Reset Password');
        $this->view_instance->render('users' . DS . 'account' . DS . 'resetpassword');
    }

    //Payment
    public function payment_successPage()
    {
        $this->view_instance->set_pageTitle('Payment');
        if ($this->session->exists(CHECKOUT_PROCESS_NAME)) {
            $userdata = $this->session->get(CHECKOUT_PROCESS_NAME);
            if (is_array($userdata)) {
                $this->view_instance->set_viewData($userdata);
                $this->session->delete(CHECKOUT_PROCESS_NAME);
                $cart = $this->container->make(CartManager::class);
                $cart->delete(['cart_id' => ['value' => $userdata['cart_items'], 'operator' => 'IN']]);
            }
        }
        $this->view_instance->render('users' . DS . 'payment' . DS . 'payment_success');
    }

    public function successPayment($data)
    {
    }

    private function frm_params(View $view)
    {
        return [
            'action'=>'#',
            'method' => 'post',
            'formClass' => 'user-ckeckout-frm needs-validation',
            'formCustomAttr' => 'novalidate',
            'formID' => 'user-ckeckout-frm',
            'fieldWrapperClass'=>'input-box',
            'token'=>$this->token,
            'model'=> $view->user_data,
            'enctype'=>'multipart/form-data',
            'autocomplete'=>'nope',
            'alertErr'=>true,
            'inputHidden'=>[
                'total-ttc'=>isset($view->user_cart[2][1]) ? $view->user_cart[2][1] : '',
                'total-ht'=>isset($view->user_cart[2][0]) ? $view->user_cart[2][0] : '',
            ],
            'nestField'=>true,
            'fieldCommonclass'=>[
                'fieldclass' => 'input-box__input',
                'labelClass' => 'input-box__label',
            ],
        ];
    }
}
