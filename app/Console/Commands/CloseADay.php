<?php

namespace App\Console\Commands;

use App\Repositories\DayRecordRepository;
use Illuminate\Console\Command;

class CloseADay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routine:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closing a day routine';

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
        DayRecordRepository::closeToday();
    }
}
