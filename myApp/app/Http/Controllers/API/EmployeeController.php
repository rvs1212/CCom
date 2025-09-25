<?php
namespace App\Http\Controllers\Api;

use App\Contracts\Employee\EmployeeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDecisionRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;

class EmployeeController extends Controller{

    protected $employeeService;
    public function __construct(EmployeeServiceInterface $employeeService){
        $this->employeeService = $employeeService;

    }

    //For Grid 1
    public function getEmpNotHandledInfo(){

        $result = $this->employeeService->getEmpNotHandled();
        $data = EmployeeResource::collection(collect($result));
        return response()->json([
            "data" => $data,
            "meta" => [
                "gradeOptions" => config('data.grade_options'),
                "ceoDecision" => config('data.decision_option'),
                "count" => count($result),
            ],
        ], 200);
    }


    //For Grid 2

    public function getAllEmployeeInfo(){
        $result = $this->employeeService->getAllEmployeeDetails();
        $data = EmployeeResource::collection(collect($result));

        return response()->json([
            "data" => $data,
            'meta' => [
                "gradeOptions" => config('data.grade_options'),
                "ceoDecision" => config('data.decision_option'),
                "count" => count($result),
            ],
        ], 200);
    }

    
    public function updateGrade(UpdateGradeRequest $request, $id){
       
        $result = $this->employeeService->updateGrade($id, $request->validated()['grade']);
        return $result ? response()->json(['message' => 'Grade updated successfully'], 200)
                       : response()->json(['message' => 'Failed to update grade. Invalid employee ID or grade.'], 400);
    }


    public function updateDecision(UpdateDecisionRequest $request, $id){
        $result = $this->employeeService->updateDecision($id, $request->validated()['decision']);
        return $result ? response()->json(['message' => 'Decision updated successfully'], 200)
                       : response()->json(['message' => 'Failed to update decision. Invalid employee ID or decision.'], 400);
      
    }

    public function deleteDecision($id){
        $result = $this->employeeService->deleteDecision($id);
        return $result ? response()->json(['message' => 'Decision deleted successfully'], 200)
                       : response()->json(['message' => 'Failed to delete decision. Invalid employee ID.'], 400);
    }



//     public function getEmployeeInfo(Request $request){
//         $data = $request->query('data');

//         $result = $data ? $this->employeeService->getAllEmployeeDetails() : $this->employeeService->pendingEmployeeDetails();

//         return response()->json($result);

//     }
// //need to to update grade and decision

//     public function update(Request $request, $id){
//         $this->employeeService->update($id, $request->only(['grade','decision']));
//         return response()->json(['message' => 'Employee updated successfully']);

//     }

//     public function deleteEmloyeeDecision($id){
//         $this->employeeService->deleteEmloyeeDecision($id);
//         return response()->json(['message' => 'Employee decision deleted successfully']);
//     }

}
// updated descsions