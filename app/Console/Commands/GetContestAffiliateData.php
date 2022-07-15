<?php

namespace App\Console\Commands;

use App\Http\Controllers\LeaderboardController;
use Illuminate\Console\Command;

class GetContestAffiliateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contest:get_contest_affiliate_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Contest Affiliate Data';

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
     * @return int
     */
    public function handle()
    {
        LeaderboardController::getContestAffiliateData();
    }
}
