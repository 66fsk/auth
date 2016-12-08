<?php

namespace zgxv\auth;

use think\helper\Hash;
use think\Session;
use think\Validate;
use zgxv\auth\model\Member;

class Acl{

    protected static $config = [
        'login_template' => 'login'
    ];

    /**
     * @desc 登录页面
     * @return mixed
     */
    public static function login(){
        //是否已登录
        if(self::isLogin()){
            redirect('/index')->send();
        }

        $assign = [
            'error_message' => Session::pull('error_message')
        ];

        return view(self::$config['login_template'],$assign);
    }

    /**
     * @desc 是否登录
     * @return bool|mixed
     */
    public static function isLogin(){

        if(Session::has('user')){
            return json_decode(Session::get('user'),true);
        }else{
            return false;
        }
    }

    /**
     * @desc 登录
     * @param Request $request
     */
    public static function doLogin(&$request){
        $data = [
            'name' => $request->post('name'),
            'password' => $request->post('password'),
            'captcha' => $request->post('captcha')
        ];

        $rule = [
            'name' => 'require|alphaDash|max:20|min:2',
            'password' => 'require|alphaNum|max:20|min:6',
            'captcha' => 'require|captcha'
        ];

        //验证规则
        $validate = new Validate($rule);
        $validateRes = $validate->check($data);

        if(true !== $validateRes){
            Session::set('error_message', $validate->getError());
            redirect('/login')->send();
        }

        $user = Member::get(['name'=>$data['name']]);
        //验证用户名和密码
        if(!Hash::check($data['password'],$user->password)){
            Session::set('error_message', '用户名或密码错误');
            redirect('/login')->send();
        }

        //登录session
        Session::set('user',json_encode($user));

        redirect('/index')->send();
    }

    /**
     * @desc 退出登录
     */
    public static function logout(){
        Session::clear();
        redirect('/login')->send();
    }
}