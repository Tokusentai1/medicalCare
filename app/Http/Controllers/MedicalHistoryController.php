<?php

namespace App\Http\Controllers;

use App\Models\MedicalHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicalHistoryController extends Controller
{

    /**
     * Store a user medical history.
     */
    public function store(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'allergies' => 'sometimes|nullable|array',
                'previousSurgeries' => 'sometimes|nullable|array',
                'pastMedicalConditions' => 'sometimes|nullable|array',
                'user_id' => 'required|exists:users,id',
            ]
        );

        if ($validation->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'statusCode' => 422,
                    'error' => $validation->errors(),
                    'result' => 'Validation failed',
                ]
            );
        } else {
            $medical_history = new MedicalHistory();
            $medical_history->allergies = $request->allergies;
            $medical_history->previous_surgeries = $request->previousSurgeries;
            $medical_history->past_medical_condition = $request->pastMedicalConditions;
            $medical_history->user_id = $request->user_id;
            $medical_history->save();

            return response()->json(
                [
                    'success' => true,
                    'statusCode' => 200,
                    'error' => null,
                    'result' => $medical_history,
                ],
            );
        }
    }

    /**
     * Display the specified user medical history via user id.
     */
    public function show(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "There is no User with this ID " . $id,
                    "result" => null
                ]
            );
        }

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $user->medicalHistories
            ]
        );
    }

    /**
     * Update the specified user medical history via user id.
     */
    public function update(Request $request, int $id)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'allergies' => 'sometimes|nullable|array',
                'previousSurgeries' => 'sometimes|nullable|array',
                'pastMedicalConditions' => 'sometimes|nullable|array',
            ]
        );

        if ($validation->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'statusCode' => 422,
                    'error' => $validation->errors(),
                    'result' => 'Validation failed',
                ]
            );
        }

        $medical_history = MedicalHistory::where('user_id', $id)->first();

        if (!$medical_history) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "No medical history found for this user",
                    "result" => null
                ]
            );
        }

        $medical_history->fill(array_filter($request->only(['allergies', 'previousSurgeries', 'pastMedicalConditions']), function ($value) {
            return $value !== null;
        }));

        $medical_history->save();

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $medical_history
            ]
        );
    }


    /**
     * Remove the specified user medical history via user id.
     */
    public function destroy(int $id)
    {
        $medical_history = MedicalHistory::where('user_id', $id)->first();

        if (!$medical_history) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "No medical history found for this user",
                    "result" => null
                ]
            );
        }
        $medical_history->delete();

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => "Medical history deleted"
            ]
        );
    }
}
