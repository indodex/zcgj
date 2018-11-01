<?php
namespace  app\admin\model;

use app\common\model\Base;
use think\Request;
use think\db;
use think\Validate;

class User extends Base
{

    /**
     * 自动加密
     */
    public function setPasswordAttr($value){
        return encrypt($value);
    }
    public function setPaymentPasswordAttr($value){
        return encrypt($value);
    }
    
    const PAGE_LIMIT = '10';//用户表分页限制
    const PAGE_SHOW = '10';//显示分页菜单数量


    /**
     * model 获取用户列表
     * @param  array $map 查询条件
     * @param  string $p  页码
     * @return array      返回列表
     */
    public function userList($map, $p){
        $request= Request::instance();
        
        $list = $this -> where($map) -> field('id,account,invitation_code,parent_id,create_time,status,is_shop') -> order('is_shop DESC,id DESC') -> page($p, self::PAGE_LIMIT) -> select() -> toArray();
        $count = $this -> where($map) -> count();
        foreach ($list as $k => $v) {
        	// 获取商城状态
        	switch($v['is_shop']){
        		case 1:
        			$list[$k]['shop_text'] = '否';
        			break;
        		case 2:
        			$list[$k]['shop_text'] = '是';
        			break;
        	}
        	
        	// 获取用户状态
        	$where['type'] = 'common_state';
        	$where['value'] = $v['status'];
        	$list[$k]['state_type'] = Db::name('dict') -> where($where) -> value('key');
        	if($v['status'] == 1){
        		$list[$k]['button'] = 'state_green';
        	}else{
        		$list[$k]['button'] = 'state_red';
        	}
            
            // 获取上级用户名称
            $list[$k]['parent_account'] = $this -> where('id',$v['parent_id']) -> value('account');
            
            // 查看申请
            $apply = Db::name('user_shop') -> where('uid',$v['id']) -> find();
            if(!$apply){
            	$list[$k]['shop_apply'] = 0;
            }else{
            	switch($apply['examine']){
            		case 1:
            			$list[$k]['shop_apply'] = 1;
            			$list[$k]['apply_color'] = 'btn-info';
            			break;
            		case 2:
            			$list[$k]['shop_apply'] = 2;
            			$list[$k]['apply_color'] = 'btn-success';
            			break;
            		case 3:
            			$list[$k]['shop_apply'] = 3;
            			$list[$k]['apply_color'] = 'btn-danger';
            			break;
            	}
            }
            
            // 获取审核状态名
            $dict_where['type'] = 'identity_status';
            $dict_where['value'] = $list[$k]['shop_apply'];
            switch($list[$k]['shop_apply']){
            	case 1:
            		$list[$k]['shop_apply_text'] = Db::name('dict') -> where($dict_where) -> value('key');
            		break;
            	case 2:
            		$list[$k]['shop_apply_text'] = Db::name('dict') -> where($dict_where) -> value('key');
            		break;
            	case 3:
            		$list[$k]['shop_apply_text'] = Db::name('dict') -> where($dict_where) -> value('key');
            		break;
            }
        }
        
        $return['count'] = $count;
        $return['list'] = $list;
        $return['page'] = boot_page($return['count'], self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request -> action());
       
        return $return;
    }
    
    /**
     * model 修改用户状态
     */
    public function modState($data){
    	$id = $data['id'];
    	if(!$id){
    		return ['code' => 0,'msg' => '未获取到用户信息!'];
    	}
    	$status = $data['state'];
    	if(!$status){
    		return ['code' => 0,'msg' => '未获取到用户状态!'];
    	}
    	
    	if($status == 1){
    		$mod['status'] = 2;
    	}else{
    		$mod['status'] = 1;
    	}
    	
    	$result = $this -> where('id',$id) -> update($mod);
    	if($result){
    		return ['code' => 1,'msg' => '修改成功!'];
    	}else{
    		return ['code' => 0,'msg' => '修改失败!'];
    	}
    }
		
