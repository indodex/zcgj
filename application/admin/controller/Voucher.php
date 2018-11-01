<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Voucher extends Admin
{
	
	/**
	 * controller 券列表
	 */
	public function index($p = 1){
		$map = [];
		$keywords = trim(input('keywords')) ? trim(input('keywords')) : null;
		if(isset($keywords) || $keywords != ''){
			$map['name'] = array('like','%'.$keywords.'%');
		}
		$this -> assign('list',model('Voucher') -> voucherList($map,$p));
		return $this -> fetch();
	}
	
	/**
	 * controller 新增券
	 */
	public function add(){
		$this -> assign('pagename','添加券');
		if(Request::instance() -> isPost()){
			return json(model('Voucher') -> saveInfo(input('post.')));
		}else{
			return $this -> fetch();
		}
	}
	
	/**
	 * controller 修改券
	 */
	public function edit($id){
		$this -> assign('pagename','修改券');
		$this -> assign('vou',model('Voucher') -> modInfo($id));
		if(Request::instance() -> isPost()){
			return json(model('Voucher') -> saveInfo(input('post.')));
		}else{
			return $this -> fetch('add');
		}
	}
	
	// 上传/修改券图标
	public function upload(){
        $type = trim(input('type'));
        if(!$type){
            $r = ['status' => 0,'info' => '参数不正确'];
        }else{
            // 获取表单上传文件 例如上传了001.jpg
            $file = request() -> file('file');
            // 移动到框架应用根目录/public/upload/ 目录下
            if($file){
                $info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/' . $type,true,true,2);
                if($info){
                    // 成功上传后 获取上传信息
                    $link = '/upload/' . $type . '/' . $info -> getSaveName();
                    $r = ['status' => 1,'info' => '上传成功','msg' => $link];
                }else{
                    // 上传失败获取错误信息
                    $r = ['status' => 0,$file -> getError()];
                }
            }else{
                $r = ['status' => 0,'info' => '未上传'];
            }
        }
        return json($r);
    }
	
	/**
	 * controller 删除券
	 */
	public function delete(){
		if(Request::instance() -> isPost()){
			return json(model('Voucher') -> deleteInfo(input('post.id')));
		}
	}
	
	
}


