<?php
namespace App\Models;

/**
 * 用户组
 * @filename   AdminGroup.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use Illuminate\Database\Eloquent\Model;

class AdminGroup extends Model
{
    protected $table = 'admin_group';
    public $dateFormat = 'U';
    public $timestamps = true;
    protected $guarded = []; //不可以注入
    public $fillable = ['name', 'description','menus'];
    public $messages = [
        'name.required' => '名不能为空',
    ];
    public $rules = [
        'name' => 'required|string|max:100|min:2'
    ];


}
