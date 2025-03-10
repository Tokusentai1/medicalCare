<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Login api.
     */
    public function login(Request $request)
    {
        $email = strtolower($request->email);
        $user = User::where("email", $email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 404,
                    "error" => "No User found with the given email and password",
                    "result" => null
                ]
            );
        } else {

            return response()->json(
                [
                    "success" => true,
                    "statusCode" => 200,
                    "error" => null,
                    "result" => [
                        'id' => $user->id,
                        'name' => $user->fullName,
                        'email' => $user->email,
                        'phone number' => $user->phone_number,
                        'gender' => $user->gender,
                        'birth date' => $user->birth_date
                    ],
                ]
            );
        }
    }

    /**
     * Register api.
     */
    public function register(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'phone_number' => 'required',
                'gender' => 'required',
                'birth_date' => 'required',
            ]
        );

        if ($validation->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => $validation->errors(),
                    "result" => null
                ]
            );
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "Email already exists",
                    "result" => null
                ]
            );
        }

        if (User::where('phone_number', $request->phone_number)->exists()) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "Phone number already exists",
                    "result" => null
                ]
            );
        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->phone_number = $request->phone_number;
        $user->gender = $request->gender;
        $user->birth_date = $request->birth_date;
        $user->save();

        return response()->json(
            [
                "success" => true,
                "statusCode" => 201,
                "error" => null,
                "result" => $user
            ]
        );
    }

    /**
     * Display the specified user info via user id.
     */
    public function show(int $id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json(
                [
                    "success" => true,
                    "statusCode" => 200,
                    "error" => null,
                    "result" => [
                        'id' => $user->id,
                        'name' => $user->fullName,
                        'email' => $user->email,
                        'phone number' => $user->phone_number,
                        'gender' => $user->gender,
                        'birth date' => $user->birth_date
                    ],
                ]
            );
        }
        return response()->json(
            [
                "success" => false,
                "statusCode" => 404,
                "error" => "User not found",
                "result" => null
            ]
        );
    }

    /**
     * Update the specified user info via user id.
     */
    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($request->all());
            return response()->json(
                [
                    "success" => true,
                    "statusCode" => 200,
                    "error" => null,
                    "result" => $user
                ]
            );
        }
        return response()->json(
            [
                "success" => false,
                "statusCode" => 404,
                "error" => "User not found",
                "result" => null
            ]
        );
    }

    /**
     * Delete the specified user via user id.
     */
    public function destroy(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 404,
                    "error" => "User not found",
                    "result" => null
                ]
            );
        }
        $user->delete();
        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => "User deleted successfully"
            ]
        );
    }
}
