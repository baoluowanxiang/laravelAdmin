<?php

namespace App\Console\Commands;

use App\Jobs\AsyncJob;
use App\Jobs\DomainUrl;
use App\Services\ApiDomainService;
use App\Services\ApiUrlService;
use App\Services\EsService;
use App\Services\NoticeService;
use App\Services\WorkInfoService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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




    }
}
