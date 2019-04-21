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

    function getUploadImageManageList()
    {
        $page = I('page', 1);
        $limit = I('limit', 20);
        $cate_id = I('cate_id');
        $where['cate_id'] = $cate_id;
        $db = M('upload_image_manage');
        $total_items = $db->where($where)->count();
        $total_page = ceil($total_items / $limit);
        $list = $db->where($where)->page($page)->limit($limit)->order('id desc')->select()?:[];

        $this->ajaxReturn($this->createReturnList(true, $list, $page, $limit, $total_items, $total_page));
    }

    function getCates(){
        $page = I('page', 1);
        $limit = I('limit', 20);
        $cate_list = M("image_cate_manage")->select()?:[];
        $img_list = M("upload_image_manage")->where(['cate_id'=>$cate_list[0]['id']])->page($page,$limit)->select()?:[];
        $total_items = M("upload_image_manage")->where(['cate_id'=>$cate_list[0]['id']])->count();
        $total_page = ceil($total_items / $limit);
        $list['img_list'] = $img_list;
        $list['cate_list'] = $cate_list;
        $this->ajaxReturn($this->createReturnList(true, $list, $page, $limit, $total_items, $total_page));
    }

    function uploadImageToCate(){
        $this->display();
    }



    function getImageCate(){
        $cate_list = M("image_cate_manage")->order('listorder desc')->select();
        $this->ajaxReturn(self::createReturn(true,['cate_list'=>$cate_list]));
    }
    function saveImageToCate(){
        $cate_id = I('cate_id');
        $img_list = I('img_list');
        foreach($img_list as $v){
            M("upload_image_manage")->add([
                'url'=>$v['url'],
                'cate_id'=>$cate_id,
            ]);
        }
        $this->ajaxReturn(self::createReturn(true,null,'上传成功'));

    }
    function selectGroup(){
        $this->display();
    }
    function addCate(){
        $this->display();
    }
    function saveCate(){
        $cate_id = I('cate_id');
        $cate_name = I('cate_name');
        if($cate_id){
            $res = M("image_cate_manage")->where(['id'=>$cate_id])->save(['cate_name'=>$cate_name]);
            if($res !== false){
                $this->ajaxReturn(self::createReturn(true,$res,'添加分类成功'));
            }else{
                $this->ajaxReturn(self::createReturn(false,$res,'添加分类失败'));
            }
        }else{
            $res = M("image_cate_manage")->add(['cate_name'=>$cate_name]);
            if($res){
                $this->ajaxReturn(self::createReturn(true,$res,'添加分类成功'));
            }else{
                $this->ajaxReturn(self::createReturn(false,$res,'添加分类失败'));
            }

        }
    }
    function delCate(){
        $cate_id = I('cate_id');
        $counts =  M("upload_image_manage")->count();
        if($counts == 1){
            $this->ajaxReturn(self::createReturn(false,null,'最后一个分类不能删除!'));
        }
        $is_exist = M("upload_image_manage")->where(['cate_id'=>$cate_id])->find();
        if($is_exist){
            $this->ajaxReturn(self::createReturn(false,null,'该分类下有图片,不能删除'));
        }else{
            $res =  M("image_cate_manage")->where(['id'=>$cate_id])->delete();

            if($res !== false){
                $this->ajaxReturn(self::createReturn(true,$res,'删除分类成功'));
            }else{
                $this->ajaxReturn(self::createReturn(false,$res,'删除分类失败'));
            }
        }

    }
    function getDetail(){
        $cate_id = I('cate_id');
        $cate_name =  M("image_cate_manage")->where(['id'=>$cate_id])->getField('cate_name');
        $this->ajaxReturn(self::createReturn(true,['cate_name'=>$cate_name]));
    }
    function getGroups(){
        $res = M("image_cate_manage")->select();
        $this->ajaxReturn(self::createReturn(true,$res,null));
    }

    function moveImgsToGroup(){
        $cate_id = I('cate_id');
        $arr = I('arr');
        foreach($arr as $v){
            M("upload_image_manage")->where(['id'=>$v['id']])->save(['cate_id'=>$cate_id]);
        }
        $this->ajaxReturn(self::createReturn(true,null,'操作成功'));
    }




}
