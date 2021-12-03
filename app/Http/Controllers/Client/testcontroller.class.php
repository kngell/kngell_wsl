<?php

declare(strict_types=1);

class TestController extends Controller
{
    public function __construct()
    {
    }

    public function testPage()
    {
        $this->view_instance->set_Layout('test');
        $this->view_instance->render('test' . DS . 'test_modal');
    }
}
