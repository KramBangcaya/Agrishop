<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deliquency;
use App\Models\report;
use Illuminate\Http\Request;

class DeliquencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Load reports with the associated user data
    $data = Report::latest()->with('user');
    if ($request->search) {
        $data = $data->where('buyer_name', 'LIKE', '%' . $request->search . '%');
    }
    $data = $data->paginate($request->length);

    // Optionally, you can loop through the reports and get the user name

    $buyerCounts = Report::select('buyer_name')
        ->groupBy('buyer_name')
        ->selectRaw('buyer_name, COUNT(*) as count')
        ->pluck('count', 'buyer_name'); // Pluck to get an associative array

    $data->getCollection()->transform(function ($item) {
        // dd($item);
        $item->user_name = $item->user ? $item->user->name . " " . $item->user->lastname: null; // Get user name
        return $item;
    });

    return response([
        'data' => $data,
        'buyerCounts' => $buyerCounts,
    ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deliquency  $deliquency
     * @return \Illuminate\Http\Response
     */
    public function show(Deliquency $deliquency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deliquency  $deliquency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deliquency $deliquency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deliquency  $deliquency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deliquency $deliquency)
    {
        //
    }
}
