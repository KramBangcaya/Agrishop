<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            if ($request->search) {
                $data = $data->where('Product_Name', 'LIKE', '%' . $request->search . '%');
            }

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

    public function minus_product($id, Request $request)
{
    // Get the product by its ID
    $product = Products::find($id);

    if (!$product) {
        return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
    }


    // Get the quantity to decrease from the request body
    $quantity_to_decrease = $request->input('quantity_to_decrease');

    if ($quantity_to_decrease <= 0) {
        return response()->json(['status' => 'error', 'message' => 'Invalid quantity'], 400);
    }

    // Check if there is enough stock
    if ($product->Quantity < $quantity_to_decrease) {
        return response()->json(['status' => 'error', 'message' => 'Not enough stock'], 400);
    }

    // Reduce the stock by the specified quantity
    $product->Quantity -= $quantity_to_decrease;

    // Save the updated product stock
    $product->save();

    return response()->json(['status' => 'success', 'message' => 'Product stock updated successfully']);
}


    public function category_all(Request $request, $id){


        $data = Products::where('idCategory', $id)->get();

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
                'last_name' => $product->user->lastname ?? null,
                'contact_number' => $product->user->contact_number ?? null,
                'latitude' => $product->user->latitude ?? null,
                'longitude' => $product->user->longitude ?? null,
            ];
        });

        return response()->json(['data' => $formattedData], 200);
    }

    public function getPrice(Request $request){

        // dd($request->all());
        $request->validate([
            'min' => 'required|numeric|min:0',
            'max' => 'required|numeric|min:0|gte:min',
        ]);

        $minPrice = $request->input('min');
        $maxPrice = $request->input('max');

        $data = Products::with('User')
        ->whereBetween('price', [$minPrice, $maxPrice])
        ->get();

    // Format the data
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
            'last_name' => $product->user->lastname ?? null,
            'contact_number' => $product->user->contact_number ?? null,
            'latitude' => $product->user->latitude ?? null,
            'longitude' => $product->user->longitude ?? null,
        ];
    });

    // Return the formatted data as a JSON response
    return response()->json(['data' => $formattedData], 200);
    }

    public function index_all()
    {

        $data = Products::with('User')->get();


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

    public function all_product(Request $request){
        $searchQuery = $request->query('search', '');

        // Query the products with optional search filter
        $data = Products::with('User')
            ->when($searchQuery, function ($query, $searchQuery) {
                return $query->where('Product_Name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->get();

        // Format the data
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
                'last_name' => $product->user->lastname ?? null,
                'contact_number' => $product->user->contact_number ?? null,
                'latitude' => $product->user->latitude ?? null,
                'longitude' => $product->user->longitude ?? null,
            ];
        });

        // Return the formatted data as a JSON response
        return response()->json(['data' => $formattedData], 200);
    }

    public function product($id){
         // Fetch the product by ID with the associated User
    $product = Products::with('User', 'measurement')->find($id);

    // Check if product exists
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }
    // Format the data
    $formattedData = [
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
        'last_name' => $product->user->lastname ?? null,
        'contact_number' => $product->user->contact_number ?? null,
        'address' => $product->user->address ?? null,
        'qrcode' => json_decode($product->user->qrcode) ?? null,
        'measurement' => $product->measurement->measurement ?? null,
    ];

    return response()->json(['data' => $formattedData], 200);
    }


    public function product_stock($id){
        // Fetch the product by ID with the associated User
   $product = Products::with('User', 'measurement')->find($id);

   // Check if product exists
   if (!$product) {
       return response()->json(['message' => 'Product not found'], 404);
   }
   // Format the data
   $formattedData = [
       'id' => $product->id,
       'Product_Name' => $product->Product_Name,
       'Quantity' => $product->Quantity,
       'userID' => $product->userID,
       'measurement' => $product->measurement->measurement ?? null,
   ];

   return response()->json(['data' => $formattedData], 200);
   }
    public function seller (Request $request, $id){

        $seller = User::find($id);

    $results = DB::table('users')
    ->join('products', 'users.id', '=', 'products.userID')
    ->select(
        'users.id',
        'users.name',
        'users.lastname',
        'products.id as product_id',
        'products.Product_Name',
        'products.price',
        'products.Quantity',
        'products.Description',
        'products.photos',
    )
    ->where('users.id', $id)
    ->get();

    // dd($results);

    return response()->json(['data' => $results], 200);
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
            if ($request->search) {
                $data = $data->where('Product_Name', 'LIKE', '%' . $request->search . '%');
            }

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

    public function replenishment_all(Request $request)
    {
        $userID = auth()->user()->id;

        // dd($emp_code);
        $data = Products::with('Category', 'Measurement')
            ->OrderBy('Quantity', 'desc')
            ->where('userID', $userID)
            ->where('Quantity', '<=', 10)
            ->get();

        return response()->json([
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
        Log::info('Request payload:', $request->all());
        // dd($request->measurement_id);
        // $measurement = $request->measurement_id['id'];

        // dd($request->measurement_id);
        $this->validate($request, []);
        $user = Products::findOrFail($id);

        $user->update([
            'Product_Name' => $request->Product_Name,
            'price' => $request->price,
            'Quantity' => $request->Quantity,
            'Description' => $request->Description,
            'idMeasurement' => $request->measurement_id,
        ]);

        // Handle photos
    if ($request->hasFile('photos')) {
        $photos = $request->file('photos');
        $photoPaths = [];
        foreach ($photos as $photo) {
            $photoPaths[] = $photo->store('product_photos', 'public'); // Save in 'storage/app/public/product_photos/photos'
        }
        $user->photos = json_encode($photoPaths); // Save as JSON or update accordingly
    }

    if ($request->hasFile('photos1')) {
        $photos1 = $request->file('photos1');
        $photo1Paths = [];
        foreach ($photos1 as $photo1) {
            $photo1Paths[] = $photo1->store('product_photos', 'public'); // Save in 'storage/app/public/product_photos/photos1'
        }
        $user->photos1 = json_encode($photo1Paths);
    }

    if ($request->hasFile('photos2')) {
        $photos2 = $request->file('photos2');
        $photo2Paths = [];
        foreach ($photos2 as $photo2) {
            $photo2Paths[] = $photo2->store('product_photos', 'public'); // Save in 'storage/app/public/product_photos/photos2'
        }
        $user->photos2 = json_encode($photo2Paths);
    }

    // Save the product
    $user->save();


        return response(['message' => 'success'], 200);
    }

    public function update1(Request $request, $id)
    {

        // dd($request->measurement_id);


        $this->validate($request, []);
        $user = Products::findOrFail($id);
        // dd($user);
        $user->update([
            'price' => $request->price,
            'Quantity' => $request->Quantity,
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
