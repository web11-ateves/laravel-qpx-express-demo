<?php

namespace App\Console\Commands;

use App\Models\Leg;
use App\Models\Price;
use App\Models\Segment;
use App\Models\Slice;
use App\Models\TripOption;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class CheckFlights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flights:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check prices for saved flights on Google QPX Express API';

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
        // https://developers.google.com/qpx-express/v1/trips/search
        // https://qpx-express-demo.itasoftware.com/
        // https://www.google.com.br/flights

        $this->info('Started checking flights...');

        $trips = Trip::all();
        foreach($trips as $trip) {

            $this->line($trip->description);

            $data = [];

            $data["request"]["passengers"]["kind"] = "qpxexpress#passengerCounts";
            if($trip->adults) { $data["request"]["passengers"]["adultCount"] = $trip->adults; }
            if($trip->children) { $data["request"]["passengers"]["childCount"] = $trip->children; }
            if($trip->infants) { $data["request"]["passengers"]["infantInLapCount"] = $trip->infants; }
            if($trip->seniors) { $data["request"]["passengers"]["seniorCount"] = $trip->seniors; }

            if($trip->max_price) {$data["request"]["maxPrice"] = $trip->max_price; }
            $data["request"]["solutions"] = env('GOOGLE_QPX_SOLUTIONS');
            $data["request"]["saleCountry"] = "BR";

            $slice = [];
            $slice["kind"] = "qpxexpress#sliceInput";
            $slice["origin"] = $trip->origin;
            $slice["destination"] = $trip->destination;
            $slice["date"] = $trip->departure_date->format("Y-m-d");
            $slice["maxStops"] = $trip->max_stops;
            if($trip->max_connection_time) { $slice["maxConnectionDuration"] = $trip->max_connection_time; }
            if($trip->earliest_departure_time || $trip->latest_departure_time) {
                $slice["permittedDepartureTime"]["kind"] = "qpxexpress#timeOfDayRange";
                if($trip->earliest_departure_time) { $slice["permittedDepartureTime"]["earliestTime"] = $trip->earliest_departure_time; }
                if($trip->latest_departure_time) { $slice["permittedDepartureTime"]["latestTime"] = $trip->latest_departure_time; }
            }
            if($trip->permitted_carriers) { $slice["permittedCarrier"] = explode(",", $trip->permitted_carriers); }
            if($trip->prohibited_carriers) { $slice["prohibitedCarrier"] = explode(",", $trip->prohibited_carriers); }

            $data["request"]["slice"] = [];
            array_push($data["request"]["slice"], $slice);

            if($trip->roundtrip) {
                $slice2 = [];
                $slice2["kind"] = "qpxexpress#sliceInput";
                $slice2["origin"] = $trip->destination;
                $slice2["destination"] = $trip->origin;
                $slice2["date"] = $trip->return_date->format("Y-m-d");
                $slice2["maxStops"] = $trip->max_stops;
                if($trip->max_connection_time) { $slice2["maxConnectionDuration"] = $trip->max_connection_time; }
                if($trip->permitted_carriers) { $slice2["permittedCarrier"] = explode(",", $trip->permitted_carriers); }
                if($trip->prohibited_carriers) { $slice2["prohibitedCarrier"] = explode(",", $trip->prohibited_carriers); }
                array_push($data["request"]["slice"], $slice2);
            }

            $client = new Client();
            $result = $client->post("https://www.googleapis.com/qpxExpress/v1/trips/search", [
                'query' => ['key' => env('GOOGLE_QPX_API_KEY')],
                'json' => $data]);

            $response = json_decode($result->getBody()->getContents());

            $trips = $response->trips;
            $trips_data = $trips->data;
            $airports = $trips_data->airport;
            $cities = $trips_data->city;
            $aircrafts = $trips_data->aircraft;
            $carriers = $trips_data->carrier;
            $trip_options = $trips->tripOption;

            foreach($trip_options as $trip_option) {

                $codeArray = [];
                $slices = $trip_option->slice;
                foreach ($slices as $slice) {
                    $segments = $slice->segment;
                    foreach ($segments as $segment) {
                        $codeArray[] = "$segment->id.$segment->bookingCode";
                    }
                }
                $code = join('-', $codeArray);

                $to = TripOption::where('code', $code)->first();
                $new_option = !$to;
                if($new_option) {
                    $to = new TripOption;
                    $to->code = $code;
                    $to->trip_id = $trip->id;
                    $to->save();
                    $this->line("Trip Option $to->id created successfully");
                } else {
                    $this->line("Trip Option $to->id already exists");
                }

                $pricing = $trip_option->pricing;
                foreach($pricing as $price) {
                    $baseFareTotal = str_replace("USD", "", $price->baseFareTotal); //USD
                    $saleFareTotal = str_replace("BRL", "", $price->saleFareTotal); //BRL
                    $saleTaxTotal = str_replace("BRL", "", $price->saleTaxTotal); //BRL
                    $saleTotal = str_replace("BRL", "", $price->saleTotal); //BRL
                    $pr = new Price;
                    $pr->base_fare_usd = $baseFareTotal;
                    $pr->base_fare_brl = $saleFareTotal;
                    $pr->taxes_brl = $saleTaxTotal;
                    $pr->price_total_brl = $saleTotal;
                    $pr->trip_option_id = $to->id;
                    $this->line("  Price: $pr->price_total_brl");
                    $pr->save();
                }

                if($new_option) {

                    foreach ($slices as $slice) {
                        $duration = $slice->duration;

                        $codeArray = [];
                        $segments = $slice->segment;
                        foreach ($segments as $segment) {
                            $codeArray[] = $segment->id;
                        }
                        $slice_code = join('-', $codeArray);

                        $new_slice = true;
                        $sl = new Slice;
                        $sl->duration = $duration;
                        $sl->code = $slice_code;
                        $sl->trip_option_id = $to->id;
                        $sl->save();
                        $this->line("  Slice $sl->id created successfully");

                        foreach ($segments as $segment) {
                            $duration = $segment->duration;
                            $segment_id = $segment->id;
                            $cabin = $segment->cabin;
                            $bookingCodeCount = $segment->bookingCodeCount;
                            $flight = $segment->flight;
                            $carrier = $flight->carrier;
                            $number = $flight->number;
                            $new_segment = true;
                            $sg = new Segment;
                            $sg->code = $segment_id;
                            $sg->duration = $duration;
                            $sg->carrier_code = $carrier;
                            $sg->carrier_name = $this->get_name_by_code($carrier, $carriers);
                            $sg->seats_available = $bookingCodeCount;
                            $sg->cabin = $cabin;
                            $sg->flight_number = $number;
                            $sg->connection_duration = isset($segment->connectionDuration) ? $segment->connectionDuration : 0;
                            $sg->slice_id = $sl->id;
                            $sg->save();
                            $this->line("    Segment $segment_id created successfully");

                            $legs = $segment->leg;
                            foreach ($legs as $leg) {
                                $leg_id = $leg->id;
                                $aircraft = $leg->aircraft;
                                $arrivalTime = $leg->arrivalTime;
                                $departureTime = $leg->departureTime;
                                $origin = $leg->origin;
                                $destination = $leg->destination;
                                $duration = $leg->duration;
                                $mileage = $leg->mileage;

                                $lg = new Leg;
                                $lg->code = $leg_id;
                                $lg->aircraft = $aircraft;
                                $lg->origin_airport = $origin;
                                $lg->origin_airport_name = $this->get_name_by_code($origin, $airports);
                                $lg->origin_city = $this->get_name_by_code($origin, $airports, 'city');
                                $lg->destination_airport = $destination;
                                $lg->destination_airport_name = $this->get_name_by_code($destination, $airports);
                                $lg->destination_city = $this->get_name_by_code($destination, $airports, 'city');
                                $lg->departure_time = Carbon::parse($departureTime);
                                $lg->arrival_time = Carbon::parse($arrivalTime);
                                $lg->duration = $duration;
                                $lg->mileage = $mileage;
                                $lg->change_plane = isset($leg->changePlane);
                                $lg->connection_duration = isset($leg->connectionDuration) ? $leg->connectionDuration : 0;
                                $lg->segment_id = $sg->id;
                                $lg->save();
                                $this->line("      Leg $leg_id created successfully");
                            }

                            if($new_segment) {
                                $first_leg = array_first($legs);
                                $last_leg = last($legs);
                                $sg->origin_airport = $first_leg->origin; //First Leg
                                $sg->destination_airport = $last_leg->destination; //Last leg
                                $sg->departure_time = Carbon::parse($first_leg->departureTime); //First Leg
                                $sg->arrival_time = Carbon::parse($last_leg->arrivalTime); //Last leg
                                $sg->save();
                                $this->line("    Segment $segment_id updated");
                            }
                        }

                        if($new_slice){
                            $sl_segments = $sl->segments;
                            $first_segment = $sl_segments->first();
                            $last_segment = $sl_segments->last();
                            $sl->origin_airport = $first_segment->origin_airport; //First Segment
                            $sl->destination_airport = $last_segment->destination_airport; //Last Segment
                            $sl->departure_time = $first_segment->departure_time; //First Segment
                            $sl->arrival_time = $last_segment->arrival_time; //Last Segment
                            $sl->connection_duration = array_sum(array_pluck($sl_segments, 'connection_duration'));
                            $stops = $sl_segments->count() - 1;
                            $sl->stops = $stops;
                            $sl->save();
                            $this->line("  Slice $sl->id updated");
                        }

                    }

                    $to_slices = $to->slices;
                    $to->duration = array_sum(array_pluck($to_slices, 'duration'));
                    $to->connection_duration = array_sum(array_pluck($to_slices, 'connection_duration'));
                    $to->stops = array_sum(array_pluck($to_slices, 'stops'));
                    $to->save();
                    $this->line("Trip Option updated");
                }

                $this->line("---------------------------------------------------------");

            }

        }

    }

    public function filter_json($value, $array, $attr)
    {
        return array_first(array_filter($array, function ($e) use ($value, $attr) {
                return $e->code == $value;
            }
        ));
    }

    public function get_name_by_code($value, $array, $attr = 'name')
    {
        $object = $this->filter_json($value, $array, $attr);
        return $object->{$attr};
    }

}
