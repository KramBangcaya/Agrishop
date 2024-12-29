<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private $model;
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function index(Request $request){
        $userID = auth()->user()->id;

        return response([
            'userID' => $userID,
        ], 200);
    }
}
