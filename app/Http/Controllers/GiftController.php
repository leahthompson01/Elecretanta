<?php

namespace App\Http\Controllers;

use App\Models\GiftExchange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GiftController extends Controller
{   
    public function index(Request $request) : Response
    {
        $gifts = GiftExchange::where('user_id',$request->user()->id)->get();

        return Inertia::render('/giftExchange', [
           'gifts' => $gifts
        ]);
    }

    public function store(Request $request) : RedirectResponse
    {
        $validated = $request->validate([
            'gifts' => 'required','array','max:500',
        ]);

        GiftExchange::create(['user_id' => $request->user_id, 'gifts' => $validated['gifts']]);
        return redirect()->route('giftExchange.index');
    }
}