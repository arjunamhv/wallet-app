<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\AuthResource;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (User::where('phone_number', $data['phone_number'])->count() == 1) {
            throw new HttpResponseException(response(['errors' => ['phone_number' => ['The Phone Number field has already been taken.']]], 422));
        }

        $data['balance'] = '0';

        $user = new User($data);
        $user->save();
        return response()->json([
            'status' => 'SUCCESS',
            'result' => new UserResource($user),
        ])->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('phone_number', $data['phone_number'])->first();
        if (!$user || $user->pin != $data['pin']) {
            throw new HttpResponseException(response(['errors' => ['message' => ['Invalid phone number or pin']]], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();
        return response()->json([
            'status' => 'SUCCESS',
            'result' => new AuthResource($user),
        ])->setStatusCode(201);
    }

    public function get(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $user = User::find($user->id);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->pin = $data['pin'];
        $user->address = $data['address'];

        $user->save();
        return response()->json([
            'status' => 'SUCCESS',
            'result' => new UserResource($user),
        ])->setStatusCode(200);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $user->token = null;
        $user->save();
        return response()->json([
            'data' => 'true'
        ])->setStatusCode(200);
    }
}
