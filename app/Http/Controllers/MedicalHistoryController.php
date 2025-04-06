<?php

namespace App\Http\Controllers;

use App\Models\MedicalHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

    /**
     * Add the Medicines that the user takes
     */
    public function addUserMedicines(Request $request)
    {
        // Step 1: Validate input
        $validation = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'medicines' => 'required|array',
            ]
        );

        Log::info('Finished validation part of the code, now checking for errors');

        if ($validation->fails()) {
            Log::error('Validation errors:', $validation->errors()->toArray());
            return response()->json([
                "success" => false,
                "statusCode" => 400,
                "error" => $validation->errors(),
                "result" => null
            ]);
        }

        Log::info('Validation passed. Proceeding to fetch or create medical history.');

        // Step 2: Get or create the user's medical history
        $medicalHistory = MedicalHistory::firstOrCreate(
            ['user_id' => $request->user_id],
            ['medications' => []] // Default if no record exists
        );

        Log::info('Medical history retrieved or created successfully.');

        // Step 3: Normalize and deduplicate medications
        $existingMeds = array_map(fn($item) => strtolower(trim($item)), $medicalHistory->medications ?? []);
        $newMeds = array_map(fn($item) => strtolower(trim($item)), $request->medicines);

        $mergedMeds = array_unique(array_merge($existingMeds, $newMeds));

        $medicalHistory->medications = $mergedMeds;

        // Step 4: Save the updated medical history
        $medicalHistory->save();

        Log::info('Medical history updated successfully.');

        // Step 5: Return the response
        return response()->json([
            "success" => true,
            "statusCode" => 200,
            "error" => null,
            "result" => $medicalHistory
        ]);
    }
}
