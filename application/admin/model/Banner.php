<?php
namespace  app\admin\model;

use app\common\model\Base;
use think\Request;
use think\db;

class Banner extends Base
{

    /**
    *更新时自动完成
    */
    protected $update = [];

    const PAGE_LIMIT = '10';	// 用户表分页限制
    const PAGE_SHOW = '10';		// 显示分页菜单数量


    /**
     * model 获取轮播图列表
     * @param  array $map 查询条件
     * @param  string $p  页码
     * @return array      返回列表
     */
    public function bannerList($map, $p){
        $request= Request::instance();
        
        $list = $this -> where($map) -> order('sort ASC') -> page($p, self::PAGE_LIMIT) -> select() -> toArray();
        foreach($list as $k => $v){
			$where['type'] = 'common_state';
			$where['value'] = $v['state'];
			$list[$k]['state_type'] = Db::name('dict') -> where($where) -> value('key');
			if($v['state'] == 1){
				$list[$k]['button'] = 'state_green';
			}else{
				$list[$k]['button'] = 'state_red';
			}
		}
        $count = $this -> where($map) -> count();
        
        $return['list'] = $list;
        $return['count'] = $count;
        $return['page'] = boot_page($return['count'], self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request->action());
        return $return;
    }
	
	/** 
	 * model 设置轮播图显示状态
	 */
	public function setState($data){
		$id = $data['id'];
		$state = $data['state'];
		
		if($state == 1){
			$mod['state'] = 2;
		}else{
			$mod['state'] = 1;
		}
		
		$info = $this -> where('id',$id) -> update($mod);
		if($info){
			return ['code' => 1,'msg' => '设置成功!'];
		}else{
			return ['code' => 0,'msg' => '设置失败!'];
		}
	}
	
	/**
	 * model 设置排序
	 */
	public function setSort($data){
		$id = $data['id'];
		$type = $data['type'];
		
		$obj = $this -> where('id',$id) -> find();
		
		if($type == 'up'){
			$up_obj = $this -> where('sort < ' . $obj['sort']) -> order('sort DESC') -> find();
			if(!$up_obj){
				return ['code' => 0,'msg' => '已升至最高!'];
			}
			$data['sort'] = $up_obj['sort'];
			
			$sort_id = $up_obj['id'];
			$sort['sort'] = $obj['sort'];
			$sss = $this -> where('id',$sort_id) -> update($sort);
			
		}
		
		if($type == 'down'){
			$down_obj = $this -> where('sort > ' . $obj['sort']) -> order('sort ASC') -> find();
			if(!$down_obj){
				return ['code' => 0,'msg' => '已降至最低!'];
			}
			$data['sort'] = $down_obj['sort'];
			
			$sort_id = $down_obj['id'];
			$sort['sort'] = $obj['sort'];
			$this -> where('id',$sort_id) -> update($sort);
		}
		
		unset($data['type']);
		$result = $this -> where('id',$id) -> update($data);

		if($result){
			return ['code' => 1,'msg' => '成功!'];
		}else{
			return ['code' => 0,'msg' => '失败!'];
		}
	}
	
    /**
     * model 添加/修改轮播图
     * @param  array $data 传入信息
     */
    public function saveInfo($data){
    	
    	// 判断轮播图标题是否为空
    	if(!$data['title']){
    		return ['status' => 0,'info' => '请填写轮播图标题!'];
    	}
    	
    	// 判断轮播图是否上传
    	if(!$data['link']){
    		return ['status' => 0,'info' => '请上传轮播图!'];
    	}
    	
        if(array_key_exists('id',$data)){
            $id = $data['id'];
            if(!empty($id)){
                $where = true;
            }else{
                $where = false;
                $data['sort'] = time();
            }
        }else{
            $where = false;
            $data['sort'] = time();
        }
             
        $Banner = new Banner;
        $result = $Banner -> allowField(true) -> isUpdate($where) -> save($data);
        if(false === $result){
            return ['status'=>0,'info'=>$AuthGroup->getError()];
        }else{
            return array('status' => 1, 'info' => '保存成功', 'url' => url('index'));
        }
    }
	
    /**
     * model 删除轮播图
     * @param  string $id ID
     */
    public function deleteInfo($id){
    	if(!$id){
    		return ['code' => 0,'msg' => '未获取到要删除的轮播图信息!'];
    	}
    	
    	$del = $this -> where(array('id' => $id)) -> delete();
    	
        if($del){
            return ['code' => 1,'msg' => '删除成功!'];
        }else{
            return ['code' => 0,'msg' => '删除失败,请重试!'];
        }
    }
	
	/**
	 * model 查看修改轮播图信息
	 */
	public function modInfo($id){
		$info = $this -> where('id',$id) -> find();
		return $info;
	}
	
    public function changeSort($datas) {
        if($this->where(array('id'=>$datas['id']))->update(array('sort'=>$datas['sort']))){
            return array('status' => 6,'info' => '更新成功!');
        }else{
            return array('status' => 5,'info' => '更新失败:数据未变动');
        }
    }
}