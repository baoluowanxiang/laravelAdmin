<?php

namespace App\Models;

/**
 * 用户组与用户
 * @filename   AdminGroupUser.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use Illuminate\Database\Eloquent\Model;

class AdminGroupUser extends Model
{

    protected $table = 'admin_group_user';
    public $dateFormat = 'U';
    public $timestamps = false;
    public $fillable = ['admin_id', 'group_id'];
    public $rules = [
        'admin_id' => 'required',
        'group_id' => 'required',
    ];

    public function btAdminUser()
    {
        return $this->belongsTo('App\Models\AdminUser', 'admin_id', 'id');
    }

    public function btAdminGroup()
    {
        return $this->belongsTo('App\Models\AdminGroup', 'group_id', 'id');
    }

}
