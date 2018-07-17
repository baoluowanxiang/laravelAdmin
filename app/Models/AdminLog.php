<?php
namespace App\Models;

/**
 * 操作日志
 * @filename   AdminLog.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $table = 'admin_log';
    public $dateFormat = 'U';
    public $timestamps = true;
    protected $guarded = []; //不可以注入
    //public $fillable = []; //仅可注入
    /**
     * 获取日志详情
     * @param $id
     * @return mixed
     */
    public function getInfo($id){
        $info = $this->select(
            't1.id',
            't1.admin_menu_id',
            't1.querystring',
            't1.data',
            't1.ip',
            't1.admin_id',
            't1.created_at',
            't1.primary_id',
            't2.c',
            't2.a',
            't2.name'
        )
            ->where('t1.id',$id)
            ->froM('admin_log as t1')
            ->leftJoin('admin_menu as t2','t2.id','=','t1.admin_menu_id')
            ->first();
        return $info;
    }

}
