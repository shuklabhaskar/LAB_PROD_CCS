<?php

namespace App\Console\Commands;

use App\Http\Controllers\Modules\Api\Paytm\OlAcqTxn;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class OfflineSaleCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline_sale:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        (new OlAcqTxn)->index();
    }
}
