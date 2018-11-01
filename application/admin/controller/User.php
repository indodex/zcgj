<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class User extends Admin
{
    /**
     * controller 用户列表
     * @param  integer $p 页码
     */
    public function index($p = 1){
        $map = [];
        
        // 搜索关键词
        $keywords = input('get.keywords') ? input('get.keywords') : null;
        if ($keywords) {
            $map['account'] = array('like', '%' . trim($keywords) . '%');
        }
        
        // 查看用户状态
        $status = input('status');
        if($status){
        	$map['status'] = $status;
        }
        $this -> assign('get_state',$status);
        
        $this -> assign("state", model("Common/Dict") -> showList('common_state'));
        $this -> assign("list", model('User') -> userList($map, $p));
        
        return $this -> fetch();
    }
	
	/**
	 * controller 修改用户状态
	 */
	public function state(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> modState(input('post.')));
		}
	}
	
    /**
     * controller 删除信息
     * @param  string $id ID
     */
    public function delete(){
        if(Request::instance() -> isPost()){
            return json(model('User') -> deleteInfo(input('post.id')));
        }
    }
	
    /**
     * controller 添加用户基本信息
     */
    public function add(){
    	$this -> assign('pagename','添加用户');
    	// 证件类型
    	$where['type'] = 'document_type';
    	$this -> assign('document',Db::name('dict') -> where($where) -> field('key,value') -> select());
    	
        if(Request::instance() -> isPost()){
            return json(model('User') -> addUser(input('post.')));
        }
        
        return $this -> fetch();
    }
	
	/**
	 * controller 查看用户详情
	 */
	public function detail($id){
		$this -> assign('pagename','用户详情');
		$this -> assign('account',Db::name('user') -> where('id',$id) -> value('account'));
		
		// 证件类型
    	$where['type'] = 'document_type';
    	$this -> assign('document',Db::name('dict') -> where($where) -> field('key,value') -> select());
		
		$this -> assign('detail',model('User') -> userDetail($id));
		
		return $this -> fetch();
	}
	
	/**
     * controller 重置登陆密码
     * @param  string $id ID
     */
    public function edit_pwd(){
        if(Request::instance() -> isPost()){
            return json(model('User') -> editPwd(input('post.')));
        }
    }
	
	/**
	 * controller 修改用户详情
	 */
	public function mod_detail(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> modDetail(input('post.')));
		}
	}
	
	// 上传身份证照片
	public function upload_identity(){
		$type = trim(input('type'));
		$uid = input('uid');
		if(!$type || !$uid){
			$ret = ['status' => 0,'info' => '参数错误!'];
		}else{
			// 获取表单上传文件 例如上传了001.jpg
			$file = request() -> file('file');
			// 移动到框架应用根目录/public/upload/ 目录下
			if($file){
				$info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/' . $type . '/' . $uid,true,true,2);
				if($info){
					// 成功上传后 获取上传信息
					$link = '/upload/' . $type . '/' . $uid . '/' . $info -> getSaveName();
					$ret = ['status' => 1,'info' => '上传成功!','msg' => $link];
				}else{
					// 上传失败获取错误信息
					$ret = ['status' => 0,$file -> getError()];
				}
			}else{
				$ret = ['status' => 0,'info' => '未上传!'];
			}
		}
		return json($ret);
	}
	
	/**
	 * controller 查看用户虚拟币
	 */
	public function finance($id){
		$this -> assign('pagename','用户财务');
		$this -> assign('uid',$id);
		$this -> assign('account',Db::name('user') -> where('id',$id) -> value('account'));
		$this -> assign('finance',model('User') -> finance($id));
		
		return view();
	}
	
	/**
	 * controller 修改用户券
	 */
	public function mod_voucher(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> modVoucher(input('post.')));
		}
	}
	
	/**
	 * controller 修改用户奖金
	 */
	public function mod_bouns(){
		if(Request::instance() -> isPost()){
			return json(model('User') -> modBouns(input('post.')));
		}
	}
	
	/**
	 * controller 查看/修改商城审核
	 */
	public function shop($id){
		if(Request::instance() -> isPost()){
			return json(model('User') -> shopExamine(input('post.')));
		}
		$this -> assign('pagename','商城审核');
		$this -> assign('uid',$id);
		$this -> assign('account',Db::name('user') -> where('id',$id) -> value('account'));
		$this -> assign('shop',model('User') -> shopInfo($id));
		
		return $this -> fetch();
	}
}