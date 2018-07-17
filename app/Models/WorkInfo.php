<?php
/**
 * 工作内容记录
 * @filename  WorkInfo.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/06/25 20:03
 */

namespace App\Models;

use App\Jobs\AsyncJob;
use App\Services\MailService;
use \Illuminate\Database\Eloquent\Model;

class WorkInfo extends Model
{
    protected $table = 'work_info';
    public $dateFormat = 'U';
    public $timestamps = true;
    protected $guarded = []; //不可以注入
    public $reminder_status_arr = [1 => '是', 2 => '否'];
    public $is_reminder_arr = [1 => '未提醒', 2 => '已提醒'];

    public function btAdminUser()
    {
        return $this->belongsTo('App\Models\AdminUser', 'admin_id', 'id');
    }


}

