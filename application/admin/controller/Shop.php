<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Shop extends Admin
{
	
	/**
	 * controller 商品分类列表
	 */
	public function index(){
		
		$this -> assign('class',model('Shop') -> classList());
		return $this -> fetch();
	}
	
	/**
	 * controller 添加分类
	 */
	public function add(){
		if(Request::instance() -> isPost()){
			return json(model('Shop') -> addClass(input('post.')));
		}
		
		$this -> assign('pagename','添加分类');
		return $this -> fetch();
	}
	
	/**
	 * controller 修改分类
	 */
	public function edit($id){
		if(Request::instance() -> isPost()){
			return json(model('Shop') -> addClass(input('post,')));
		}
		
		$this -> assign('class',Db::name('goods_classify') -> where('id',$id) -> find());
		$this -> assign('pagename','修改分类');
		return $this -> fetch('add');
	}
	
	/**
	 * controller 删除分类
	 */
	public function delete($id){
		return json(model('Shop') -> deleteClass($id));
	}
	
    /**
     * controller 优惠专区
     */
    public function preferential($p = 1){
    	
    	$this -> assign('goods',model('Shop') -> preferential($map,$p));
    	return $this -> fetch();
    }
    
    /**
	 * controller 优惠专区添加商品
	 */
	public function add_preferential_goods(){
		if(Request::instance() -> isPost()){
			return json(model('Shop') -> addPreferentialGoods(input('post.')));
		}
		
		// 获取当前登陆管理员ID
		$aid = $_SESSION['think']['aid'];
		$this -> assign('shop_id',model('Shop') -> get_shop($aid));	// 获取店铺ID
		$this -> assign('class',model('Shop') -> classList());
		$this -> assign('pagename','添加商品');
		return $this -> fetch();
	}
    
    /**
     * controller 特色专区
     */
    public function feature(){
    	
    	return $this -> fetch();
    }
	
	/**
	 * controller 特色专区添加商品
	 */
	public function add_feature_goods(){
		
		$this -> assign('pagename','添加商品');
		return $this -> fetch();
	}
	
	
	
	// 上传图片
    public function upload_pic(){
    	$type = trim(input('type'));
    	$shop_id = input('shop_id');	// 店铺ID
    	if(!$type || !$shop_id){
    		$ret = ['code' => 0,'msg' => '参数错误!'];
    	}else{
    		$file = request() -> file('file');
    		if($file){
    			$info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/' . $type . '/' . $shop_id,true,true,2);
    			if($info){
    				$link = '/upload/' . $type . '/' . $shop_id . '/' . $info -> getSaveName();
    				$ret = ['code' => 1,'msg' => '上传成功!','url' => $link];
    			}else{
    				$ret = ['code' => 0,'msg' => $file -> getError()];
    			}
    		}else{
    			$ret = ['code' => 0,'msg' => '未上传!'];
    		}
    	}
    	return json($ret);
    }
    
    // 上传多张图片
    public function upload_pics(){
    	$type = trim(input('type'));
    	$shop_id = input('shop_id');	// 店铺ID
    	if(!$type || !$shop_id){
    		$ret = ['code' => 0,'msg' => '参数错误!'];
    	}else{
    		$file = request() -> file('file');
    		if($file){
    			$info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/' . $type . '/' . $shop_id,true,true,2);
    			if($info){
    				$link = '/upload/' . $type . '/' . $shop_id . '/' . $info -> getSaveName();
    				$ret = ['code' => 1,'msg' => '上传成功!','url' => $link];
    			}else{
    				$ret = ['code' => 0,'msg' => $file -> getError()];
    			}
    		}else{
    			$ret = ['code' => 0,'msg' => '未上传!'];
    		}
    	}
    	return json($ret);
    }
}
