<?php

namespace App\Jobs;

/**
 * 异步任务
 * 添加 \App\Jobs\AsyncJob::dispatch('Es','testc',['duzhenxun',28])->onQueue(config('queue.name.high'));
 * 取出 php artisan queue:listen  --queue=dzx:high --tries=1   --timeout=60
 * 取出 php artisan queue:listen  --queue=dzx:low --tries=1  --memory=1204 --timeout=600
 * @filename   AsyncJob.php
 * @author    Zhenxun Du <5552123@qq.com>
 * @date      2018/6/24 16:48
 */
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AsyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $serviceName; //类名
    public $methodName; //方法
    public $params; //参数

    /**
     * AsyncJob constructor.
     * @param $serviceName 类名
     * @param $methodName 方法
     * @param array $params 参数
     */
    public function __construct($serviceName, $methodName, $params = [])
    {

        $this->serviceName = "\\App\\Services\\". ucfirst($serviceName)."Service";
        $this->methodName = $methodName;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        //检查类是否存在
        if (!class_exists($this->serviceName)) {
            throw new \Exception('找不到文件'.$this->serviceName);
        }
        $service = new \ReflectionClass($this->serviceName);

        //检查方法是否存在
        $method = $service->getMethod($this->methodName);
        if(!$method){
            throw new \Exception('找不到文件'.$this->serviceName.'中的方法:'.$this->methodName);
        }

        //检查方法需要传递参数个数
        $params = $method->getParameters();
        $i=0;
        if(count($params)>0){
            foreach ($params as $param){
                if(!$param->isOptional()){
                    $i++;
                }
            }
            if(count($this->params)<$i){
                throw new \Exception('类:'.$this->serviceName.'中方法:'.$this->methodName.",必传参数量:".count($params));
            }
        }
        //反射,实例化
        $instance  = (new \ReflectionClass($this->serviceName))->newInstanceArgs();

        //执行方法
        $instance->{$this->methodName}(...$this->params);
    }


    public function failed()
    {
        dump('failed');
    }
}
