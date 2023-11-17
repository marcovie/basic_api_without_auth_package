<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QuoteService;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    /**
     * @param QuoteService $quoteService
     */
    public function __construct(public QuoteService $quoteService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index($cache = 1): JsonResponse
    {
        return $this->quoteService->getQuotes($cache);
    }
}
