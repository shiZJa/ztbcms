<?php
/**
 * User: jayinton
 * Date: 2019/3/5
 * Time: 10:58
 */

namespace Upload\Controller;


use Common\Controller\AdminBase;

class UploadCenterController extends AdminBase
{
    function imageUploadPanel(){
        $this->display();
    }

    function fileUploadPanel(){
        $this->display();
    }

    function imageManagePanel(){
        $this->display();
    }

}