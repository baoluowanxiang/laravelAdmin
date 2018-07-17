<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class tool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool {fn} {--param=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '工具 php artisan tool test --param=xxx';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $function = $this->argument('fn');
        $param = $this->option('param');
        $this->{$function}($param);
    }

    public function test1(){
        echo 'test1';
    }

    public function test2(){
        echo 'test2';
    }


}
