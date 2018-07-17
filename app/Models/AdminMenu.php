<?php

namespace App\Models;

/**
 * 菜单
 * @filename   AdminMenu.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use App\Services\ApiDomainService;
use App\Services\ApiUrlService;
use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    protected $table = 'admin_menu';
    public $dateFormat = 'U';
    public $timestamps = true;
    //protected $guarded = []; //不可以注入
    public $status_arr = ['1' => '显示', '2' => '不显示'];
    public $write_log_arr = ['1' => '记录', '2' => '不记录'];
    public $fillable = ['name', 'parentid', 'icon', 'c', 'a', 'data', 'status', 'listorder', 'write_log']; //可以注入
    public $messages = [
        'name.required' => '名称不能为空',
        'c.required' => '文件不能为空',
        'a.required' => '方法不能为空',
        'status.required' => '状态不能为空'
    ];
    public $rules = [
        'name' => 'required|string|max:100|min:2',
        'c' => 'required|string',
        'a' => 'required|string',
        'status' => 'required|int',
    ];


    /**
     * 所有操作菜单
     */
    public static function getMenuList($where = [])
    {
        $res = static::where($where)->orderBy('listorder', 'asc')->get()->toArray();
        $res = node_tree($res);
        return $res;
    }

    //下拉框菜单选择
    public function selectMenu()
    {
        $tmpArr = $this->getMenuList();
        $data = array();
        foreach ($tmpArr as $k => $v) {
            $name = $v['level'] == 0 ? '<b>' . $v['name'] . '</b>' : '├─' . $v['name'];
            $name = str_repeat("│        ", $v['level']) . $name;
            $data[$v['id']] = $name;
        }
        return $data;
    }

    /**
     * 我的菜单
     * @param int $status 状态 1 只查显示,0所有
     * @return array|bool
     */
    public function myMenu($status = 1)
    {
        $where = array();
        if ($status == 1) {
            $where[] = ['status', '=', 1];
        }
        $loginUser = request()->user('admin');
        $admin_id = $loginUser->id;

        //查看此人是否超级管理员组,如果是返回所有权限
        if ($loginUser->is_super == 1) {
            //超级管理员
            $menus = static::where($where)->orderBy('listorder', 'asc')->get()->toArray();
        } else {
            //查出用户所在组Id拥有的menus
            //select menus from erp_admin_group_access t1 left join erp_admin_group t2 on t1.group_id=t2.id where t1.admin_id=11
            $menu_arr = M('AdminGroupUser')
                ->froM('admin_group_user as t1')
                ->leftJoin('admin_group as t2', 't1.group_id', '=', 't2.id')
                ->where('t1.admin_id', $admin_id)
                ->pluck('menus')->toArray();
            $menu_ids = array();
            foreach ($menu_arr as $k => $v) {
                if ($v) {
                    $menu_ids = array_unique(array_merge($menu_ids, explode(',', $v)));
                }
            }

            //菜单大于0查出
            if (count($menu_ids) > 0) {
                $menus = static::where($where)->wherein('id', $menu_ids)->orderBy('listorder', 'asc')->get()->toArray();
            } else {
                return false;
            }

        }

        return $menus;
    }

    /**我的菜单返回html
     * @return string
     */
    public function myMenuHtml()
    {
        $menuTree = list_to_tree($this->MyMenu(1));

        $html = '<ul class="nav nav-list">';
        $html .= $this->menu_tree($menuTree);
        $html .= "
                </ul>";
        return $html;
    }

    private function menu_tree($tree)
    {

        $html = '';
        if (is_array($tree)) {
            foreach ($tree as $val) {
                if (isset($val["name"])) {
                    $title = $val["name"];
                    $url = '/' . $val['m'] . '/' . $val['c'] . '/' . $val['a'];
                    $val['data'] ? $url .= '?' . $val['data'] : '';
                    if (empty($val["id"])) {
                        $id = $val["name"];
                    } else {
                        $id = $val["id"];
                    }
                    if (empty($val['icon'])) {
                        $icon = "fa-caret-right";
                    } else {
                        $icon = $val['icon'];
                    }
                    $pathinfo = explode('?', $_SERVER['REQUEST_URI'])[0];

                    if ($url == $pathinfo) {
                        $active = 'active ';
                    } else {
                        $active = '';
                    }

                    if (isset($val['_child'])) {
                        if ($val['id'] == 193) {
                            $open = 'open';
                        } else {
                            $open = '';
                        }
                        $html .= ' 
                            <li class="' . $open . '">
                            <a href="' . $url . '" class="dropdown-toggle">
                                <i class="menu-icon fa ' . $icon . '"></i>
                                <span class="menu-text"> ' . $title . ' </span>
                                <b class="arrow fa fa-angle-down"></b>
                            </a>
                            <b class="arrow"></b>
                            <ul class="submenu">
                            ';
                        $html .= $this->menu_tree($val['_child']);
                        $html .= '              
                            </ul>
                        </li>
                        ';
                    } else {
                        $html .= '
                    <li class = "' . $active . '">
                    <a href = "' . $url . '">
                    <i class = "menu-icon fa ' . $icon . '"></i>
                    <span class = "menu-text"> ' . $title . ' </span>
                    </a>
                    <b class = "arrow"></b>
                    </li>
                    ';
                    }
                }
            }
        }
        return $html;
    }


}