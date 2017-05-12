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
            $data["request"]["solutions"] = 2;

            $slice = [];
            $slice["kind"] = "qpxexpress#sliceInput";
            $slice["origin"] = $trip->origin;
            $slice["destination"] = $trip->destination;
            $slice["date"] = $trip->departure_date->format("Y-m-d");
            if($trip->maxStops) { $slice["maxStops"] = $trip->maxStops; }
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
                $slice2["date"] = $trip->return_date;
                if($trip->maxStops) { $slice2["maxStops"] = $trip->maxStops; }
                if($trip->max_connection_time) { $slice2["maxConnectionDuration"] = $trip->max_connection_time; }
                if($trip->permitted_carriers) { $slice2["permittedCarrier"] = explode(",", $trip->permitted_carriers); }
                if($trip->prohibited_carriers) { $slice2["prohibitedCarrier"] = explode(",", $trip->prohibited_carriers); }
                array_push($data["request"]["slice"], $slice2);
            }

            //$client = new Client();
            //$result = $client->post("https://www.googleapis.com/qpxExpress/v1/trips/search", [
            //    'query' => ['key' => env('GOOGLE_QPX_API_KEY')],
            //    'json' => $data]);

            $json = "{\n
 \"kind\": \"qpxExpress#tripsSearch\",\n
 \"trips\": {\n
  \"kind\": \"qpxexpress#tripOptions\",\n
  \"requestId\": \"U3FhqBIkmFKh4vvPO0QWlL\",\n
  \"data\": {\n
   \"kind\": \"qpxexpress#data\",\n
   \"airport\": [\n
    {\n
     \"kind\": \"qpxexpress#airportData\",\n
     \"code\": \"EWR\",\n
     \"city\": \"EWR\",\n
     \"name\": \"Newark Liberty International\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#airportData\",\n
     \"code\": \"GRU\",\n
     \"city\": \"SAO\",\n
     \"name\": \"Sao Paulo Guarulhos International\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#airportData\",\n
     \"code\": \"IAD\",\n
     \"city\": \"WAS\",\n
     \"name\": \"Washington Dulles International\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#airportData\",\n
     \"code\": \"PTY\",\n
     \"city\": \"PTY\",\n
     \"name\": \"Panama City Tocumen Int'l\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#airportData\",\n
     \"code\": \"YYZ\",\n
     \"city\": \"YTO\",\n
     \"name\": \"Toronto Lester B Pearson\"\n
    }\n
   ],\n
   \"city\": [\n
    {\n
     \"kind\": \"qpxexpress#cityData\",\n
     \"code\": \"EWR\",\n
     \"name\": \"Newark\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#cityData\",\n
     \"code\": \"PTY\",\n
     \"name\": \"Panama City\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#cityData\",\n
     \"code\": \"SAO\",\n
     \"name\": \"Sao Paulo\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#cityData\",\n
     \"code\": \"WAS\",\n
     \"name\": \"Washington\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#cityData\",\n
     \"code\": \"YTO\",\n
     \"name\": \"Toronto\"\n
    }\n
   ],\n
   \"aircraft\": [\n
    {\n
     \"kind\": \"qpxexpress#aircraftData\",\n
     \"code\": \"738\",\n
     \"name\": \"Boeing 737\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#aircraftData\",\n
     \"code\": \"764\",\n
     \"name\": \"Boeing 767\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#aircraftData\",\n
     \"code\": \"777\",\n
     \"name\": \"Boeing 777\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#aircraftData\",\n
     \"code\": \"CR7\",\n
     \"name\": \"Canadair RJ 700\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#aircraftData\",\n
     \"code\": \"ERJ\",\n
     \"name\": \"Embraer ERJ-135/145\"\n
    }\n
   ],\n
   \"tax\": [\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"XY\",\n
     \"name\": \"US Immigration Fee\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"RC\",\n
     \"name\": \"Canadian Harmonized Sales Tax (ON)\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"AH_003\",\n
     \"name\": \"Panama Airport Security Fee Departures\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"AY_001\",\n
     \"name\": \"US September 11th Security Fee\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"XA\",\n
     \"name\": \"USDA APHIS Fee\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"BR_004\",\n
     \"name\": \"Brazil Embarkation Fee International\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"YC\",\n
     \"name\": \"US Customs Fee\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"XF\",\n
     \"name\": \"US Passenger Facility Charge\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"SQ\",\n
     \"name\": \"Toronto Airport Improvement Fee\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#taxData\",\n
     \"id\": \"CA\",\n
     \"name\": \"Canadian Air Travelers Security Charge\"\n
    }\n
   ],\n
   \"carrier\": [\n
    {\n
     \"kind\": \"qpxexpress#carrierData\",\n
     \"code\": \"CM\",\n
     \"name\": \"Compania Panamena de Aviacion, S.A.\"\n
    },\n
    {\n
     \"kind\": \"qpxexpress#carrierData\",\n
     \"code\": \"UA\",\n
     \"name\": \"United Airlines, Inc.\"\n
    }\n
   ]\n
  },\n
  \"tripOption\": [\n
   {\n
    \"kind\": \"qpxexpress#tripOption\",\n
    \"saleTotal\": \"BRL3101.10\",\n
    \"id\": \"E7jdysgjWFNLYxczeCZVTG001\",\n
    \"slice\": [\n
     {\n
      \"kind\": \"qpxexpress#sliceInfo\",\n
      \"duration\": 786,\n
      \"segment\": [\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 580,\n
        \"flight\": {\n
         \"carrier\": \"UA\",\n
         \"number\": \"148\"\n
        },\n
        \"id\": \"G3tVqsniVPtyAeRp\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"T\",\n
        \"bookingCodeCount\": 5,\n
        \"marriedSegmentGroup\": \"0\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"LxIoNvHdSXdeS2Xh\",\n
          \"aircraft\": \"777\",\n
          \"arrivalTime\": \"2017-08-05T05:40-04:00\",\n
          \"departureTime\": \"2017-08-04T21:00-03:00\",\n
          \"origin\": \"GRU\",\n
          \"destination\": \"EWR\",\n
          \"originTerminal\": \"3\",\n
          \"destinationTerminal\": \"C\",\n
          \"duration\": 580,\n
          \"mileage\": 4772,\n
          \"meal\": \"Dinner\",\n
          \"secure\": true\n
         }\n
        ],\n
        \"connectionDuration\": 105\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 101,\n
        \"flight\": {\n
         \"carrier\": \"UA\",\n
         \"number\": \"4199\"\n
        },\n
        \"id\": \"G1VKFfs0+fjYoDga\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"T\",\n
        \"bookingCodeCount\": 5,\n
        \"marriedSegmentGroup\": \"0\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"L1gKPPH7Yy62tWxD\",\n
          \"aircraft\": \"ERJ\",\n
          \"arrivalTime\": \"2017-08-05T09:06-04:00\",\n
          \"departureTime\": \"2017-08-05T07:25-04:00\",\n
          \"origin\": \"EWR\",\n
          \"destination\": \"YYZ\",\n
          \"originTerminal\": \"A\",\n
          \"destinationTerminal\": \"1\",\n
          \"duration\": 101,\n
          \"operatingDisclosure\": \"OPERATED BY EXPRESSJET AIRLINES DBA UNITED EXPRESS\",\n
          \"mileage\": 347,\n
          \"secure\": true\n
         }\n
        ]\n
       }\n
      ]\n
     },\n
     {\n
      \"kind\": \"qpxexpress#sliceInfo\",\n
      \"duration\": 741,\n
      \"segment\": [\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 85,\n
        \"flight\": {\n
         \"carrier\": \"UA\",\n
         \"number\": \"6103\"\n
        },\n
        \"id\": \"G7UGy72esYFzNvr0\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"T\",\n
        \"bookingCodeCount\": 9,\n
        \"marriedSegmentGroup\": \"1\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"Luqzhpv9lmuqbMZi\",\n
          \"aircraft\": \"CR7\",\n
          \"arrivalTime\": \"2017-08-26T20:59-04:00\",\n
          \"departureTime\": \"2017-08-26T19:34-04:00\",\n
          \"origin\": \"YYZ\",\n
          \"destination\": \"IAD\",\n
          \"originTerminal\": \"1\",\n
          \"duration\": 85,\n
          \"operatingDisclosure\": \"OPERATED BY MESA AIRLINES DBA UNITED EXPRESS\",\n
          \"mileage\": 345,\n
          \"secure\": true\n
         }\n
        ],\n
        \"connectionDuration\": 61\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 595,\n
        \"flight\": {\n
         \"carrier\": \"UA\",\n
         \"number\": \"861\"\n
        },\n
        \"id\": \"GkQoMC+e5Rm7VVtZ\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"T\",\n
        \"bookingCodeCount\": 9,\n
        \"marriedSegmentGroup\": \"1\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"L7BKVzwbLolsi38T\",\n
          \"aircraft\": \"764\",\n
          \"arrivalTime\": \"2017-08-27T08:55-03:00\",\n
          \"departureTime\": \"2017-08-26T22:00-04:00\",\n
          \"origin\": \"IAD\",\n
          \"destination\": \"GRU\",\n
          \"destinationTerminal\": \"3\",\n
          \"duration\": 595,\n
          \"mileage\": 4750,\n
          \"meal\": \"Dinner\",\n
          \"secure\": true\n
         }\n
        ]\n
       }\n
      ]\n
     }\n
    ],\n
    \"pricing\": [\n
     {\n
      \"kind\": \"qpxexpress#pricingInfo\",\n
      \"fare\": [\n
       {\n
        \"kind\": \"qpxexpress#fareInfo\",\n
        \"id\": \"AZMCbqt40ZGCOpOMEwdy/cY4GQ5OajvHurvNxhrDBiD1yeU\",\n
        \"carrier\": \"UA\",\n
        \"origin\": \"SAO\",\n
        \"destination\": \"YTO\",\n
        \"basisCode\": \"TLW0D9VF\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#fareInfo\",\n
        \"id\": \"AZMCbqt40ZGCOpOMEwdy/cY4GQ5OajvHurvNxhrDBiD1yeU\",\n
        \"carrier\": \"UA\",\n
        \"origin\": \"YTO\",\n
        \"destination\": \"SAO\",\n
        \"basisCode\": \"TLW0D9VF\"\n
       }\n
      ],\n
      \"segmentPricing\": [\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"AZMCbqt40ZGCOpOMEwdy/cY4GQ5OajvHurvNxhrDBiD1yeU\",\n
        \"segmentId\": \"G1VKFfs0+fjYoDga\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"AZMCbqt40ZGCOpOMEwdy/cY4GQ5OajvHurvNxhrDBiD1yeU\",\n
        \"segmentId\": \"G7UGy72esYFzNvr0\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"AZMCbqt40ZGCOpOMEwdy/cY4GQ5OajvHurvNxhrDBiD1yeU\",\n
        \"segmentId\": \"G3tVqsniVPtyAeRp\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"AZMCbqt40ZGCOpOMEwdy/cY4GQ5OajvHurvNxhrDBiD1yeU\",\n
        \"segmentId\": \"GkQoMC+e5Rm7VVtZ\"\n
       }\n
      ],\n
      \"baseFareTotal\": \"USD854.00\",\n
      \"saleFareTotal\": \"BRL2695.13\",\n
      \"saleTaxTotal\": \"BRL405.97\",\n
      \"saleTotal\": \"BRL3101.10\",\n
      \"passengers\": {\n
       \"kind\": \"qpxexpress#passengerCounts\",\n
       \"adultCount\": 1\n
      },\n
      \"tax\": [\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"AY_001\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"AY\",\n
        \"country\": \"US\",\n
        \"salePrice\": \"BRL35.34\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"BR_004\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"BR\",\n
        \"country\": \"BR\",\n
        \"salePrice\": \"BRL113.37\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"XA\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"XA\",\n
        \"country\": \"US\",\n
        \"salePrice\": \"BRL24.98\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"XY\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"XY\",\n
        \"country\": \"US\",\n
        \"salePrice\": \"BRL44.18\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"YC\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"YC\",\n
        \"country\": \"US\",\n
        \"salePrice\": \"BRL34.70\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"XF\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"XF\",\n
        \"country\": \"US\",\n
        \"salePrice\": \"BRL28.40\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"SQ\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"SQ\",\n
        \"country\": \"CA\",\n
        \"salePrice\": \"BRL57.70\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"RC\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"RC\",\n
        \"country\": \"CA\",\n
        \"salePrice\": \"BRL7.50\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"CA\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"CA\",\n
        \"country\": \"CA\",\n
        \"salePrice\": \"BRL59.80\"\n
       }\n
      ],\n
      \"fareCalculation\": \"SAO UA X/EWR UA YTO 427.00TLW0D9VF UA X/WAS UA SAO 427.00TLW0D9VF NUC 854.00 END ROE 1.00 FARE USD 854.00 EQU BRL 2695.13 XT 113.37BR 34.70YC 44.18XY 24.98XA 35.34AY 59.80CA 7.50RC 57.70SQ 28.40XF EWR4.50 IAD4.50\",\n
      \"latestTicketingTime\": \"2017-05-12T23:59-04:00\",\n
      \"ptc\": \"ADT\"\n
     }\n
    ]\n
   },\n
   {\n
    \"kind\": \"qpxexpress#tripOption\",\n
    \"saleTotal\": \"BRL3320.09\",\n
    \"id\": \"E7jdysgjWFNLYxczeCZVTG002\",\n
    \"slice\": [\n
     {\n
      \"kind\": \"qpxexpress#sliceInfo\",\n
      \"duration\": 832,\n
      \"segment\": [\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 434,\n
        \"flight\": {\n
         \"carrier\": \"CM\",\n
         \"number\": \"700\"\n
        },\n
        \"id\": \"G9A0-be7qD38R4w6\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"S\",\n
        \"bookingCodeCount\": 3,\n
        \"marriedSegmentGroup\": \"0\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"LnU5PExb0GMQcEEL\",\n
          \"aircraft\": \"738\",\n
          \"arrivalTime\": \"2017-08-04T17:14-05:00\",\n
          \"departureTime\": \"2017-08-04T12:00-03:00\",\n
          \"origin\": \"GRU\",\n
          \"destination\": \"PTY\",\n
          \"originTerminal\": \"2\",\n
          \"duration\": 434,\n
          \"mileage\": 3158,\n
          \"meal\": \"Meal\"\n
         }\n
        ],\n
        \"connectionDuration\": 64\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 334,\n
        \"flight\": {\n
         \"carrier\": \"CM\",\n
         \"number\": \"470\"\n
        },\n
        \"id\": \"Ga18IVPaTnFl5xx1\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"S\",\n
        \"bookingCodeCount\": 3,\n
        \"marriedSegmentGroup\": \"0\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"LHJgwpggVQ8OIrRp\",\n
          \"aircraft\": \"738\",\n
          \"arrivalTime\": \"2017-08-05T00:52-04:00\",\n
          \"departureTime\": \"2017-08-04T18:18-05:00\",\n
          \"origin\": \"PTY\",\n
          \"destination\": \"YYZ\",\n
          \"destinationTerminal\": \"1\",\n
          \"duration\": 334,\n
          \"mileage\": 2390,\n
          \"meal\": \"Meal\",\n
          \"secure\": true\n
         }\n
        ]\n
       }\n
      ]\n
     },\n
     {\n
      \"kind\": \"qpxexpress#sliceInfo\",\n
      \"duration\": 825,\n
      \"segment\": [\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 338,\n
        \"flight\": {\n
         \"carrier\": \"CM\",\n
         \"number\": \"471\"\n
        },\n
        \"id\": \"GBwFiUZqhitjEw8r\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"E\",\n
        \"bookingCodeCount\": 9,\n
        \"marriedSegmentGroup\": \"1\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"LqkI8yO6ME8L60Cu\",\n
          \"aircraft\": \"738\",\n
          \"arrivalTime\": \"2017-08-26T14:23-05:00\",\n
          \"departureTime\": \"2017-08-26T09:45-04:00\",\n
          \"origin\": \"YYZ\",\n
          \"destination\": \"PTY\",\n
          \"originTerminal\": \"1\",\n
          \"duration\": 338,\n
          \"mileage\": 2390,\n
          \"meal\": \"Meal\",\n
          \"secure\": true\n
         }\n
        ],\n
        \"connectionDuration\": 65\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentInfo\",\n
        \"duration\": 422,\n
        \"flight\": {\n
         \"carrier\": \"CM\",\n
         \"number\": \"800\"\n
        },\n
        \"id\": \"GNRBpufsQSlQiA90\",\n
        \"cabin\": \"COACH\",\n
        \"bookingCode\": \"E\",\n
        \"bookingCodeCount\": 9,\n
        \"marriedSegmentGroup\": \"1\",\n
        \"leg\": [\n
         {\n
          \"kind\": \"qpxexpress#legInfo\",\n
          \"id\": \"LhWexno0ln1FUamI\",\n
          \"aircraft\": \"738\",\n
          \"arrivalTime\": \"2017-08-27T00:30-03:00\",\n
          \"departureTime\": \"2017-08-26T15:28-05:00\",\n
          \"origin\": \"PTY\",\n
          \"destination\": \"GRU\",\n
          \"destinationTerminal\": \"2\",\n
          \"duration\": 422,\n
          \"mileage\": 3158,\n
          \"meal\": \"Meal\"\n
         }\n
        ]\n
       }\n
      ]\n
     }\n
    ],\n
    \"pricing\": [\n
     {\n
      \"kind\": \"qpxexpress#pricingInfo\",\n
      \"fare\": [\n
       {\n
        \"kind\": \"qpxexpress#fareInfo\",\n
        \"id\": \"AFdSaPjRGi8hAuaHGmkRci0Y4Fe3zz17gdeuNKIlWvlQjo/\",\n
        \"carrier\": \"CM\",\n
        \"origin\": \"SAO\",\n
        \"destination\": \"YTO\",\n
        \"basisCode\": \"S7MZCA\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#fareInfo\",\n
        \"id\": \"ATqyFIMtIhEucPbHctNqtMOuT/87Kk0jV7rRSPBVS1VOKOo\",\n
        \"carrier\": \"CM\",\n
        \"origin\": \"YTO\",\n
        \"destination\": \"SAO\",\n
        \"basisCode\": \"ENOB65\"\n
       }\n
      ],\n
      \"segmentPricing\": [\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"AFdSaPjRGi8hAuaHGmkRci0Y4Fe3zz17gdeuNKIlWvlQjo/\",\n
        \"segmentId\": \"G9A0-be7qD38R4w6\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"ATqyFIMtIhEucPbHctNqtMOuT/87Kk0jV7rRSPBVS1VOKOo\",\n
        \"segmentId\": \"GBwFiUZqhitjEw8r\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"ATqyFIMtIhEucPbHctNqtMOuT/87Kk0jV7rRSPBVS1VOKOo\",\n
        \"segmentId\": \"GNRBpufsQSlQiA90\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#segmentPricing\",\n
        \"fareId\": \"AFdSaPjRGi8hAuaHGmkRci0Y4Fe3zz17gdeuNKIlWvlQjo/\",\n
        \"segmentId\": \"Ga18IVPaTnFl5xx1\"\n
       }\n
      ],\n
      \"baseFareTotal\": \"USD974.00\",\n
      \"saleFareTotal\": \"BRL3073.84\",\n
      \"saleTaxTotal\": \"BRL246.25\",\n
      \"saleTotal\": \"BRL3320.09\",\n
      \"passengers\": {\n
       \"kind\": \"qpxexpress#passengerCounts\",\n
       \"adultCount\": 1\n
      },\n
      \"tax\": [\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"AH_003\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"AH\",\n
        \"country\": \"PA\",\n
        \"salePrice\": \"BRL7.88\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"BR_004\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"BR\",\n
        \"country\": \"BR\",\n
        \"salePrice\": \"BRL113.37\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"SQ\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"SQ\",\n
        \"country\": \"CA\",\n
        \"salePrice\": \"BRL57.70\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"RC\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"RC\",\n
        \"country\": \"CA\",\n
        \"salePrice\": \"BRL7.50\"\n
       },\n
       {\n
        \"kind\": \"qpxexpress#taxInfo\",\n
        \"id\": \"CA\",\n
        \"chargeType\": \"GOVERNMENT\",\n
        \"code\": \"CA\",\n
        \"country\": \"CA\",\n
        \"salePrice\": \"BRL59.80\"\n
       }\n
      ],\n
      \"fareCalculation\": \"SAO CM X/PTY CM YTO 559.50S7MZCA CM X/PTY CM SAO 414.50ENOB65 NUC 974.00 END ROE 1.00 FARE USD 974.00 EQU BRL 3073.84 XT 113.37BR 7.88AH 59.80CA 7.50RC 57.70SQ\",\n
      \"latestTicketingTime\": \"2017-05-15T23:59-04:00\",\n
      \"ptc\": \"ADT\"\n
     }\n
    ]\n
   }\n
  ]\n
 }\n
}\n";

            //$response = json_decode($result->getBody()->getContents());
            $response = json_decode($json);

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
                        $codeArray[] = $segment->id;
                    }
                }
                $code = join('-', $codeArray);

                $to = TripOption::firstOrCreate(['code' => $code, 'trip_id' => $trip->id]);
                $this->line("Trip Option $to->id");

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
                    $pr->save();
                }

                $count = 0;
                foreach ($slices as $slice) {
                    $count += 1;
                    $duration = $slice->duration;

                    $codeArray = [];
                    $segments = $slice->segment;
                    foreach ($segments as $segment) {
                        $codeArray[] = $segment->id;
                    }
                    $slice_code = join('-', $codeArray);

                    $sl = Slice::where('code', $slice_code)->first();
                    $new_slice = !$sl;
                    if($new_slice) {
                        $sl = new Slice;
                        $sl->duration = $duration;
                        $sl->code = $slice_code;
                        $sl->trip_option_id = $to->id;
                        $sl->save();
                        $this->line("  Slice $sl->id created successfully");
                    } else {
                        $this->line("  Slice $sl->id already exists");
                    }

                    foreach ($segments as $segment) {
                        $duration = $segment->duration;
                        $segment_id = $segment->id;
                        $cabin = $segment->cabin;
                        $bookingCodeCount = $segment->bookingCodeCount;
                        $flight = $segment->flight;
                        $carrier = $flight->carrier;
                        $number = $flight->number;
                        $sg = Segment::where('code', $segment_id)->first();
                        $new_segment = !$sg;
                        if($new_segment) {
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
                        } else {
                            $this->line("    Segment $segment_id already exists");
                        }

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

                            $lg = Leg::where('code', $leg_id)->first();
                            $new_leg = !$lg;
                            if($new_leg) {
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
                            } else {
                                $this->line("      Leg $leg_id already exists");
                            }
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
                        $sl->connection_duration = array_sum(array_pluck($sl_segments, 'connection_duration')); // Sum segments connection duration
                        $sl->save();
                        $this->line("  Slice $count updated");
                    }

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
