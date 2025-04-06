<?php

namespace App\Http\Controllers;

use App\Models\MedicalHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Login api.
     */
    public function login(Request $request)
    {
        Log::info('begin login');
        $validation = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        if ($validation->fails()) {
            Log::error('Validation errors:', $validation->errors()->toArray());
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => $validation->errors(),
                    "result" => null
                ]
            );
        }

        Log::info('finished validation part and no Errors time to check email and password');
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
            Log::info('user found');
            $token = $user->createToken($user->fullName . 'token')->plainTextToken;
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
                        'birth date' => $user->birth_date,
                        'address' => $user->address,
                        'token' => $token
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
        Log::info('begin register');
        $validation = Validator::make(
            $request->all(),
            [
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'phone' => 'required',
                'gender' => 'required',
                'dob' => 'required',
                'residence' => 'required',
                'allergies' => 'sometimes|nullable|array',
                'previousSurgeries' => 'sometimes|nullable|array',
                'pastMedicalConditions' => 'sometimes|nullable|array',
            ]
        );

        Log::info('finished validation part of the code now to check for errors');

        if ($validation->fails()) {
            Log::error('Validation errors:', $validation->errors()->toArray());
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => $validation->errors(),
                    "result" => null
                ]
            );
        }

        Log::info('check validation errors and nothing is error now the check if phone or email exists');
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

        if (User::where('phone_number', $request->phone)->exists()) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "Phone number already exists",
                    "result" => null
                ]
            );
        }

        Log::info('finished checking now to create user and medical history');
        $user = new User();
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->phone_number = $request->phone;
        $user->gender = $request->gender;
        $user->birth_date = \Carbon\Carbon::parse($request->dob)->format('Y-m-d');
        $user->address = $request->residence;
        $user->save();

        $medicalHistory = new MedicalHistory();
        $medicalHistory->user_id = $user->id;
        $medicalHistory->allergies = $request->allergies;
        $medicalHistory->previous_surgeries = $request->previousSurgeries;
        $medicalHistory->past_medical_condition = $request->pastMedicalConditions;
        $medicalHistory->save();

        $token = $user->createToken($user->fullName . 'token')->plainTextToken;

        Log::info('now to send the data in the response');
        return response()->json(
            [
                "success" => true,
                "statusCode" => 201,
                "error" => null,
                "result" => [
                    'user' => $user,
                    'medical_history' => $medicalHistory,
                    'token' => $token
                ]
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
                        'phone' => $user->phone_number,
                        'gender' => $user->gender,
                        'address' => $user->address,
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
        $validation = Validator::make(
            $request->all(),
            [
                'firstName' => 'sometimes|string',
                'lastName' => 'sometimes|string',
                'email' => 'sometimes|email',
                'password' => 'sometimes|string',
                'gender' => 'sometimes|string',
                'residence' => 'sometimes|string',
                'phone' => 'sometimes|string',
                'dob' => 'sometimes',
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
