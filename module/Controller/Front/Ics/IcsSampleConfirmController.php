<?php
namespace Controller\Front\Ics;

use Controller\Admin\Ims\IcsControllerTrait;

class IcsSampleConfirmController extends \Controller\Front\Controller
{
    use IcsControllerTrait;

    public function index()
    {
        $this->setDefault();

        $this->setData('managerSno', \Session::get('manager.sno'));
        $this->setData('sampleSno', \Request::request()->toArray()['sno']);
    }
}