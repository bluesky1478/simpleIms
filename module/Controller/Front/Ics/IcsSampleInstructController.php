<?php
namespace Controller\Front\Ics;

use Controller\Admin\Ims\IcsControllerTrait;

//샘플지시서 Controller
class IcsSampleInstructController extends \Controller\Front\Controller
{
    use IcsControllerTrait;

    public function index()
    {
        $this->setDefault();

        $this->setData('managerSno', \Session::get('manager.sno'));
        $this->setData('sampleSno', \Request::request()->toArray()['sno']);
    }
}