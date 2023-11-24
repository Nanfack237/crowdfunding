<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{

    /**
     * @LRDparam first_name required|string|min:2|max:100
     * @LRDparam last_name required|string|min:2|max:100
     * @LRDparam email required|string|email|max:100
     * @LRDparam phone_number required|integer
     * @LRDparam country required|string|max:100
     * @LRDparam city required|string|max:100
     * @LRDparam password required|string|min:6
     * @LRDparam password_confirmation required|string|min:6
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function CreateUser(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|min:2|max:100',
                'last_name' => 'required|string|min:2|max:100',
                'email' => 'required|string|email|max:100|unique:users',
                'phone_number' => 'required|integer|min:2',
                'country' => 'required|string|min:2|max:100',
                'city' => 'required|string|min:2|max:100',
                'password' => 'required|string|confirmed|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = DB::select("SELECT * FROM users WHERE email = ?", [$request->email]);

            if (empty($user)) {
                $user = User::create([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'country' => $request->country,
                    'city' => $request->city,
                    'password' => bcrypt($request->password),
                    'status' => 'active'
                ]);

                $status = 201;
                $responses = [
                    'success' => 'User successfully registered',
                    'user' => $user
                ];
            } else {
                $status = 422;
                $responses = ['error' => 'User already exist or no user'];
            }

            return response()->json($responses, $status);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => 'User not registered ' . $exception->getMessage()
            ], 422);
        }
    }

    /**
     * @LRDparam email required|string|email|max:100
     * @LRDparam password required|string|min:6
     *
     * @param Request $request
     * @return JsonResponse
     */
                // Login
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $users = DB::select("SELECT * FROM users WHERE email = ?", [$request->email]);

            if (!empty($users)) {
                $user = $users[0];

                if (Hash::check($request->password, $user->password)) {
                    $token = Str::random(80);

                    DB::select("UPDATE users SET api_token = ? WHERE email = ?", [Hash::make($token), $user->email]);

                    Log::info("The user $user->id logged-in with token $token");

                    $status = 200;
                    $response = ['token' => "Bearer $token"];
                } else {
                    $status = 404;
                    $response = ['error' => 'Password mismatch'];
                }
            } else {
                $status = 404;
                $response = ['error' => 'User does not exist'];
            }

            return response()->json($response, $status);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => 'User not logged-in ' . $exception->getMessage()
            ], 422);
        }
    }

    /**
     * @LRDparam email required|string|email|max:100
     *
     * @param Request $request
     * @return JsonResponse
     */
            // Logout
    public function logout(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $users = DB::select("SELECT * FROM users WHERE email = ?", [$request->email]);
            if (!empty($users)) {
                $user = $users[0];

                if ($user->api_token) {
                    DB::select("UPDATE users SET api_token = ? WHERE email = ?", [null, $user->email]);

                    Log::info("The user $user->id has logged-out");

                    $status = 200;
                    $response = ['success' => 'You have been successfully logged out'];
                } else {
                    $status = 422;
                    $response = ['error' => 'User not logged-in'];
                }
            } else {
                $status = 404;
                $response = ['error' => 'User does not exist'];
            }

            return response()->json($response, $status);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'error' => 'User not logged-out ' . $exception->getMessage()
            ], 422);
        }
    }

}


