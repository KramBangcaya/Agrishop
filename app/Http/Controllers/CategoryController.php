<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $model;
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        // dd('sample');
        // dd("");
        $data = Category::latest();
        if ($request->search) {
            $data = $data->where('category', 'LIKE', '%' . $request->search . '%');
        }
        $data = $data->paginate($request->length);
        return response(['data' => $data], 200);
    }
    public function index_all()
    {
        $data = Category::all();
        return response(['data' => $data], 200);
    }

    public function all()
    {
        $data = Category::get();
        return response()->json(['data' => $data], 200);
    }


    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|string',
        //     'email' => 'required|email',
        //     'password' => 'required|string',
        // ]);
        // $data = $this->model->create($request->all());
        $data = Category::create([
            'category' => $request->category,

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
        $user = Category::findOrFail($id);
        $user->update([
            'category' => $request->category,
        ]);

        return response(['message' => 'success'], 200);
    }
    public function destroy($id)
    {
        $user = Category::findOrFail($id);
        $user->delete();
        return response(['message' => 'success'], 200);
    }
}
