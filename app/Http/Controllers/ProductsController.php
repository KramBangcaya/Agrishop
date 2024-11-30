<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductsController extends Controller
{
    //
    private $model;
    public function __construct(Products $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $userID = auth()->user()->id;

        // dd($emp_code);
        $data = Products::with('Category', 'Measurement')
            ->OrderBy('Quantity', 'desc')
            ->where('userID', $userID)
            ->where('Quantity', '>', 10)
            ->latest();

        $data = $data->paginate($request->length);

        $data->getCollection()->transform(function ($item) {
            // Add the 'category' field from the related Category model to the product data
            $item->category_name = $item->category ? $item->category->category : null; // 'category' is the field in the Category model

            $item->measurement_name = $item->measurement ? $item->measurement->measurement : null;
            return $item;
        });
        // dd($data);
        return response([
            'data' => $data,
            'userID' => $userID,
        ], 200);
    }

    public function category_all(Request $request, $id){
        $data = Products::where('idCategory', $id)->get();

        return response()->json(['data' => $data], 200);
    }

    public function index_all()
    {
        // $user = User::all();

        $data = Products::with('User')->get();

        // dd($data);

        $formattedData = $data->map(function ($product) {
            return [
                'id' => $product->id,
                'Product_Name' => $product->Product_Name,
                'idCategory' => $product->idCategory,
                'price' => $product->price,
                'idMeasurement' => $product->idMeasurement,
                'Quantity' => $product->Quantity,
                'Description' => $product->Description,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'photos' => json_decode($product->photos),
                'photos1' => json_decode($product->photos1),
                'photos2' => json_decode($product->photos2),
                'userID' => $product->userID,
                'first_name' => $product->user->name ?? null,
                'last_name' => $product->user->lastname ?? null, // Add user name here
                'contact_number' => $product->user->contact_number ?? null, // Add user name here
            ];
        });

        // dd($formattedData);
        return response()->json(['data' => $formattedData], 200);
    }

    public function index1(Request $request)
    {
        $userID = auth()->user()->id;

        // dd($emp_code);
        $data = Products::with('Category', 'Measurement')
            ->OrderBy('Quantity', 'desc')
            ->where('userID', $userID)
            ->where('Quantity', '<=', 10)
            ->latest();

        $data = $data->paginate($request->length);

        $data->getCollection()->transform(function ($item) {
            // Add the 'category' field from the related Category model to the product data
            $item->category_name = $item->category ? $item->category->category : null; // 'category' is the field in the Category model

            $item->measurement_name = $item->measurement ? $item->measurement->measurement : null;
            return $item;
        });
        return response([
            'data' => $data,
            'userID' => $userID,
        ], 200);
    }


    public function store(Request $request)
    {

        $userID = auth()->user()->id;
        // dd($request->all());
        // dd($request->measurement_id);
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required|string',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // validate each uploaded file
            'photos1.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // validate each uploaded file
            'photos2.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // validate each uploaded file
        ]);

        // Handle file uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('product_photos', 'public');
                $photoPaths[] = $path;
            }
        }

        $photoPaths1 = [];
        if ($request->hasFile('photos1')) {
            foreach ($request->file('photos1') as $photo) {
                $path = $photo->store('product_photos', 'public');
                $photoPaths1[] = $path;
            }
        }

        $photoPaths2 = [];
        if ($request->hasFile('photos2')) {
            foreach ($request->file('photos2') as $photo) {
                $path = $photo->store('product_photos', 'public');
                $photoPaths2[] = $path;
            }
        }


        // dd($request->measurement_id + 0);
        // dd($photoPaths);
        // Create product with photo paths
        $product = Products::create([
            'Product_Name' => $request->product_name,
            'idCategory' => $request->category_id,
            'price' => $request->price,
            'Quantity' => $request->quantity,
            'Description' => $request->description,
            'idMeasurement' => $request->measurement_id,
            'photos' => json_encode($photoPaths),
            'photos1' => json_encode($photoPaths1),
            'photos2' => json_encode($photoPaths2),
            'userID' => $userID,
            // Store photo paths as JSON
        ]);

        // dd($product);
        // Dump a message indicating product creation with photos


        return response(['message' => 'success'], 200);
    }

    public function update(Request $request, $id)
    {

        // dd($request->measurement_id);
        // dd($request->measurement_id);

        $measurement = $request->measurement_id['id'];

        // dd($measurement);
        $this->validate($request, []);
        $user = Products::findOrFail($id);

        $user->update([
            'Product_Name' => $request->Product_Name,
            'price' => $request->price,
            'Quantity' => $request->Quantity,
            'Description' => $request->Description,
            'idMeasurement' => $measurement,
        ]);


        return response(['message' => 'success'], 200);
    }

    public function update1(Request $request, $id)
    {

        // dd($request->measurement_id);


        $this->validate($request, []);
        $user = Products::findOrFail($id);
        // dd($user);
        $user->update([
            'Product_Name' => $request->Product_Name,
            'price' => $request->price,
            'Quantity' => $request->Quantity,
            'Description' => $request->Description,
            // 'idMeasurement' => $request->measurement_id,
        ]);

        return response(['message' => 'success'], 200);
    }

    public function destroy($id)
    {
        $user = Products::findOrFail($id);
        $user->delete();
        return response(['message' => 'success'], 200);
    }
}
