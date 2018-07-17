<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crontab extends Model
{

    public $dateFormat = 'U';
    public $timestamps = true;
    protected $table = 'crontab';
    public $status_arr = [1 => '正常', 2 => '禁用'];
    protected $guarded = []; //不可以注入

    //可以注入
    public $fillable = [
        'name',
        'code',
        'crontab',
        'description',
        'status',
        'admin_id',

    ];

    public $messages = [
        'name.required' => '名称不能为空',
        'code.required' => '代码不能为空',
        'crontab.required' => '时间不能为空',
        'status.required' => '状态不能为空',
    ];
    public $rules = [
        'name' => 'required|string|max:50|min:2',
        'code' => 'required',
        'crontab' => 'required',
        'status' => 'required',

    ];


    //获取crontab
    public function getCrontab()
    {
        $crontab = \Cache::get('system:crontab');
        if ($crontab) {
            $crontab = json_decode($crontab, true);
        } else {
            $crontab = $this->select('name', 'code', 'crontab')
                ->where('status', 1)
                ->get();
            $crontab = $crontab->toArray();
            \Cache::set('system:crontab', json_encode($crontab), 60);
        }

        $crontab = array_column($crontab, 'crontab', 'code');

        return $crontab;
    }

    /**
     * 设置crontab
     */
    public function setCrontab()
    {
        $crontab = $this->select('name', 'code', 'crontab')
            ->where('status', 1)
            ->get();
        \Cache::set('system:crontab', json_encode($crontab->toArray()), 1);
    }

    public function btAdminUser()
    {
        return $this->belongsTo('App\Models\AdminUser', 'admin_id', 'id');
    }


}
