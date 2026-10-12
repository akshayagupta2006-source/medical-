<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalCaseRequest;
use App\Http\Requests\UpdateMedicalCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CaseApiController extends Controller
{
    /**
     * Display a listing of cases.
     */
    public function index(Request $request): JsonResponse
    {
        if (! Gate::allows('viewAny', MedicalCase::class)) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ], 403);
        }

        $query = MedicalCase::with('creator:id,name,email,role')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('surgeon_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), MedicalCase::STATUSES)) {
            $query->where('status', $request->input('status'));
        }

        $cases = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Cases retrieved successfully.',
            'data' => $cases,
        ], 200);
    }

    /**
     * Store a newly created case in storage.
     */
    public function store(StoreMedicalCaseRequest $request): JsonResponse
    {
        if (! $request->user()->can('create', MedicalCase::class)) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ], 403);
        }

        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $case = MedicalCase::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medical case created successfully.',
            'data' => $case->load('creator:id,name,email,role'),
        ], 201);
    }

    /**
     * Display the specified case.
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $case = MedicalCase::with('creator:id,name,email,role')->find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'message' => 'Medical case not found.',
                'data' => null,
            ], 404);
        }

        if (! $request->user()->can('view', $case)) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Medical case retrieved successfully.',
            'data' => $case,
        ], 200);
    }

    /**
     * Update the specified case in storage.
     */
    public function update(UpdateMedicalCaseRequest $request, int|string $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'message' => 'Medical case not found.',
                'data' => null,
            ], 404);
        }

        if (! $request->user()->can('update', $case)) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ], 403);
        }

        $case->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Medical case updated successfully.',
            'data' => $case->fresh()->load('creator:id,name,email,role'),
        ], 200);
    }

    /**
     * Remove the specified case from storage.
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $case = MedicalCase::find($id);

        if (! $case) {
            return response()->json([
                'success' => false,
                'message' => 'Medical case not found.',
                'data' => null,
            ], 404);
        }

        if (! $request->user()->can('delete', $case)) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
                'data' => null,
            ], 403);
        }

        $case->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medical case deleted successfully.',
            'data' => null,
        ], 200);
    }
}