	/**
     * model 删除用户
     * @param  string $id ID
     */
    public function deleteInfo($id){
        
        if(!$id){
        	return ['code' => 0,'msg' => '未获取到要删除的用户信息!'];
        }
		
		$condition = 0;
		Db::startTrans();
		try{
			// 删除用户表基本表中的数据
			$this -> where(array('id' => $id)) -> delete();
			
			// 删除用户券数量表中的数据
			Db::name('user_vou') -> where('uid',$id) -> delete();
			
			// 删除用户奖金表中的数据
			Db::name('user_bouns') -> where('uid',$id) -> delete();
			
			// 判断是否有店铺,如果存在店铺则删除店铺信息
			$have_shop = Db::name('user_shop') -> where('uid',$id) -> find();
			if($have_shop){
				Db::name('user_shop') -> where('uid',$id) -> delete();
			}
			
			$condition = 1;
			Db::commit();
		}catch(\Exception $e){
			Db::rollback();
		}
        
        if($condition = 1){
            return ['code' => 1,'msg' => '删除成功!'];
        }else{
            return ['code' => 0,'msg' => '删除失败,请重试!'];
        }
    }
	
    /**
     * model 添加用户基本信息
     * @param  array $data 传入信息
     */
    public function addUser($data){
    	// 调用二维码生成器 qrcode
    	vendor("phpqrcode.phpqrcode");
    	
    	$exist_account = $this -> where('account',$data['account']) -> find();
    	if($exist_account){
    		return ['status' => 0,'info' => '此手机号已被注册!'];
    	}
    	$exist_identity = $this -> where('identity',$data['identity']) -> find();
    	if($exist_identity){
    		return ['status' => 0,'info' => '此身份证号已被注册!'];
    	}
    	
        $data['invitation_code'] = make_coupon_card();
        if($data['parent']){
        	$data['parent_id'] = $this -> where('invitation_code',$data['parent']) -> value('id');
        }
        
        $condition = 0;
        
        // 添加用户基本信息表
    	$User = new User;
    	$result = $User -> allowField(true) -> validate('user_add') -> save($data);
    	$user_id = $this -> getLastInsID();
    	
    	// 添加用户券表
    	if($result){
    		// 在用户券表中添加信息
    		$voucher = Db::name('voucher') -> field('id') -> select();
	    	foreach($voucher as $k => $v){
	    		$vou_in['uid'] = $user_id;
	    		$vou_in['vid'] = $v['id'];
	    		Db::name('user_vou') -> insert($vou_in);
	    	}
	    	
	    	// 在用户奖金中添加信息
			$dict_where['type'] = 'bouns_type';
			$bouns = Db::name('dict') -> where($dict_where) -> field('value') -> select();
			foreach($bouns as $k => $v){
				$bouns_in['uid'] = $user_id;
				$bouns_in['bouns_type'] = $v['value'];
				Db::name('user_bouns') -> insert($bouns_in);
			}
	    	
	    	$condition = 1;
    	}
        
        if($condition === 1){
        	return array('status' => 1, 'info' => '添加成功!', 'url' => url('index'));
        }else{
            return ['status' => 0,'info' => $User -> getError()];
        }
    }
	
	/**
	 * model 查看用户详情
	 */
	public function userDetail($id){
		
		$info = $this -> where('id',$id) -> field('id,account,invitation_code,real_name,identity,tel,parent_id,create_time,update_time,status,wechat_accout,alipay_accout') -> find();
		$info['parent_account'] = $this -> where('id',$info['parent_id']) -> value('account');
		return $info;
	}
	
	/**
     * model 重置密码
     * @param  array $data 传入数组
     */
    public function editPwd($data){
    	if(!$data['id']){
    		return ['code' => 0,'msg' => '未获取到用户信息!'];
    	}
    	
    	if(!$data['type']){
    		return ['code' => 0,'msg' => '未获取到密码状态!'];
    	}
    	
		switch($data['type']){
			case 'password':
				$mod['password'] = encrypt('12345678');
				break;
			case 'payment_password':
				$mod['payment_password'] = encrypt('12345678');
				break;
		}
        $mod['update_time'] = time();
        $result = $this -> where(array('id' => $data['id'])) -> update($mod);
        if($result){
            return array('code' => 1, 'msg' => '重置密码成功!');
        }else{
            return array('code' => 0, 'msg' => '重置密码失败!');
        }
    }
	
	/**
	 * model 修改用户详情
	 */
	public function modDetail($data){
		$User = new User;
		$info = $User -> allowField(true) -> validate('user_detail') -> isUpdate(true) -> save($data);
		if($info){
			return ['status' => 1,'info' => '修改成功!'];
		}else{
			return ['status' => 0,'info' => $User -> getError()];
		}
	}
	
