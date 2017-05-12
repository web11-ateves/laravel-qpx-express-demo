<?php

namespace App\Console\Commands;

use App\Mail\PriceAlert;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPriceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flights:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send price alerts for saved flights';

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
        $this->info('Started checking flights prices variation...');

        setlocale(LC_MONETARY, 'pt_BR');

        $trips = Trip::where('end_date', '>', Carbon::today())
            ->orWhere('departure_date', '<', Carbon::today()->addDays(10))
            ->get();
        foreach($trips as $trip) {
            $trip_options = $trip->trip_options;
            foreach($trip_options as $trip_option) {
                $prices = $trip_option->prices;

                $last_price = $prices->pop();
                $last_price_total_brl = $last_price->price_total_brl;
                $previous_price = $prices->pop();
                if ($previous_price && $last_price_total_brl < $previous_price->price_total_brl ){
                    $dif = $last_price_total_brl - $previous_price->price_total_brl;
                    $value = money_format("%n", $last_price->price_total_brl);
                    $message = "$value ($dif)";
                    $title = $trip->description;
                    //Mail::to($trip->user)->send(new PriceAlert($message, $title));
                    $this->line($message);
                }
            }
        }

    }

}
