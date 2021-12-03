<?php

declare(strict_types=1);
class AdminController extends Controller
{
    public function __construct()
    {
    }

    //page index
    public function indexPage($data = [])
    {
        $this->view_instance->set_pageTitle('Dashboard');
        $this->view_instance->render('admin' . DS . 'index');
    }

    //not found page
    public function not_found_page()
    {
        $this->view_instance->set_pageTitle('Not Found Page');
        $this->view_instance->render('error' . DS . 'not_found_page');
    }

    //page index
    public function analytics($data = [])
    {
        $this->view_instance->set_pageTitle('Analytics');
        $this->view_instance->render('admin' . DS . 'analytics');
    }

    //page index
    public function calendar($data = [])
    {
        $this->view_instance->set_pageTitle('Calendar');
        $this->view_instance->render('admin' . DS . 'calendar');
    }

    // Categories add and Manage categories
    public function allcategoriesPage()
    {
        $this->view_instance->set_pageTitle('Manage Categories');
        $this->view_instance->render('admin' . DS . 'products' . DS . 'allcategories');
    }

    // Units Management
    public function allunitsPage()
    {
        $this->view_instance->set_pageTitle('Units');
        $this->view_instance->render('admin' . DS . 'products' . DS . 'allunits');
    }

    // public function login()
    // {
    //     $this->view_instance->set_Layout('adminlogin');
    //     $this->view_instance->set_pageTitle('Login');
    //     $this->view_instance->render('admin' . DS . 'login');
    // }

    public function allusersPage($method)
    {
        // dd(($this->get_model('UsersManager'))->get_Tables_Column('users'));
        $profile_upload = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'upload_profile_template.php');
        $profile_upload = str_replace('{{form_id}}', 'add-user-frm', $profile_upload);
        $profile_upload = str_replace('{{camera}}', IMG . 'camera' . DS . 'camera-solid.svg', $profile_upload);
        $profile_upload = str_replace('{{avatar}}', IMG . 'users' . DS . 'avatar.png', $profile_upload);
        $this->view_instance->profile_upload = $profile_upload;
        $this->view_instance->set_viewData($this->container->make(UsersManager::class));
        $this->view_instance->user_method = array_pop($method);
        $this->view_instance->set_pageTitle('All Users');
        $this->view_instance->render('admin' . DS . 'users' . DS . 'allusers');
    }

    //users profile
    public function profilePage()
    {
        $this->view_instance->set_pageTitle('Profile');
        $this->view_instance->render('admin' . DS . 'users' . DS . 'profile');
    }

    // Users permissions
    public function permissionsPage()
    {
        $this->view_instance->set_pageTitle('permissions');
        $this->view_instance->render('admin' . DS . 'users' . DS . 'permissions');
    }

    // All product
    public function allproductsPage()
    {
        $this->view_instance->set_viewData($this->container->make(CategoriesManager::class)->getAllItem(['return_mode' => 'class'])->get_results());
        $this->view_instance->set_pageTitle('AllProducts');
        $this->view_instance->dragAndDrop = file_get_contents(FILES . 'template' . DS . 'base' . DS . 'dragandDropTemplate.php');
        $this->view_instance->render('admin' . DS . 'products' . DS . 'allproducts');
    }

    // Product Details
    public function product_detailsPage()
    {
        $this->view_instance->set_pageTitle('Product Details');
        $this->view_instance->render('admin' . DS . 'products' . DS . 'product_details');
    }

    // Add new Product
    public function new_productPage()
    {
        // dd(($this->get_model('UsersManager'))->get_Tables_Column('products'));
        $this->view_instance->set_pageTitle('New Product');
        $this->view_instance->render('admin' . DS . 'products' . DS . 'new_product');
    }

    //Manage Companies
    public function allcompaniesPage()
    {
        // dd(($this->get_model('UsersManager'))->get_Tables_Columns('address_book'));
        $this->view_instance->set_pageTitle('All Companies');
        $this->view_instance->render('admin' . DS . 'company' . DS . 'allcompanies');
    }

    public function allwarehousesPage()
    {
        $this->view_instance->set_pageTitle('All WareHouses');
        $this->view_instance->render('admin' . DS . 'warehouse' . DS . 'allwarehouses');
    }

    //Manage Companies
    public function allbrandsPage()
    {
        // dd(($this->get_model('UsersManager'))->get_Tables_Columns('address_book'));
        $this->view_instance->set_pageTitle('All Brands');
        $this->view_instance->render('admin' . DS . 'products' . DS . 'allbrands');
    }

    //Companny details
    public function company_detailsPage($data)
    {
        $this->view_instance->company = $this->container->make(CompanyManager::class)->getDetails(array_pop($data));
        $this->view_instance->set_pageTitle('Company Details');
        $this->view_instance->render('admin' . DS . 'company' . DS . 'company_details');
    }

    public function shippingClassPage()
    {
        $this->view_instance->set_pageTitle('Shipping Classes');
        $this->view_instance->render('admin' . DS . 'shipping' . DS . 'shippingclass');
    }

    public function alltaxesPage()
    {
        $this->view_instance->set_pageTitle('Taxes Management');
        $this->view_instance->render('admin' . DS . 'company' . DS . 'alltaxes');
    }

    public function allpostsPage()
    {
        $this->view_instance->set_pageTitle('Posts Management');
        $this->view_instance->post_frm = $this->container->make(Form::class);
        $this->view_instance->render('admin' . DS . 'posts' . DS . 'allposts', ['params'=>$this->response->posts_frm_params()]);
    }

    public function ordersPage()
    {
        $this->view_instance->set_pageTitle('Orders Management');
        $this->view_instance->render('admin' . DS . 'orders' . DS . 'orders');
    }

    public function settingsPage($type)
    {
        $page = array_pop($type);
        $formAttr = [
            'method' => 'post',
            'formClass' => 'px-3 needs-validation',
            'formCustomAttr' => 'novalidate',
        ];
        $this->view_instance->set_pageTitle('Settings');
        $this->view_instance->form = $this->container->make(Form::class);
        switch ($page) {
                    case 'general':
                        $formAttr['formID'] = 'add-general_settings-frm';
                    break;
                    case 'sliders':
                    $formAttr['formID'] = 'add-sliders-frm';
                    break;
                    default:
                    // code...
                    break;
        }
        $this->view_instance->render('admin' . DS . 'settings' . DS . $page, ['frm' => $formAttr]);
    }
}
