<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chitty;
use App\Models\ChittyWinner;
use Illuminate\Http\Request;

class ChittyController extends Controller
{
    public function createChitty(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'chitty_name' => 'required',
            ]);
            $chitty = Chitty::create($request->all());
            return response()->json([
                'chitty' => $chitty,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Chitty could not be created',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function getChittyCount()
    {
        try {
            $chittyCount = Chitty::count();
            return response()->json([
                'chittyCount' => $chittyCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Chitty count could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getChitties()
    {
        try {
            $chitties = Chitty::all()->sortByDesc('created_at')->values()->all();
            return response()->json([
                'chitties' => $chitties,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Chitties could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsChittyWinner(Request $request)
    {
        try {
            $chittyWinners = ChittyWinner::whereMonth('created_at', date('m'))->get();
            if ($chittyWinners->count() > 0) {
                return response()->json([
                    'message' => 'Chitty Winner is already marked for this month',
                ], 409);
            }
            foreach ($chittyWinners as $chittyWinner) {
                if ($chittyWinner->customer_id == $request->customer_id) {
                    return response()->json([
                        'message' => 'Customer is already marked as winner for this month',
                    ], 409);
                }
            }
            $request->validate([
                'customer_id' => 'required',
                'chitty_id' => 'required',
            ]);
            $chittyWinner = ChittyWinner::create($request->all());
            return response()->json([
                'chittyWinner' => $chittyWinner,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Chitty winner could not be marked',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

}

// $chittyWinners = ChittyWinner::whereMonth('created_at', date('m'))->get();
//             foreach ($chittyWinners as $chittyWinner) {
//                 if ($chittyWinner->customer_id == $request->customer_id) {
//                     return response()->json([
//                         'message' => 'Customer is already marked as winner for this month',
//                     ], 409);
//                 }
//             }
//             $request->validate([
//                 'customer_id' => 'required',
//             ]);
//             $chittyWinner = ChittyWinner::create($request->all());
