<?php

/**
 *
 * @filename  Login
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018-7-13 21:55:17
 * @version   SVN:$Id:$
 */

namespace App\Http\Controllers\Admin;

use App\Services\AdminUserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use App\Helper\Jump;
use Illuminate\Support\Facades\Auth;

class IndexController extends BaseController
{

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests,
        Jump;

    public function index()
    {
        if (isset(request()->user('admin')->id)) {
            return redirect('admin/adminHome/publicIndex');
        } else {
            $info['admin_title'] = M('Site')->where('id', 1)->value('admin_title');

            return view('admin.index.index', compact('info'));
        }

    }

    public function login()
    {
        $back_url = request('back_url') ?: 'admin/adminHome/publicIndex';
        $params = request(['name', 'password']);
        //账号是否存在
        $info = M('AdminUser')->where('name', $params['name'])->first();

        if (!$info) {
            return \Redirect::back()->withErrors('此用户不存在');
        }
        if ($info->status == 2) {
            return \Redirect::back()->withErrors('此用户已停用');
        }

        //外部账号
        if (\Auth::guard('admin')->attempt($params)) {
            return redirect($back_url);
        } else {
            return \Redirect::back()->withErrors('账号密码不匹配');
        }

    }

    //登出
    public function logout()
    {
        \Auth::guard('admin')->logout();
        return redirect('admin/index/index');
    }

}
