<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchUsersByNameRequest;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherService $teacherService
    ) { }


     /**
     * Search for teachers by name and return a list of options
     * @param Request $request
     * @return JsonResponse
     */
    public function search(SearchUsersByNameRequest $request): JsonResponse
    {
        $teachers = $this->teacherService->searchTeachersByName($request->get('name'));
        return \response()->json($teachers);
    }
}
