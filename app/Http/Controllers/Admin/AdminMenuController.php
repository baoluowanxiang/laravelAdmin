<?php
/**
 * 菜单管理
 * @filename  AdminMenuController.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/23 14:33
 */

namespace App\Http\Controllers\Admin;
class AdminMenuController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->M = M('AdminMenu');
    }

    //首页
    public function index()
    {
        $where = [];
        if (request('status')) {
            $where[] = ['status', request('status')];
        }
        if (request('write_log')) {
            $where[] = ['write_log', request('write_log')];
        }
        if (request('name')) {
            $where[] = ['name', 'like', '%' . request('name') . '%'];
        }
        $lists = $this->M->getMenuList($where);

        foreach ($lists as $k => $v) {
            $lists[$k]['name'] = $v['level'] == 0 ? $v['name'] : '├─' . $v['name'];
            $lists[$k]['name'] = str_repeat("│        ", $v['level']) . $lists[$k]['name'];
        }
        return $this->view(compact('lists'));
    }

    //详情
    public function info()
    {
        $info = $this->M->find(request('id'));
        $menus = $this->M->selectMenu();
        return $this->view(compact('info', 'menus'));
    }

    //添加
    public function add()
    {
        $this->validate(request(), $this->M->rules, $this->M->messages);
        $params = request($this->M->fillable); //可以添加或修改的参数

        if ($params['parentid'] === null) {
            $params['parentid'] = 0;
        }

        $res = $this->M->create($params);
        if ($res->a == 'index') {
            $params['parentid'] = $res->id;
            $params['icon'] = '';
            $params['status'] = 2;

            $params['name'] = '详情';
            $params['a'] = 'info';
            $this->M->create($params);

            $params['name'] = '添加';
            $params['a'] = 'add';
            $this->M->create($params);

            $params['name'] = '修改';
            $params['a'] = 'edit';
            $this->M->create($params);

            $params['name'] = '删除';
            $params['a'] = 'del';
            $this->M->create($params);


        }
        return $this->success('添加成功', '/'. $this->c .'/index');
    }
    //修改
    public function edit()
    {
        $this->validate(request(), $this->M->rules, $this->M->messages);
        $params = request($this->M->fillable); //可以添加或修改的参数
        if ($params['parentid'] === null) {
            $params['parentid'] = 0;
        }
        $rs = $this->M->where('id', request('id'))->update($params);
        if ($rs) {
            return $this->success('修改成功', '/' . $this->c . '/index');
        } else {
            return $this->error();
        }
    }

    //删除
    public function del()
    {
        $id = request('id');
        $this->M->where('id', $id)->delete();
        $this->M->where('parentid', $id)->delete();
        return $this->success();
    }

    //排序
    public function setListorder()
    {
        $data = request('listorder');
        foreach ($data as $k => $v) {
            $this->M->where('id', $k)->update(['listorder' => $v]);
        }
        return $this->success();
    }
}