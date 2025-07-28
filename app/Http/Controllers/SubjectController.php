<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSubjectRequest;
use App\Repositories\Interfaces\SubjectRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function __construct(
        protected SubjectRepositoryInterface $subjectRepository
    ) { }

     /**
     * Display a listing of the subjects.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $subjects = $this->subjectRepository->getAll();
        return \response()->json($subjects);
    }

     /**
     * Store a newly created subject in storage.
     * @param SaveSubjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SaveSubjectRequest $request): JsonResponse
    {
        $insert_data = array_merge($request->validated(), [
            'organization_id' => Auth::user()->organization_id
        ]);

        $subject = $this->subjectRepository->create($insert_data);
        return response()->json($subject, 201);
    }

    /**
     * Return the specified subject.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $subject = $this->subjectRepository->find($id);
        return response()->json($subject);
    }

    /**
     * Update the specified subject in storage.
     * @param SaveSubjectRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SaveSubjectRequest $request, string $id): JsonResponse
    {
        $subject = $this->subjectRepository->find($id);
        
        $this->subjectRepository->update($subject, $request->validated());

        return response()->json($subject);
    }

    /**
     * Remove the specified subject from storage.
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $subject = $this->subjectRepository->find($id);
        $this->subjectRepository->delete($subject);
        return response()->json(null, 204);
    }
}
