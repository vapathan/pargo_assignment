<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeService
{
    private $apiUrl = 'https://api.apilayer.com/exchangerates_data/';

    /**
     * fetches list of all symbols (currency)
     * @return mixed|null
     */
    public function getSymbols()
    {
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            'apikey' => env('API_KEY'),
        ])->get($this->apiUrl . 'symbols');

        if ($response->successful()) {
            $response = $response->json();
            return $response['symbols'];
        } else {
            return null;
        }
    }

    /**
     * fetches exchange rates for provide span and base currency
     * @param $startDate
     * @param $endDate
     * @param $base
     * @return mixed|null
     */
    public function getExchangeRates($startDate, $endDate, $base)
    {
        $apiUrl = $this->apiUrl . 'timeseries?start_date=' .
            $startDate . '&end_date=' . $endDate . '&base=' . $base;
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            'apikey' => env('API_KEY'),
        ])->get($apiUrl);
        if ($response->successful()) {
            $response = $response->json();
            return $response['rates'];
        } else {
            return null;
        }
    }
}
