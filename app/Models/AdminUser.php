<?php

namespace App\Models;

/**
 * 用户
 * @filename   AdminUser.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_user';
    public $dateFormat = 'U';
    public $timestamps = true;
    protected $guarded = []; //不可以注入
    public $fillable = ['name', 'mobile', 'password', 'realname', 'status', 'email','setting'];
    public $status_arr = [1 => '正常', 2 => '禁用'];
    public $type_arr = [1 => '外部账号', 2 => '域账号'];

    public $messages = [
        'name.required' => '名不能为空',
    ];

    public $rules = [
        'name' => 'required|string|unique:admin_user|max:100|min:2',
    ];





}
