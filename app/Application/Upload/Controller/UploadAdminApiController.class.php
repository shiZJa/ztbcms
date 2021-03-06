<?php
/**
 * User: jayinton
 * Date: 2019/3/5
 * Time: 14:55
 */

namespace Upload\Controller;


use Admin\Controller\AdminApiBaseController;
use Admin\Service\User;

class UploadAdminApiController extends AdminApiBaseController
{

    const isadmin = 1; //后台上传

    //模块
    const MODULE_IMAGE = 'module_upload_images';
    const MODULE_FILE = 'module_upload_files';

    /**
     * @param $module  string 文件所属模块
     */
    private function _upload($module)
    {
        if (IS_POST) {
            //回调函数
            $Callback = false;
            $userInfo = User::getInstance()->getInfo();
            $upuserid = $userInfo['id'];
            //取得栏目ID
            $catid = I('post.catid', 0, 'intval');
            //获取附件服务
            $Attachment = service("Attachment", array('module' => $module, 'catid' => $catid, 'isadmin' => self::isadmin, 'userid' => $upuserid));

            //开始上传
            $info = $Attachment->upload($Callback);
            if ($info) {
                $this->ajaxReturn([
                    'status' => true,
                    'data' => [
                        'url' => $info[0]['url'],
                        'name' => $info[0]['name'],
                    ],
                    'msg' => '上传成功'
                ]);
            } else {
                //上传失败，返回错误
                $this->ajaxReturn([
                    'status' => false,
                    'msg' => $Attachment->getErrorMsg()
                ]);
            }
        } else {
            $this->ajaxReturn([
                'status' => false,
                'msg' => '上传失败'
            ]);
        }
    }

    function uploadImage(){
        $this->_upload(self::MODULE_IMAGE);
    }

    function uploadFile(){
        $this->_upload(self::MODULE_FILE);
    }

    function getGalleryList()
    {
        $page = I('page', 1);
        $limit = I('limit', 20);
        $userInfo = User::getInstance()->getInfo();
        $userid = $userInfo['id'];

        $db = M('Attachment');
        $where = [
            'module' => self::MODULE_IMAGE,
            'userid' => $userid,
            'isadmin' => 1,
        ];
        $total_items = $db->where($where)->count();
        $total_page = ceil($total_items / $limit);
        $list = $db->where($where)->page($page)->limit($limit)->order(array("uploadtime" => "DESC"))->select();

        $return_list = [];
        foreach ($list as $index => $item) {
            $return_list [] = [
                'name' => $item['filename'],
                'url' => cache('Config.sitefileurl') . $item['filepath'],
                'filepath' => $item['filepath'],
            ];
        }

        $this->ajaxReturn($this->createReturnList(true, $return_list, $page, $limit, $total_items, $total_page));
    }
}