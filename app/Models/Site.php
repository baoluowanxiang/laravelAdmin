<?php

namespace App\Models;

/**
 * 站点设置
 * @filename   Site.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'site';
    public $dateFormat = 'U';
    public $timestamps = true;
    protected $guarded = []; //不可以注入
    //public $fillable = []; //仅可注入
    public $alert_type_arr = ['weixin' => '微信', 'email' => '邮件'];//报警类型

    //获取站点信息
    public function getInfo()
    {
        $info = \Cache::get('system:config');
        if ($info) {
            $info = json_decode($info);
        } else {
            $info = $this->first();
            \Cache::set('system:config', json_encode($info->toArray()), 60);
        }
        return $info;
    }

    /**
     * 获取设置的邮箱
     * @param string $key
     * @return array|bool|\Illuminate\Config\Repository|mixed
     */
    public function getSetting($key = '')
    {
        $setting = json_decode($this->getInfo()->setting, true);
        $data = [];
        foreach ($setting as $k => $v) {
            $tmp = explode("\n", $v);
            foreach ($tmp as $kk => $vv) {
                $data[$k][$kk] = trim($vv);
            }
        }
        if ($key && isset($data[$key])) {
            $data = $data[$key];
        }
        return $data;
    }
}
