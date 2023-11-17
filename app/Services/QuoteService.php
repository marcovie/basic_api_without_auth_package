<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthService
 * @package App\Services
 */
class QuoteService
{
    public $maxPoolValue = 15;
    /**
     * @return void
     */
    public function getQuotes($cachingEnabled = 1) {
        $quotes                 = [];

        $recordsToDisplay       = ((int)env('APP_QUOTE_REQUEST_COUNT', 5)) ?: 5;
        $recordsToDisplay       = ($recordsToDisplay <= $this->maxPoolValue)?$recordsToDisplay:$this->maxPoolValue;

        $quotes = Cache::store('file')->get('myEndpointResponses');

        if(!isset($quotes) || $cachingEnabled == 0) {
            $responses = Http::acceptJson()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(5)
                ->connectTimeout(5)
                ->retry(5, 100, throw: false)
                ->pool(function (Pool $pool) use ($recordsToDisplay) {
                    for ($x = 0; $x < $recordsToDisplay; $x++) {
                        $pool->get('https://api.kanye.rest/');
                    }
                });


            $poolResponses = collect($responses)
                ->map(fn($response) => $response instanceof \Illuminate\Http\Client\Response
                    && $response->ok()
                )
                ->filter();

            //Might want to still send some of data if one http request fails. But for this task I will just send all or none
            if (count($poolResponses) == $recordsToDisplay && count(array_unique($poolResponses->toArray())) === 1) {
                foreach ($responses as $key => $response) {
                    if (isset($response['quote']))
                        $quotes[] = $response['quote'];
                }
                Cache::store('file')->put('myEndpointResponses', $quotes, 60);
                return sendResponse(json_encode($quotes), 'List of quotes.');
            }
        }
        else if(isset($quotes)) {
            return sendResponse(json_encode($quotes), 'List of quotes.');
        }
        return sendError('Service Unavailable.', Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
