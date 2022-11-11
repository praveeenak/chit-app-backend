<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coordinator;
use Illuminate\Http\Request;

class CoordinatorController extends Controller
{

    public function index()
    {
        try {
            $coordinators = Coordinator::all()->sortByDesc('created_at')->values()->all();

            return response()->json([
                'coordinators' => $coordinators,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'success' => false,
                'message' => 'Coordinators could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required',
                'phone' => 'required | unique:coordinators',
            ]);
            $coordinator = Coordinator::create($request->all());
            return response()->json([
                'success' => true,
                'coordinator' => $coordinator,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function show($id)
    {
        try {
            $coordinator = Coordinator::findOrFail($id);
            return response()->json([
                'coordinator' => $coordinator,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Coordinator could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $coordinator = Coordinator::findOrFail($id);
            $coordinator->update($request->all());
            return response()->json([
                'coordinator' => $coordinator,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Coordinator could not be updated',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function destroy($id)
    {
        try {
            $coordinator = Coordinator::findOrFail($id);
            $coordinator->delete();
            return response()->json([
                'message' => 'Coordinator deleted successfully',
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Coordinator could not be deleted',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
