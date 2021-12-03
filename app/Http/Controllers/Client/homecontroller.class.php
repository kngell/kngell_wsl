<?php

declare(strict_types=1);

class HomeController extends Controller
{
    public function __construct()
    {
    }

    /**
     * IndexPage
     * ===========================================================================.
     * @param array $data
     * @return void
     */
    public function indexPage(array $data = [])
    {
        $this->view_instance->set_pageTitle('Home');
        $this->view_instance->set_siteTitle('Home');
        $this->view_instance->render('home' . DS . 'index', ['slider' => $this->helper->getSliders()->index_phone]);
    }

    //page product
    public function productPage($data = [])
    {
        $id = $this->request->add_slashes(array_pop($data));
        if ($id) {
            $this->view_instance->set_pageTitle('Product');
            $this->view_instance->set_siteTitle('Product');
            $this->view_instance->p_details = $this->container->make(ProductsManager::class)->getDetails($id, 'p_slug');
        }
        $this->view_instance->render('home' . DS . 'product' . DS . 'product');
    }

    // Product details custom
    public function detailsPage($data = [])
    {
        $id = $this->request->add_slashes(array_pop($data));
        if ($id) {
            $this->view_instance->set_siteTitle('Product Details');
            $this->view_instance->set_pageTitle('Details');
            $this->view_instance->p_details = $this->container->make(ProductsManager::class)->getDetails($id, 'p_slug');
        }
        $this->view_instance->render('home' . DS . 'product' . DS . 'details');
    }

    //page cart
    public function cartPage($data = [])
    {
        // $this->view_instance->cart_product_list = file_get_contents(FILES . 'template' . DS . 'e_commerce' . DS . 'shopping_cart' . DS . '_php_shpping_cart_template.php');
        $this->view_instance->set_pageTitle('Cart');
        $this->view_instance->set_siteTitle('Cart');
        $this->view_instance->render('home' . DS . 'cart' . DS . 'cart');
    }

    // Boutique page
    public function boutiquePage()
    {
        $this->view_instance->set_pageTitle('Boutique');
        $this->view_instance->set_siteTitle('Boutique');
        $this->view_instance->render('home' . DS . 'boutique' . DS . 'boutique');
    }

    //Contact
    public function contactPage()
    {
        $this->view_instance->set_pageTitle('Contact');
        $this->view_instance->set_siteTitle('Contact');
        $formAttr = [
            'method' => 'post',
            'formClass' => 'px-3 needs-validation',
            'formCustomAttr' => 'novalidate',
            'formID' => 'contact-frm',
            'fieldWrapperClass'=>'input-box',
            'token'=>$this->token,
            'alertErr'=>true,
        ];
        $this->view_instance->form = $this->container->make(Form::class);
        $this->view_instance->render('home' . DS . 'contact' . DS . 'contact', ['frm' => $formAttr]);
    }

    //sitemap
    public function sitemapPage()
    {
        $this->view_instance->set_pageTitle('Sitemap');
        $this->view_instance->render('home' . DS . 'sitemap' . DS . 'sitemap');
    }

    protected function before()
    {
        parent::before();
    }

    protected function after()
    {
    }
}
