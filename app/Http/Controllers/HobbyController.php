<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class HobbyController extends Controller
{
    public function createHobby(Request $request)
{
    $customMessages = [
        'name.unique' => 'This hobby name is already taken. Please choose a different one.',
    ];

    // Validate input
    $req_body = $request->validate([
        "name" => ["required", "string", 'max:30'],
    ], $customMessages);

    try {
        $createdHobby = Hobby::create(['name' => $req_body['name']]);
        return response()->json($createdHobby, 201);
    } catch (QueryException $e) {
        // Check if it's a duplicate entry error
        if ($e->getCode() == 23000) {
            return response()->json([
                'error' => "Failed to create hobby",
                'message' => 'This hobby name is already taken. Please choose a different one.'
            ], 400);
        }
        // Handle other types of exceptions
        return response()->json([
            'error' => "Failed to create hobby",
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function fetchAllHobbies(Request $request)
    {
        $hobbies = Hobby::all();
        return response()->json($hobbies,200);
    }
       
}
