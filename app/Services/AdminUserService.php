<?php
/**
 * AdminUserService
 * @filename  AdminUserService.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/7/8 16:18
 */

namespace App\Services;


use App\Models\AdminGroupUser;
use App\Models\AdminUser;

class AdminUserService extends Service
{


    /**
     * 获取用户
     * @param type $where
     * @return type
     */
    public function adminUserLists($where)
    {
        $res = AdminUser::where($where)->orderBy('id', 'desc')->paginate(20);

        foreach ($res as $k => $v) {
            if ($tmp = $this->getAdminGroupUser($v['id'])) {

                $res[$k]['groups'] = implode(',', array_column($tmp, 'name'));
            }
        }
        return $res;
    }


    /**
     * 根据类型id获取信息
     */
    public function getAdminGroupUser($id, $type = 'admin_id')
    {

        $where = [];
        if ($id) {
            if ($type == 'admin_id') {
                $where[] = ['t1.admin_id', $id];
            } else {
                $where[] = ['t1.group_id', $id];
            }
        }

        $res = AdminGroupUser::select('t2.id', 't2.name')
            ->from('admin_group_user as t1')
            ->leftJoin('admin_group as t2', 't1.group_id', '=', 't2.id')
            ->where($where)
            ->get()
            ->toArray();

        return $res;
    }

    /**
     *保存分组
     * @param type int $admin_id
     * @param type Array $group_ids
     */
    public function saveAdminGroupUser($admin_id, $group_ids)
    {
        //删除原账号对应分组
        $AdminGroupUser = M('AdminGroupUser');
        $AdminGroupUser->where('admin_id', $admin_id)->delete();
        //添加新账号对应分组
        foreach ($group_ids as $group_id) {
            $AdminGroupUser->create(['admin_id' => $admin_id, 'group_id' => $group_id]);
        }
    }
}
