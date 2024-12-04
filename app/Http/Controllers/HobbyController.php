<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use Illuminate\Http\Request;

// class HobbyController extends Controller
// {
//     public function createHobby(Request $request)
//     {
//         echo "We made it";
//         $req_body = $request->validate([
//             "name" => ["required","string"],
//         ]);

//         $createdHobby = Hobby::create(attributes: $req_body);

//         return response()->json(data: $createdHobby);
//     }

       
// }

class HobbyController extends Controller
{
    public function createHobby(Request $request)
    {
        echo "We made it";
    }
}