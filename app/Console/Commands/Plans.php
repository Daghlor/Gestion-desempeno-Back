<?php

namespace App\Console\Commands;

use App\Models\ObjectivesIndividual;
use App\Models\ObjectivesStrategics;
use App\Models\PerformancePlans;
use App\Models\Tracing;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;

class Plans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for inactive plans';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $plans = PerformancePlans::where('dateEnd', '<', Date('Y-m-d'))->get();

        for ($i=0; $i < count($plans); $i++) { 
           ObjectivesStrategics::where('plans_id', $plans[$i]['id'])->delete();
           ObjectivesIndividual::where('plans_id', $plans[$i]['id'])->delete();
           Tracing::where('plans_id', $plans[$i]['id'])->delete();
        }

        PerformancePlans::where('dateEnd', '<', Date('Y-m-d'))->delete();

        // $this->info('Comando ejecutado correctamente.');
        // return Command::SUCCESS;
    }
}
