<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    private $model;
    public function __construct(Measurement $model)
    {
        $this->model = $model;
    }
    public function index(Request $request)
    {
        // dd('sample');
        // dd("");
        $data = Measurement::latest();
        if ($request->search) {
            $data = $data->where('measurement', 'LIKE', '%' . $request->search . '%');
        }
        $data = $data->paginate($request->length);
        return response(['data' => $data], 200);
    }
    public function index_all()
    {
        $data = Measurement::all();
        return response(['data' => $data], 200);
    }
    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|string',
        //     'email' => 'required|email',
        //     'password' => 'required|string',
        // ]);
        // $data = $this->model->create($request->all());
        $data = Measurement::create([
            'measurement' => $request->measurement,

        ]);
        // $data->assignRole($request->role['name']);
        // foreach ($request->permissions as $permission) {
        //     $data->givePermissionTo($permission['name']);
        // }
        return response(['message' => 'success'], 200);
    }
    public function update(Request $request, $id)
    {


        $this->validate($request, []);
        $user = Measurement::findOrFail($id);
        $user->update([
            'measurement' => $request->measurement,
        ]);

        return response(['message' => 'success'], 200);
    }

    public function destroy($id)
    {
        $user = Measurement::findOrFail($id);
        $user->delete();
        return response(['message' => 'success'], 200);
    }
}
