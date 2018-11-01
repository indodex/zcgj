<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Banner extends Admin
{
    /**
     * controlle 轮播图列表
     * @param  integer $p 页码
     */
    public function index($p = 1){
        $map = [];
        
        // 搜索关键词
        $keywords = input('get.keywords') ? input('get.keywords') : null;
        if($keywords) {
            $map['title'] = array('like','%'.trim($keywords).'%');
        }
        
        // 查看状态
        $state = input('state');
        if($state){
        	$map['state'] = $state;
        }
        $this -> assign('get_state',$state);
        
        $this -> assign("state", model("Common/Dict") -> showList('common_state'));
        $this -> assign("list", model('Banner') -> bannerList($map, $p));
        
        return $this -> fetch();
    }
	
	/**
	 * controlle 设置轮播图显示状态
	 */
	public function state(){
		if(Request::instance() -> isPost()){
			return json(model('Banner') -> setState(input('post.')));
		}
	}
	
	/**
	 * controller 排序
	 */
	public function sort(){
		if(Request::instance() -> isPost()){
			return json(model('Banner') -> setSort(input('post.')));
		}
	}
	
	/**
     * controlle 删除轮播图
     * @param  string $id ID
     */
    public function delete(){
        if (Request::instance() -> isPost()) {
            return json(model('Banner') -> deleteInfo(input('post.id')));
        }
    }
	
    /**
     * controller 添加轮播图
     */
    public function add(){
    	$this -> assign('pagename','添加轮播图');
        
        if(Request::instance() -> isPost()){
            return json(model('Banner') -> saveInfo(input('post.')));
        }else{
        	return $this -> fetch();
        }
        
    }
    
    /**
     * controlle 修改轮播图
     * @param  string $id ID
     */
    public function edit($id){
    	$this -> assign('pagename','修改轮播图');
    	$this -> assign('info',model('Banner') -> modInfo($id));
    	
        if (Request::instance() -> isPost()) {
            return json(model('Banner') -> saveInfo(input('post.')));
        }else{
        	return $this -> fetch('add');
        }
    }

	// 上传轮播图
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
}
