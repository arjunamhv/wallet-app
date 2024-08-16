<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Http\Resources\PaymentResource;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;


class PaymentController extends Controller
{
    public function payment(StorePaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $user = User::find($user->id);
        $balanceBefore = $user->balance;

        if ($balanceBefore < $data['amount']) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Insufficient balance for this payment.',
                ], 400)
            );
        }

        $user->balance -= $data['amount'];
        $user->save();

        $paymentData = [
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'remarks' => $data['remarks'],
            'balance_before' => $balanceBefore,
            'balance_after' => $user->balance,
        ];
        $payment = payment::create($paymentData);
        return response()->json([
            'status' => 'SUCCESS',
            'result' => new PaymentResource($payment),
        ])->setStatusCode(200);
    }
}
