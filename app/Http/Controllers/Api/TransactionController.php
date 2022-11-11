<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chitty;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function index($month)
    {
        try {
            if ($month == 'all') {
                $transactions = Transaction::all()->sortByDesc('created_at')->values()->all();

            } else {
                $transactions = Transaction::whereMonth('created_at', $month)->orderBy('created_at', 'desc')->get();
            }
            foreach ($transactions as $transaction) {
                $customer = Customer::withTrashed()->where('id', $transaction->customer_id)->first();
                $name = $customer->name;
                $phone = $customer->phone;
                $customer = [
                    'name' => $name,
                    'phone' => $phone,
                ];
                $transaction->customer = $customer;
            }

            return response()->json([
                'transactions' => $transactions,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Transactions could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $customer = Customer::where('id', $request->customer_id)->first();
            $transactions = Transaction::whereMonth('created_at', date('m'))->get();
            foreach ($transactions as $transaction) {
                if ($transaction->customer_id == $customer->id) {
                    return response()->json([
                        'message' => 'Transaction is already created for this month',
                    ], 409);
                }
            }

            $request->validate([
                'customer_id' => 'required',
                'amount' => 'required',
            ]);
            $data = new Transaction();
            $data->customer_id = $request->customer_id;
            $data->amount = $request->amount;
            $data->chitty_id = Chitty::getDefaultChittyId();
            $data->save();
            $transactionData = [];

            $customer = Customer::withTrashed()->where('id', $data->customer_id)->first();
            $name = $customer->name;
            $phone = $customer->phone;
            $chitty_number = $customer->chitty_number;
            $customer = [
                'name' => $name,
                'phone' => $phone,
                'chitty_number' => $chitty_number,
            ];
            $transactionData = [
                'id' => $data->id,
                'customer_id' => $data->customer_id,
                'amount' => $data->amount,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
                'transaction_no' => $data->transaction_no,
                'customer' => $customer,
            ];

            // foreach ($transactions as $transaction) {
            //     $customer = Customer::withTrashed()->where('id', $transaction->customer_id)->first();
            //     $name = $customer->name;
            //     $phone = $customer->phone;
            //     $customer = [
            //         'name' => $name,
            //         'phone' => $phone,
            //     ];
            //     $transactionData = [
            //         'id' => $transaction->id,
            //         'customer_id' => $transaction->customer_id,
            //         'amount' => $transaction->amount,
            //         'created_at' => $transaction->created_at,
            //         'updated_at' => $transaction->updated_at,
            //         'transaction_no' => $transaction->transaction_no,
            //         'customer' => $customer,
            //     ];

            // }
            return response()->json([
                'transactions' => $transactionData,
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
            $transaction = Transaction::findOrFail($id);
            $customer = Customer::findOrFail($transaction->customer_id);
            return response()->json([
                'transaction' => $transaction,
                'customer' => $customer,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Transaction could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsSelected(Transaction $transaction)
    {

        try {
            $transactions = Transaction::whereMonth('created_at', date('m'))->get();
            foreach ($transactions as $transaction) {
                $transaction->is_selected = 0;
                $transaction->save();
            }
            $transaction->is_selected = 1;
            $transaction->save();
            return response()->json([
                'message' => 'Transaction marked as selected',
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Transaction could not be marked as selected',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSelectedTransactions()
    {
        try {
            $transactions = Transaction::where('is_selected', true)->get();
            foreach ($transactions as $transaction) {
                $customer = Customer::where('id', $transaction->customer_id)->first();
                $name = $customer->name;
                $phone = $customer->phone;
                $customer = [
                    'name' => $name,
                    'phone' => $phone,
                ];
                $transaction->customer = $customer;
            }
            return response()->json([
                'transactions' => $transactions,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Transactions could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTransactionByCustomerId($id)
    {
        try {
            $transactions = Transaction::where('customer_id', $id)->get();
            $transactionData = [];
            foreach ($transactions as $transaction) {
                $amount = $transaction->amount;
                $date = date('Y-M-d', strtotime($transaction->created_at));
                $transactionNo = $transaction->transaction_no;
                $totalAmount = 0;
                $totalAmount += $amount;
                $transactionData[] = [
                    'amount' => $amount,
                    'date' => $date,
                    'transaction_no' => $transactionNo,
                    'totalAmount' => $totalAmount,
                ];

            }
            return response()->json([
                'transactions' => $transactionData,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Transactions could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDueCustomers()
    {
        try {

            $customers = Customer::all();
            $dueCustomers = [];
            foreach ($customers as $customer) {
                $transactions = Transaction::where('customer_id', $customer->id)->get();
                if (count($transactions) > 0) {
                    $totalAmount = 0;
                    foreach ($transactions as $transaction) {
                        $totalAmount += $transaction->amount;
                    }
                    if ($totalAmount < $customer->due_amount) {
                        $dueCustomers[] = $customer;
                    }
                } else {
                    $dueCustomers[] = $customer;
                }
            }
            return response()->json([
                'due_customers' => $dueCustomers,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => 'Transactions could not be retrieved',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
