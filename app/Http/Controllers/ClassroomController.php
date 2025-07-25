<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveClassroomRequest;
use App\Repositories\Interfaces\ClassroomRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    public function __construct(
        protected ClassroomRepositoryInterface $classroomRepository
    ) { }

    /**
     * Display a listing of the classroom.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() :JsonResponse
    {
        $classrooms = $this->classroomRepository->getAll();
        return response()->json($classrooms);
    }

    /**
     * Store a newly created classrrom in storage.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SaveClassroomRequest $request): JsonResponse
    {
        $insert_data = array_merge($request->validated(), [
            'organization_id' => Auth::user()->organization_id
        ]);
        $classroom = $this->classroomRepository->create($insert_data);
        return response()->json($classroom, 201);
    }

    /**
     * Return the specified classroom.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $classroom = $this->classroomRepository->find($id);
        return response()->json($classroom);
    }

    /**
     * Update the specified classroom in storage.
     * @param SaveClassroomRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SaveClassroomRequest $request, string $id): JsonResponse
    {
        $classroom = $this->classroomRepository->find($id);
        
        $this->classroomRepository->update($classroom, $request->validated());

        return response()->json($classroom);
    }

    /**
     * Remove the specified classroom from storage.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $classroom = $this->classroomRepository->find($id);
        $this->classroomRepository->delete($classroom);
        return response()->json(null, 204);
    }
}
