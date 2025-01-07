<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\Activation;
use App\Mail\ApprovalConfirmation;
use App\Mail\Deactivation;
use App\Mail\Disapproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('list user'), 403, 'You do not have the required authorization.');
        $data = User::withTrashed()->with('roles', 'permissions')->latest();
        if ($request->search) {
            $data = $data->where('name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->filter == 'Deactivate') {
            $data = $data->where('deleted_at', null);
        }
        if ($request->filter == 'Activate') {
            $data = $data->whereNotNull('deleted_at');
        }
        $data = $data->paginate($request->length);
        // dd($data);
        return response(['data' => $data], 200);
    }

    public function register(Request $request){

        // Handle file uploads (photos)
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Generate a unique filename using current timestamp
                $filename = time() . '.' . $photo->getClientOriginalExtension();

                // Store the file in 'Personal_Info_Photo' directory, using 'public' disk
                $path = $photo->storeAs('Personal_Info_Photo', $filename, 'public');

                // Remove the 'public/' prefix from the path and store the relative path
                $photos[] = str_replace('public/', '', $path); // Format path as required
            }
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'lastname' => $request->lastname,
            'middle_initial' => $request->middle_initial ?? null,
            'date_of_birth' => $request->date_of_birth,
            'contact_number' => $request->contact_number,
            'telephone_number' => $request->telephone_number ?? null,
            'address' => $request->address,
            'photos' => json_encode($photos),  // Store photo paths as JSON=
        ]);

        // Return response
        return response()->json([
            'status' => 'success',
            'message' => 'User successfully registered.',
            'user' => $user


        ], 201);
    }


    private function uploadFile($file)
    {
        // Assuming file is uploaded via the request and stored in a directory
        $storagePath = 'storage/Personal_Info_Photo/';
        $filename = uniqid() . '-' . basename($file['name']);
        $targetPath = $storagePath . $filename;

        // Move the uploaded file to the target path
        if (move_uploaded_file($file['tmp_name'], public_path($targetPath))) {
            return $targetPath;
        }

        return null;
    }

    public function index_all(Request $request){
        $users = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id') // Join with the roles table
                ->whereNotNull('users.approved_at')
                ->whereIn('roles.name', ['Buyer', 'Seller']) // Add condition for role names
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.lastname as user_lastname',
                    'model_has_roles.role_id',
                    'roles.name as role_name' // Retrieve the role name
                )
                ->get();

    return response()->json([
        'data' => $users,
        'message' => 'Users retrieved successfully',
    ], 200);
    }

    public function all_buyer(Request $request){
        $users = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id') // Join with the roles table
                ->whereNotNull('users.approved_at')
                ->whereIn('roles.name', ['Buyer', 'Seller']) // Add condition for role names
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.lastname as user_lastname',
                    'model_has_roles.role_id',
                    'roles.name as role_name' // Retrieve the role name
                )
                ->get();

    return response()->json([
        'data' => $users,
        'message' => 'Users retrieved successfully',
    ], 200);
    }

    public function show()
    {
        abort_if(Gate::denies('edit user'), 403, 'You do not have the required authorization.');
        $data = User::with('roles', 'permissions')->where('id', Auth::user()->id)->get();

        return response(['data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



        // dd($request->all());
        $this->validate($request, [
            'name' => 'required|string|unique:users,name,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'required|sometimes',
            'lastname' => 'required|string',
            'middle_initial' => 'nullable|string|max:2',
            'date_of_birth' => 'required|date',
            'contact_number' => 'required|string|digits:11',
            'telephone_number' => 'nullable|string|digits:7',
            'address' => 'required|string',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('Personal_Info_Photo', 'public');
                $photoPaths[] = $path;
            }
        }

        $qrcode = [];

        if ($request->hasFile('qrcode')) {
            foreach ($request->file('qrcode') as $qrcodes) {
                $path = $qrcodes->store('QRCode', 'public');
                $qrcode[] = $path;
            }
        }

        // dd($photoPaths);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // 'password' => Hash::make($request->password),
            'password' => $request->password,
            'lastname' => $request->lastname,
            'middle_initial' => $request->middle_initial,
            'date_of_birth' => $request->date_of_birth,
            'contact_number' => $request->contact_number,
            'telephone_number' => $request->telephone_number,
            'address' => $request->address,
            'photos' => json_encode($photoPaths),
            'qrcode' => json_encode($qrcode),
        ]);
        // dd($request->all());
        if (isset($request->roles['name'])) {
            $user->assignRole($request->roles['name']);

            foreach ($request->permissions as $permission) {
                $user->givePermissionTo($permission['name']);
            }
        }

        return response(['message' => 'success'], 200);
    }

        public function authenticate(Request $request)
        {
            // dd($request->email);
            $email = $request->email;
            // $user = User::find($email);


            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $results = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id' )
            ->where('users.id', $user->id)
            ->select('users.id as user_id',
                    'users.name',
                    'users.lastname',
                    'users.email',
                    'users.password',
                    'users.contact_number',
                    'users.address',
                    'model_has_roles.role_id as role_id',
                    'roles.name as role_name')
            ->get();

        return response()->json([
            'user' => $results,
        ], 200);
        }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validated(Request $request, $id)
    {
        //
        if ($request->selectedOption == 'approve') {
            $user = User::findOrFail($id);

            $user->update([
                'approved_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            // dd($user);

            // $ipAddress = $request->server('SERVER_ADDR', 'localhost');
            // dd($ipAddress);
            Mail::to($user->email)->send(new ApprovalConfirmation($user));
        } else {
            // dd($request->txtdesapproval);
            $user = User::findOrFail($id);

            $user->update([
                'reason_of_disapproval' => $request->txtdesapproval
            ]);

            Mail::to($user->email)->send(new Disapproval($user));
        }

        return response(['message' => 'success'], 200);
    }
    public function disapprove($id)
    {
        //
        $user = User::findOrFail($id);

        $user->update([
            'approved_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        return response(['message' => 'success'], 200);
    }

    public function all(Request $request){

        $user = User::all();

        return response()->json([
            'user' => $user,
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|string|unique:users,name,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'required|sometimes',
            'lastname' => 'required|string',
            'date_of_birth' => 'required|date',
            'contact_number' => 'required|string|digits:11',
            'address' => 'required', 'string',// validate each uploaded file
        ]);
        $photoPaths = [];
        if ($request->hasFile('user_photo')) {
            foreach ($request->file('user_photo') as $photo) {
                $path = $photo->store('user_photo', 'public');
                $photoPaths[] = $path;
            }
        }
        $photoPaths1 = [];
        if ($request->hasFile('qrcode')) {
            foreach ($request->file('qrcode') as $photo) {
                $path = $photo->store('QRCode', 'public');
                $photoPaths1[] = $path;
            }
        }
        $photoPaths2 = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('Personal_Info_Photo', 'public');
                $photoPaths2[] = $path;
            }
        }
        // dd(json_encode($photoPaths));

        $user = User::findOrFail($request->id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'lastname' => $request->lastname,
            'middle_initial' => $request->middle_initial,
            'date_of_birth' => $request->date_of_birth,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'telephone_number' => $request->telephone_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

          // Only update the user_photo if there is a new file
            if (!empty($photoPaths)) {
                $user['user_photo'] = json_encode($photoPaths);
            }

            // Only update the qrcode if there is a new file
            if (!empty($photoPaths1)) {
                $user['qrcode'] = json_encode($photoPaths1);
            }

            if (!empty($photoPaths2)) {
                $user['photos'] = json_encode($photoPaths2);
            }

            // Update user data
            $user->update();

        if ($request->password) {
            // $user->password = Hash::make($request->password);
            $user->password  = $request->password;
            $user->save();
        }
        return response(['message' => 'success'], 200);
    }

    public function assign(Request $request)
    {
        // dd(json_encode($photoPaths));

        $user = User::findOrFail($request->id);

        if ($request->password) {
            // $user->password = Hash::make($request->password);
            $user->password  = $request->password;
            $user->save();
        }

        if (isset($request->roles['name'])) {
            $user->roles()->detach();
            $user->assignRole($request->roles['name']);

            foreach ($user->permissions as $permission) {
                $user->revokePermissionTo($permission['name']);
            }
            foreach ($request->permissions as $permission) {
                $user->givePermissionTo($permission['name']);
            }
        }

        return response(['message' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        Mail::to($user->email)->send(new Deactivation($user));
        return response(['message' => 'success'], 200);
    }
    public function activate($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        Mail::to($user->email)->send(new Activation($user));
        return response(['message' => 'success'], 200);
    }
}
