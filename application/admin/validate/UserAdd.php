<?php
namespace app\admin\validate;
use think\Validate;
class Useradd extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [    	'account'	=>	'require|number|max:11|min:11',    	'password'	=>	'require|max:16|min:8',    	'repwd'		=>	'require|confirm:password',
        'real_name'	=>	'require',        'identity'	=>	'require',        'status'	=>	'require',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'account.require'	=>	'请输入用户手机号',        'account.number'	=>	'手机号需为位数字',        'account.max'		=>	'手机号最大长度为11位',        'account.min'		=>	'手机号需为11位',        
        'password.require'	=>	'请输入登陆密码',        'password.max'		=>	'登陆密码最大长度为16位',        'password.min'		=>	'登陆密码不能小于8位',                'repwd.require'		=>	'请输入确认登陆密码',        'repwd.confirm'		=>	'两次输入的登陆密码不相同',                'real_name.require'	=>	'请输入真实姓名',        'identity'	=>	'请填写证件号码',        'status.require'	=>	'请设置用户状态',
    ];
        
}
