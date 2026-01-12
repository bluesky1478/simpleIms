<?php
namespace Controller\Front\Ics;

use Controller\Admin\Ims\IcsControllerTrait;

class IcsStyleInspectController extends \Controller\Front\Controller
{
    use IcsControllerTrait;

    public function index() {
        $this->setDefault();

        $this->setData('styleSno', \Request::request()->toArray()['sno']);
    }
}