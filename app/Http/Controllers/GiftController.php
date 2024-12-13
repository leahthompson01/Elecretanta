<?php

namespace App\Http\Controllers;

use App\Models\GiftExchange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GiftController extends Controller
{
    public function store(Request $request) : RedirectResponse
    {
        $priorities = 'low'| 'medium' | 'high';
        $validated = $request->validate([
            'user_id' => 'required','string','max:50',
            'gifts' => 'required','array','max:500',
        ]);
        Todo::create(['id' => $request->user()->id, 'user_id' => $request->user_id, 'gifts' => $request->gifts]);
        return redirect()->route('todos.index');
    }
}