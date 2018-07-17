<?php

/**
 * 后台个人页
 * @filename  UserHomeController
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2017-8-16 10:20:11
 * @version   SVN:$Id:$
 */

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Hash;

class AdminHomeController extends Controller
{

    public $M;

    public function __construct()
    {
        parent::__construct();
        $this->M = M('AdminUser');
    }

    public function publicIndex()
    {
        return $this->view();


    }

    /**
     * 个人修改资料
     */
    public function publicInfo()
    {
        $info = $this->login_user;

        if (request('id')) {
            //修改
            if ($info->id) {
                $params = request(['mobile', 'realname','email']); //可以添加或修改的参数

                if(request('setting')){
                    foreach(request('setting') as $k=>$arr){
                        if(is_array($arr)){
                            $params['setting'][$k]= implode(',',$arr);
                        }
                    }
                    $params['setting'] = json_encode($params['setting'], 64 | 256);
                }else{
                    $params['setting'] = '';
                }
                $this->M->where('id', $info->id)->update($params);
            }
            return $this->success();
        } else {
            return $this->view(compact('info'));
        }
    }

    /**
     * 个人修改密码
     */
    public function publicChangePwd()
    {
        $info = $this->M->find($this->login_user->id);

        if (!$info) {
            return $this->error('操作非法');
        }

        if (request('id')) {
            $this->validate(request(), [
                'passwordOld' => 'required',
                'password' => 'required|min:3|max:20|confirmed',
            ], [
                'passwordOld.required' => '旧密码不能为空',
                'password.required' => '密码不能为空',
                'password.confirmed' => '密码与确认密码不一致',
            ]);
            //旧密码不正确
            if (!Hash::check(request('passwordOld'),$info->password)) {
                return $this->error('旧密码不正确');
            }

            $this->M->where('id', $info->id)->update(['password' => bcrypt(request('password'))]);
            return $this->success();
        } else {
            return $this->view(compact('info'));
        }
    }


    public function publicBindWeinxin2(){
        $info = $this->M->where('id',$this->login_user->id)->first();
        if($info->weixin_openid){
            return $this->error('账号已绑定');
        }
        if($info->bind_weinxin_code){
            $code = $info->bind_weinxin_code;
        }else{
            $code = $this->login_user->id.rand(1111,9999);
            $this->M->where('id',$this->login_user->id)->update(['bind_weinxin_code'=>$code]);
        }

        $info->message = '绑定员工/'.$this->login_user->name.'/'.$code;
        return $this->view(compact('info'));
    }



    //绑定微信
    public function publicBindWeixin()
    {

        //已绑过微信
        if ($this->login_user->weixin_openid) {
            return $this->error('此账号已绑定微信','/adminHome/publicInfo');
        }
        if (request('json')) {
            $weixin_info = json_decode(request('json'), true);
            if(!$weixin_info['openid']){
                return $this->error('非法操作');
            }
            $openid = $weixin_info['openid'];
            //查看此 openid 是否被别人绑定过
            $info = M('AdminUser')->where('weixin_openid', $openid)->first();
            if ($info) {
                return $this->error('此微信号已绑定账号','/adminHome/publicInfo');
            }
            //处理头像.....

            $data = [];
            $data['headimgurl'] = $weixin_info['headimgurl'];
            $data['weixin_openid'] = $weixin_info['openid'];
            //修改用户信息
            M('AdminUser')->where('id', $this->login_user->id)->update($data);
            return $this->success('绑定成功','/adminHome/publicInfo');

        } else {
            return $this->view();
        }
    }

    //解绑
    public function publicUnBindWeixin(){
        if (!$this->login_user->weixin_openid) {
            return $this->error('还没绑定','/adminHome/publicInfo');
        }else{
            //修改用户信息
            M('AdminUser')->where('id', $this->login_user->id)->update(['weixin_openid'=>'']);
            return $this->success('解绑成功','/adminHome/publicInfo');
        }
    }


}
