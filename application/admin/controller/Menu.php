<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Menu extends Admin
{
	/**
	 * controller 网站导航列表
	 */
	public function index($p = 1){
		$map = [];
		// 搜索关键词
		$keywords = trim(input('keywords')) ? trim(input('keywords')) : null;
		if(isset($keywords) || $keywords != ''){
			$map['title'] = array('like','%'.$keywords.'%');
		}
		
		// 查看显示状态
		$is_show = input('is_show');
		if($is_show){
			$map['is_show'] = $is_show;
		}
		$this -> assign('get_is_show',$is_show);
		
		$this -> assign('state',model('Common/Dict') -> showList('is_show'));
		$this -> assign('menu_type',model('Common/Dict') -> showList('menu_type'));
		
		$this -> assign('list',model('Menu') -> menuList($map,$p));
		
		return $this -> fetch();
	}
	
	/**
	 * controller 设置显示状态
	 */
	public function is_show(){
		if(Request::instance() -> isPost()){
			return json(model('Menu') -> is_show(input('post.')));
		}
	}
	
	/**
	 * ccontroller 设置排序
	 */
	public function sort(){
		if(Request::instance() -> isPost()){
			return json(model('Menu') -> setSort(input('post.')));
		}
	}
	
	/**
	 * controller 删除导航
	 */
	public function delete(){
		if(Request::instance() -> isPost()){
			return json(model('Menu') -> deleteInfo(input('post.id')));
		}
	}
	
	/**
	 * controller 添加导航
	 */
	public function add(){
		$this -> assign('pagename','添加导航');
		if(Request::instance() -> isPost()){
			return json(model('Menu') -> saveInfo(input('post.')));
		}else{
			return $this -> fetch();
		}
	}
	
	/**
	 * controller 修改导航
	 */
	public function edit($id){
		$this -> assign('pagename','修改导航');
		$this -> assign('info',model('Menu') -> modInfo($id));
		if(Request::instance() -> isPost()){
			return json(model('Menu') -> saveInfo(input('post.')));
		}else{
			return $this -> fetch('add');
		}
	}
	
	/**
	 * controller 网站单页列表
	 */
	public function page($p = 1){
		
		$map = [];
		$keywords = input('keywords') ? input('keywords') : null;
		if($keywords){
			$map['name'] = array('like','%'.trim($keywords).'%');
		}
		
		$this -> assign('list',model('Page') -> pageList($map,$p));
		
		return $this -> fetch();
	}
	
	/**
	 * controller 添加单页
	 */
	public function add_page(){
		$this -> assign('pagename','添加单页');
		if(Request::instance() -> isPost()){
			return json(model('Page') -> addPage(input('post.')));
		}
		return $this -> fetch();
	}
	
	/**
	 * controller 修改单页
	 */
	public function edit_page($id){
		$this -> assign('pagename','修改单页');
		$this -> assign('page',model('Page') -> modPage($id));
		if(Request::instance() -> isPost()){
			return json(model('Page') -> addpage(input('post.')));
		}
		return $this -> fetch('add_page');
	}
	
	/**
	 * controller 删除单页
	 */
	public function delete_page($id){
		return json(model('Page') -> deletePage($id));
	}
}
