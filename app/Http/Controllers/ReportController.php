<?php

namespace App\Http\Controllers;

use App\Models\report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    private $model;
    public function __construct(report $model)
    {
        $this->model = $model;
    }

    public function index(Request $request){
        $userID = auth()->user()->id;

        $data = report::latest();

        $data = $data->paginate($request->length);

        return response([
            'data' => $data,
            'userID' => $userID,
        ], 200);
    }

    public function index_all(Request $request){
        $data = report::all();

        return response([
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $userID = auth()->user()->id;
        // dd($userID);
        $request->validate([
            'reason' => 'required|string',
            'proof.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // validate each uploaded file
        ]);

        $photoPaths = [];
        if ($request->hasFile('proof')) {
            foreach ($request->file('proof') as $photo) {
                $path = $photo->store('proof', 'public');
                $photoPaths[] = $path;
            }
        }

        $report = report::create([
            'reason' => $request->reason,
            'buyer_name' => $request->reported_name,
            'userID' => $userID,
            'proof' => json_encode($photoPaths),
        ]);

        return response(['message' => 'success'], 200);
    }

    public function response(Request $request){

        // dd($request->all());

        $report = report::findOrFail($request->id);


        $report->update([
            'reply' => $request->response,
        ]);

        return response(['message' => 'success'], 200);


    }

    public function destroy($id)
    {
        $user = report::findOrFail($id);
        $user->delete();
        return response(['message' => 'success'], 200);
    }
}
