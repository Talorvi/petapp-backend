<?php

namespace App\Console\Commands;

use App\Models\Offer;
use Illuminate\Console\Command;

class CreateOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:create {count=100000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a specified number of offers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $count = $this->argument('count');

        $this->info("Creating {$count} offers...");

        Offer::factory()->count($count)->create();

        $this->info("{$count} offers created successfully.");

        return 0;
    }

}
