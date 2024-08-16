<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\User;
use App\Http\Resources\TransferResource;
use App\Http\Requests\StoretransferRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransferController extends Controller
{
    public function transfer(StoreTransferRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $user = User::find($user->id);
        $balanceBefore = $user->balance;

        if ($balanceBefore < $data['amount']) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Insufficient balance for this transfer.',
                ], 400)
            );
        }

        $targetUser = User::find($data['target_user_id']);

        if (!$targetUser){
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Recipient not found.',
                ], 400)
            );
        }

        $user->balance -= $data['amount'];
        $user->save();
        $targetUser->balance += $data['amount'];
        $targetUser->save();

        $transferData = [
            'user_id' => $user->id,
            'target_user_id' => $data['target_user_id'],
            'amount' => $data['amount'],
            'remarks' => $data['remarks'],
            'balance_before' => $balanceBefore,
            'balance_after' => $user->balance,
        ];
        $transfer = transfer::create($transferData);
        return response()->json([
            'status' => 'SUCCESS',
            'result' => new TransferResource($transfer),
        ])->setStatusCode(200);
    }
}
