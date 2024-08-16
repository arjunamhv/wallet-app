<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Topup;
use App\Models\Payment;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function transaction(): JsonResponse
    {
        $user = Auth::user();

        $topups = Topup::where('user_id', $user->id)->get()->map(function ($transaction) {
            $transaction->type = 'topup';
            return $transaction;
        });

        $payments = Payment::where('user_id', $user->id)->get()->map(function ($transaction) {
            $transaction->type = 'payment';
            return $transaction;
        });

        $transfers = Transfer::where('user_id', $user->id)->get()->map(function ($transaction) {
            $transaction->type = 'transfer';
            return $transaction;
        });

        $transactions = $topups->concat($payments)->concat($transfers);

        $sortedTransactions = $transactions->sortByDesc('created_at')->values();

        return response()->json([
            'status' => 'SUCCESS',
            'result' => $sortedTransactions,
        ])->setStatusCode(200);
    }
}
