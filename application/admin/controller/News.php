<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class News extends Admin
{
	/**
	 * controller 公告列表
	 */
	public function index($p = 1){
		$map = [];
		// 搜索关键词
		$keywords = trim(input('keywords')) ? trim(input('keywords')) : null;
		if(isset($keywords) || $keywords != ''){
			$map['title'] = array('like','%'.$keywords.'%');
		}
		
		// 查看显示状态
		$state = input('state');
		if($state){
			$map['state'] = $state;
		}
		$this -> assign('get_is_show',$state);
		$this -> assign("is_show",model("Common/Dict") -> showList('is_show'));
		
		$this -> assign('list',model('News') -> newsList($map,$p));
		return $this -> fetch();
	}
	
	/**
	 * controller 修改公告显示状态
	 */
	public function change_is_show(){
		if(Request::instance()){
			return json(model('News') -> changeIsShow(input('post.')));
		}
	}
	
	/**
	 *  controller 删除公告
	 */
	public function delete(){
		if(Request::instance() -> isPost()){
			return json(model('News') -> deleteInfo(input('post.id')));
		}
	}
	
	/**
	 * controller 添加公告
	 */
	public function add(){
		$this -> assign('pagename','添加新闻');
		if(Request::instance() -> isPost()){
			return json(model('News') -> saveInfo(input('post.')));
		}else{
			return $this -> fetch();
		}
	}
	
	/*
	 * controller 修改公告
	 */
	public function edit($id){
		$this -> assign('state',model('Common/Dict') -> showList('news_type'));
		$this -> assign('pagename','修改新闻');
		$this -> assign('news',model('News') -> modInfo($id));
		if(Request::instance() -> isPost()){
			return json(model('News') -> saveInfo(input('post.')));
		}else{
			return $this -> fetch('add');
		}
	}
	
}
