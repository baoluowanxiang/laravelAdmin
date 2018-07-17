<?php
/**
 * 定时任务
 * @filename   Kernel.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/26 16:48
 */
namespace App\Console;

use App\Jobs\AsyncJob;
use App\Services\ApiUrlService;
use App\Services\WorkInfoService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Tool::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $crontab_lists = M('Crontab')->getCrontab();

        //处理信息
        foreach ($crontab_lists as $k => $v) {
            if (!$v || count(explode(' ', $v)) < 5) {
                unset($crontab_lists[$k]);
            }
        }
        //任务处理
        foreach ($crontab_lists as $k => $v) {
            switch ($k) {
                //工作提醒
                case 'work_reminder':
                    $schedule->call(function () {
                        WorkInfoService::getInstance()->reminder();
                    })->cron($v)->name('work_reminder')->withoutOverlapping();
                    break;
                //更新域名下的 url
                case 'sync_domain_url':
                    $schedule->call(function () {
                       //你的代码
                    })->cron($v)->name('sync_domain_url')->withoutOverlapping();
                    break;
                //更新 URL请求响应
                case 'sync_response_time':
                    $schedule->call(function () {
                        //你的代码
                    })->cron($v)->name('sync_response_time')->withoutOverlapping();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
