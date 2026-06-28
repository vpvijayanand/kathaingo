<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;

class SearchController extends Controller
{
    protected $searchService;

    /**
     * SearchController constructor.
     *
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Handle search suggestions requests and return JSON formatted matches.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request)
    {
        $query = $request->query('q', '');
        
        if (mb_strlen($query) < 2) {
            return response()->json([
                'posts' => [],
                'authors' => [],
                'categories' => []
            ]);
        }

        $results = $this->searchService->searchAll($query);

        return response()->json($results);
    }
}
