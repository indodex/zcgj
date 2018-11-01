<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\Request;
use think\Session;
use think\Db;

class Publics extends Base
{
	
    /**
     * 用户登录
     */
    public function login()
    {
        if (Request::instance()->isPost()) {
            return model('User')->userLogin(input('post.'));
        } else {
            return $this->fetch();
        }
    }
	
	/**
	 * 判断验证码
	 */
	public function check_verify($verify){
		if(!captcha_check($verify)){
			return json(['code' => 0]);
		}else{
			return json(['code' => 1]);
		}
	}
	
    /**
     * 用户注册
     */
    public function userReg()
    {
    	// 判断是否是通过邀请过来注册
    	$code = input('code');
    	if($code){
    		$this -> assign('code',$code);
    	}
    	
        if (Request::instance()->isPost()) {
            return model('User')->userReg(input('post.'));
        } else {
            return $this->fetch();
        }
    }
	
	/**
	 * 获取手机验证码
	 */
	public function get_verify($account){
		return json(model('User') -> getVerify($account));
	}
	
    /**
     * 忘记密码
     */
    public function forgetPwd()
    {
        if (Request::instance()->isPost()) {
            $result = model('User')->forgetPwd(input('post.')); 
            Session::delete('account');
            return $result;
        } else {
            return $this -> fetch();
        }
    }
	
    /**
     * 用户登出
     */
    function logout()
    {
        session('uid', null);
        session('account',null);
        $this -> redirect('Publics/login', '您已经安全退出！');
    }

    //上传图片
    public function upload(){
        $type = trim(input('type'));
        if(!$type){
            $r = ['status'=>0,'info'=>'参数不正确'];;
        }else{
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('file');
            
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/'.$type);
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    $link = '/upload/'.$type.'/'.$info->getSaveName();
                    $r = ['status'=>1,'info'=>'成功','msg'=>$link];
                }else{
                    // 上传失败获取错误信息
                    $r = ['status'=>0,'info'=>$file->getError()];;
                }
            }else{
                $r = ['status'=>0,'info'=>'未上传'];
            }
        }
        return json($r);
    }
    
}