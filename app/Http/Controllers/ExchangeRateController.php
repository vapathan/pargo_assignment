<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeRequest;
use App\Models\CurrencySymbol;
use App\Models\RatesHistory;
use App\Services\ExchangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ExchangeRateController extends Controller
{
    /**
     * @var ExchangeService
     */
    private $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * loads home page with list of symbols and rates history
     * @return Response
     */
    public function home()
    {
        $symbols = CurrencySymbol::all();

        if (count($symbols) == 0) {
            $symbols = $this->exchangeService->getSymbols();
            foreach ($symbols as $symbol => $country) {
                CurrencySymbol::create([
                    'symbol' => $symbol,
                    'country_name' => $country
                ]);
            }
            $symbols = CurrencySymbol::all();
        }

        return Inertia::render('Home', [
            'symbols' => $symbols,
            'ratesHistory' => RatesHistory::get(['id', 'start_date', 'end_date', 'base'])
        ]);
    }

    /**
     * fetch rates using api
     * @param ExchangeRequest $request
     * @return JsonResponse
     */
    public function getRates(ExchangeRequest $request)
    {
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rates = $this->exchangeService->getExchangeRates($request->start_date, $request->end_date, $request->base);

        if ($rates != null) {
            $rates = $this->processRates($rates);
            return response()->json($rates, 200);
        } else {
            return response()->json(['message' => 'Error occurred while fetching rates'], 400);
        }
    }

    /**
     * get rates from local db
     * @param $historyId
     * @return JsonResponse
     */
    public function getRatesFromHistory($historyId)
    {
        $ratesHistory = RatesHistory::find($historyId);
        if ($ratesHistory) {
            $rates = $this->processRates($ratesHistory->rates);
            return response()->json($rates, 200);
        } else {
            return response()->json(['message' => 'Error occurred while fetching rates'], 400);
        }
    }


    /**
     * save search entry as a rate history
     * @param ExchangeRequest $request
     * @return RedirectResponse
     */
    public function saveRatesHistory(ExchangeRequest $request)
    {
        $rates = $this->exchangeService->getExchangeRates($request->start_date, $request->end_date, $request->base);

        $ratesHistory = RatesHistory::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'base' => $request->base,
            'rates' => $rates
        ]);

        if ($ratesHistory) {
            return redirect()->back()
                ->with(['message' => 'Rates history saved successfully', 'status' => 'success']);
        } else {
            return redirect()->back()
                ->with(['message' => 'Error occurred while saving rates history', 'status' => 'error']);
        }
    }

    /**
     * delete search entry
     * @param $historyId
     * @return RedirectResponse
     */
    public function deleteRatesHistory($historyId)
    {
        $ratesHistory = RatesHistory::find($historyId);
        if ($ratesHistory && $ratesHistory->delete()) {
            return redirect()->back()
                ->with(['message' => 'Rates history deleted successfully', 'status' => 'success']);
        } else {
            return redirect()->back()
                ->with(['message' => 'Error occurred while deleting rates history', 'status' => 'error']);
        }
    }

    /**
     * calculate min, max and average for rates in the selected span
     * @param $rates
     * @return Collection
     */
    private function processRates($rates)
    {
        $rates = collect($rates);
        $rates = $rates->reverse();
        $symbols = array_keys($rates->get($rates->keys()[0]));
        $min = [];
        $max = [];
        $avg = [];
        foreach ($symbols as $symbol) {
            $min[$symbol] = $rates->min($symbol);
            $max[$symbol] = $rates->max($symbol);
            $avg[$symbol] = $rates->avg($symbol);
        }
        $rates['Min'] = $min;
        $rates['Max'] = $max;
        $rates['Avg'] = $avg;
        return $rates;
    }
}