	/**
	 * model 查询用户财务
	 */
	public function finance($id){
		// 用户券
		$vou = Db::name('user_vou') -> where('uid',$id) -> order('vid ASC') -> select();
		foreach($vou as $k => $v){
			$voucher = Db::name('voucher') -> where('id',$v['vid']) -> value('name');
			$vou[$k]['name'] = $voucher;
		}
		// 用户奖金
		$bouns = Db::name('user_bouns') -> where('uid',$id) -> order('bouns_type ASC') -> select();
		foreach($bouns as $k => $v){
			$dict_where['type'] = 'bouns_type';
			$dict_where['value'] = $v['bouns_type'];
			$bouns[$k]['name'] = Db::name('dict') -> where($dict_where) -> value('key');
		}
		
		$finance['vou'] = $vou;
		$finance['bouns'] = $bouns;
		return $finance;
	}
	
	/**
	 * model 修改用户券
	 */
	public function modVoucher($data){
		$uid = $data['uid'];
		if(!$uid){
			return ['status' => 0,'info' => '未获取用户信息!'];
		}
		$where['uid'] = $uid;
		unset($data['uid']);
		
		foreach($data as $k => $v){
			$where['vid'] = $k;
			// 判断数量
			if(!$v){
				$mod['number'] = 0;
			}else{
				if(is_numeric($v)){
					$mod['number'] = $v;
				}else{
					return ['status' => 0,'info' => '只能输入数字!'];
				}
			}
			Db::name('user_vou') -> where($where) -> update($mod);
		}
		return ['status' => 1,'info' => '修改成功!'];
	}
	
	/**
	 * model 修改用户券
	 */
	public function modBouns($data){
		$uid = $data['uid'];
		if(!$uid){
			return ['status' => 0,'info' => '未获取用户信息!'];
		}
		$where['uid'] = $uid;
		unset($data['uid']);
		
		foreach($data as $k => $v){
			$where['bouns_type'] = $k;
			// 判断数量
			if(!$v){
				$mod['bouns_number'] = 0;
			}else{
				if(is_numeric($v)){
					$mod['bouns_number'] = $v;
				}else{
					return ['status' => 0,'info' => '只能输入数字!'];
				}
			}
			Db::name('user_bouns') -> where($where) -> update($mod);
		}
		return ['status' => 1,'info' => '修改成功!'];
	}
	
	/**
	 * model 商城审核
	 */
	public function shopInfo($id){
		$shop = Db::name('user_shop') -> where('uid',$id) -> find();
		return $shop;
	}
	
	/**
	 * model 执行商城审核
	 */
	public function shopExamine($data){
		if(!$data['id']){
			return ['code' => 0,'msg' => '未获取审核信息!'];
		}
		if(!$data['uid']){
			return ['code' => 0,'msg' => '未获取用户信息!'];
		}
		if(!$data['examine']){
			return ['code' => 0,'msg' => '未获取审核状态!'];
		}
		
		Db::startTrans();
		$condition = 0;
		try{
			
			// 修改用户店铺审核表
			$where['id'] = $data['id'];
			$where['uid'] = $data['uid'];
			$mod['examine'] = $data['examine'];
			Db::name('user_shop') -> where($where) -> update($mod);
			
			// 审核通过
			if($data['examine'] == 2){
				// 修改用户表状态
				Db::name('user') -> where('id',$data['uid']) -> update(['is_shop' => 2]);
				
				// 为用户添加后台 店铺管理员
				$user_where['id'] = $data['uid'];
				$user_shop = Db::name('user') -> where($user_where) -> find();	// 获取用户信息
				$in_admin['username'] = $user_shop['account'];
				$in_admin['password'] = $user_shop['password'];
				$in_admin['user_type'] = 2;
				$in_admin['description'] = '店铺管理员';
				$in_admin['status'] = 1;
				$in_admin['create_time'] = time();
				$in_admin['uid'] = $data['uid'];
				$admin_id = Db::name('admin') -> insertGetId($in_admin);
				
				// 为新添加的 店铺管理员 添加权限
				$in_auth_group_access['uid'] = $admin_id;
				$in_auth_group_access['group_id'] = 2;
				Db::name('auth_group_access') -> insert($in_auth_group_access);
			}
			
			$condition = 1;
			Db::commit();
		}catch(\exception $e){
			Db::rollback();
		}
		
		if($condition = 1){
			return ['code' => 1,'msg' => '执行成功!','url' => url('index')];
		}else{
			return ['code' => 0,'msg' => '执行失败!'];
		}
	}
}
