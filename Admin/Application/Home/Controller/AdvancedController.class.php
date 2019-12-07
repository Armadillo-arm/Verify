<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class AdvancedController extends BaseController {
    public function index(){
        $this->display();
    }
}