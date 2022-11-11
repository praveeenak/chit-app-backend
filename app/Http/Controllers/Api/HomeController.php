<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChittyWinner;
use App\Models\Coordinator;
use App\Models\Customer;
use App\Models\Transaction;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $coordinators = Coordinator::all();
            $customers = Customer::all();
            $totalCustomers = Customer::all();
            $transactions = Transaction::all();
            $chitty_winners = ChittyWinner::whereMonth('created_at', date('m'))->get();

            foreach ($chitty_winners as $chitty_winner) {
                foreach ($customers as $key => $customer) {     
                    if ($customer->id == $chitty_winner->customer_id) {
                        unset($customers[$key]);
                    }
                }
            }

            $total_coordinators = count($coordinators);
            $total_customers = count($totalCustomers);
            $total_transactions_amount = 0;
            foreach ($transactions as $transaction) {
                $total_transactions_amount += $transaction->amount;
            }

            $due_customers = 0;
            foreach ($customers as $customer) {
                $transactions = Transaction::where('customer_id', $customer->id)->get();

                if (count($transactions) > 0) {

                    $transaction = Transaction::whereMonth('created_at', date('m'))->where('customer_id', $customer->id)->first();
                    if ($transaction == null) {
                        $due_customers++;
                    }
                } else {
                    $due_customers++;
                }
            }

            return response()->json([
                'total_coordinators' => $total_coordinators,
                'total_customers' => $total_customers,
                'total_transactions_amount' => $total_transactions_amount,
                'due_customers' => $due_customers,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

    }

}
