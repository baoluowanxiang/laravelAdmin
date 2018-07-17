<?php

/**
 * 管理员
 * @filename  AdminGroupController
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018-6-24 18:20:12
 * @version   SVN:$Id:$
 */

namespace App\Http\Controllers\Admin;

use App\Services\AdminUserService;
use App\Services\ApiDomainService;

class AdminUserController extends Controller
{

    public $M;

    public function __construct()
    {
        parent::__construct();
        $this->M = M('AdminUser');

    }


    //列表
    public function index()
    {

        $where = [];
        if (request('name')) {
            $where[] = ['name', 'like', '%' . request('name') . '%'];
        }
        if (request('type')) {
            $where[] = ['type', request('type')];
        }
        if (request('status')) {
            $where[] = ['status', request('status')];
        }

        $lists = AdminUserService::getInstance()->adminUserLists($where);

        return $this->view(compact('lists'));

    }

    //详情
    public function info()
    {
        $info = $this->M->find(request('id'));
        if ($info) {
            $groups = AdminUserService::getInstance()->getAdminGroupUser($info->id, 'admin_id');
            if ($groups) {
                $info['group_ids'] = implode(',', array_column($groups, 'id'));
            }
            $info->setting = json_decode($info->setting, true);

        }

        return $this->view(compact('info'));

    }

    //添加
    public function add()
    {
        if ($this->storage()) {
            return $this->success('添加成功', '/' . $this->c . '/index');
        } else {
            return $this->error();
        }
    }

    //修改
    public function edit()
    {
        if ($this->storage()) {
            return $this->success('修改成功', '/' . $this->c . '/index');
        } else {
            return $this->error();
        }
    }

    /*
     * 存储
     */
    private function storage()
    {
        $params = request($this->M->fillable);

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


        if (request('id')) {
            //修改
            $admin_id = request('id');
            $rs = $this->M->where('id', $admin_id)->update($params);

        } else {
            //添加
            //等有空整理个统一验证代码 $this->validate(request(), $this->M->rules, $this->M->messages);
            $params['password'] = bcrypt($params['password']);
            $rs = $this->M->create($params);
            $admin_id = $rs->id;
        }

        //用户分组
        if (request('group_id')) {
            $group_ids = request('group_id');
            AdminUserService::getInstance()->saveAdminGroupUser($admin_id, $group_ids);
        } else {
            M('AdminGroupUser')->del($admin_id);
        }

        return $rs;
    }


    //修改密码
    public function changePwd()
    {
        $info = $this->M->find(request('id'));
        if (!$info) {
            return $this->error('非法请求');
        }
        if (request('password')) {
            $this->validate(request(),
                [
                    'password' => 'required|min:3|max:20|confirmed'
                ],
                [
                    'password.required' => '密码不能为空',
                    'password.confirmed' => '密码与确认密码不一致'
                ]
            );

            $this->M->where('id', request('id'))->update(['password' => bcrypt(request('password'))]);

            return $this->success('修改成功', '/' . $this->c . '/index');
        } else {
            return $this->view(compact('info'));
        }
    }



}
