<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChittyWinner;
use App\Models\Coordinator;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function index()
    {

        try {

            $customers = Customer::all()->sortByDesc('updated_at')->values()->all();

            foreach ($customers as $customer) {
                $coordinator = Coordinator::where('id', $customer->c_id)->first();
                $customer->coordinator = $coordinator;
            }
            return response()->json([
                'customers' => $customers,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([

                'message' => 'Customers could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required',
                'phone' => 'required | unique:customers',
            ]);
            $customer = Customer::create($request->all());
            return response()->json([
                'customer' => $customer,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Customer could not be created',
                'error' => $e->getMessage(),
            ], 409);
        }
    }

    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return response()->json([
                'customer' => $customer,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Customer could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $customer = Customer::findOrFail($id);
            $customer->update($request->all());
            return response()->json([
                'message' => 'Customer updated successfully',
                'data' => $customer,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Customer could not be updated',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return response()->json([
                'message' => 'Customer deleted successfully',
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Customer could not be deleted',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function dueCustomers()
    {
        try {
            $transactions = Transaction::whereMonth('created_at', date('m'))->get();
            $chitty_winners = ChittyWinner::whereMonth('created_at', date('m'))->get();
            $customers = Customer::all();
            $dueCustomers = [];

            //remove customer in the chitty winner list
            foreach ($chitty_winners as $chitty_winner) {
                foreach ($customers as $key => $customer) {
                    if ($customer->id == $chitty_winner->customer_id) {
                        unset($customers[$key]);
                    }
                }
            }

            foreach ($customers as $customer) {
                $due = true;
                foreach ($transactions as $transaction) {
                    if ($transaction->customer_id == $customer->id) {
                        $due = false;
                    }
                }
                if ($due) {
                    $coordinator = Coordinator::where('id', $customer->c_id)->first();
                    $customer->coordinator = $coordinator;
                    array_push($dueCustomers, $customer);
                }
            }

            return response()->json([
                'customers' => $dueCustomers,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Due Customers could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //get chitty winner customers
    public function winners()
    {
        try {
            $chitty_winners = ChittyWinner::whereMonth('created_at', date('m'))->get();
            $customers = Customer::withTrashed()->get();
            $chittyWinnerCustomers = [];
            foreach ($chitty_winners as $chitty_winner) {
                foreach ($customers as $key => $customer) {
                    if ($customer->id == $chitty_winner->customer_id) {
                        $coordinator = Coordinator::where('id', $customer->c_id)->first();
                        $customer->coordinator = $coordinator;
                        array_push($chittyWinnerCustomers, $customer);
                    }
                }
            }
            return response()->json([
                'customers' => $chittyWinnerCustomers,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Winner could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
