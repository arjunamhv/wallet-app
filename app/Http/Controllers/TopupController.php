<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\User;
use App\Http\Resources\TopupResource;
use App\Http\Requests\StoreTopupRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class TopupController extends Controller
{
    public function topup(StoreTopupRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $user = User::find($user->id);
        $balanceBefore = $user->balance;

        $user->balance += $data['amount'];
        $user->save();

        $topupData = [
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'balance_before' => $balanceBefore,
            'balance_after' => $user->balance,
        ];
        $topup = Topup::create($topupData);
        return response()->json([
            'status' => 'SUCCESS',
            'result' => new TopupResource($topup),
        ])->setStatusCode(200);
    }
}
