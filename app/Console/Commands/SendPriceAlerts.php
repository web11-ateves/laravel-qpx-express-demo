<?php

namespace App\Console\Commands;

use App\Mail\PriceAlert;
use App\Models\MinimumPrice;
use App\Models\Trip;
use App\Models\TripOption;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

        $trips = Trip::where('end_date', '>', Carbon::today())
            ->orWhere('departure_date', '<', Carbon::today()->addDays(5))
            ->get();

        foreach($trips as $trip) {

            $this->line("Trip #" . $trip->id);

            $optionsByDate = $trip->trip_options()
                                    ->withPrices()
                                    ->selectedColumns()
                                    ->where('alert', true)
                                    ->where("prices.created_at", ">", Carbon::now()->subHour(4))
                                    ->orderBy("prices.created_at", "DESC")
                                    ->orderBy("prices.price_total_brl", "ASC");

            //dd($optionsByDate->toSql());

            $newest = $optionsByDate->get()->first();

            $optionsByPrice = $trip->trip_options()
                                    ->withPrices()
                                    ->selectedColumns()
                                    ->where('alert', true)
                                    ->orderBy("price_total_brl", "ASC");

            if($newest) {
                $optionsByPrice->whereNotIn('prices.id', [$newest->price_id]);
            }

            //dd($optionsByPrice->toSql());

            $cheapest = $optionsByPrice->first();

            $newest_total = $newest ? $newest->price_total_brl : 0;
            $cheapest_total = $cheapest->price_total_brl;

            $min_price = $trip->min_price;

            $this->line("  Mais recente: " . $newest_total);
            $this->line("  Mais barato: " . $cheapest_total);
            //dd($newest_total, $cheapest_total);
            //dd($newest->id, $cheapest->id);

            if ($newest && ($newest_total < $cheapest_total)){
                $dif = $newest_total - $cheapest_total;
                $value = toReal($newest_total);
                $summary = "$value ($dif)";
                $trip_option = TripOption::find($newest->id);
                $this->line("  " . $summary);
                $cheapest_total = $newest_total;
                $cheapest = $newest;
                Mail::to($trip->user)->send(new PriceAlert($summary, $trip->description, $trip_option));
            }

            $trip_min_price = new MinimumPrice;
            $trip_min_price->value = $cheapest_total;
            $trip_min_price->trip_id = $trip->id;
            $trip_min_price->trip_option_id = $cheapest->id;
            $trip_min_price->save();

            $trip->min_price = $cheapest_total;
            if($trip->min_price != $min_price){
                $trip->save();
            }

        }

    }

}
