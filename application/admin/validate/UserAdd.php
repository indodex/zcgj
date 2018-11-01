<?php
namespace app\admin\validate;
use think\Validate;
class Useradd extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'real_name'	=>	'require',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'account.require'	=>	'请输入用户手机号',
        'password.require'	=>	'请输入登陆密码',
    ];
        
}