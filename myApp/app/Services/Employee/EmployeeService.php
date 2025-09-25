<?php
namespace App\Services\Employee;

use App\Contracts\Employee\EmployeeServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class EmployeeService  implements EmployeeServiceInterface{

    public function getEmpNotHandled(){

        return DB::table('Employee as e')
        ->select('e.EmployeeID','e.Company', 'e.FirstName','e.LastName',
            'e.JobDescription', 'eg.Grade', 'cd.decision')
        ->leftJoin('EmployeeGrading as eg', 'e.EmployeeID', '=', 'eg.EmployeeID')
        ->leftJoin('CEODecision as cd', 'e.EmployeeID', '=', 'cd.EmployeeID')
        ->where(function($q){
            $q->where('cd.decision', '=', Config('data.default_decision'))
            ->orWhereNull('cd.decision');
        })
        // ->where('cd.decision', '=', Config('data.default_decision'))
        // ->orWhereNull('cd.EmployeeId')
        //->toSql();
        ->get()->toArray();
    }


    public function getAllEmployeeDetails(){
        return DB::table('Employee as e')
        ->select('e.EmployeeID','e.Company', 'e.FirstName',
                'e.LastName', 'e.JobDescription',
                'eg.Grade')
        ->selectRaw('COALESCE(cd.decision, ?) as decision', [Config('data.default_decision')])
        ->leftJoin('EmployeeGrading as eg', 'e.EmployeeID', '=', 'eg.EmployeeID')
        ->leftJoin('CEODecision as cd', 'e.EmployeeID', '=', 'cd.EmployeeID')
        ->get()->toArray();

    }
//DB::raw("COALESCE(cd.decision, "'.config(data.default_decision).'" as decision"))


    public function updateGrade($employeeId, $grade){
    //check if grade is valid
    // check if employee exists
    // if exists update else insert
        $allowed = config('data.grade_options', ['A','B','C','D','F']);
        if(!in_array($grade, $allowed)){
            return false;
        }

        $exists = DB::table('Employee')
                ->where('EmployeeID', '=', $employeeId)
                ->exists();
        if(!$exists){
            return false;
        }

        DB::table('EmployeeGrading')
            ->updateOrInsert(
                ['EmployeeID' => $employeeId],
                ['Grade' => $grade]
            );
        return true;

    }


    public function updateDecision($employeeId, $decision){
        //check if decision is valid
        // check if employee exists
        // if exists update else insert
            $allowed = config('data.decision_option', [ 'NOT YET TAKEN','STAYS AT COMPANY','MOVE TO DIFFERENT POSITION','LET GO']);
            if(!in_array($decision, $allowed)){
                return false;
            }
    
            $exists = DB::table('Employee')
                    ->where('EmployeeID', '=', $employeeId)
                    ->exists();
            if(!$exists){
                return false;
            }
    
            DB::table('CEODecision')
                ->updateOrInsert(
                    ['EmployeeID' => $employeeId],
                    ['decision' => $decision]
                );
            return true;

    }


    public function deleteDecision($employeeId){
        
        $exists = DB::table('Employee')
                    ->where('EmployeeID', '=', $employeeId)
                    ->exists();
        if(!$exists){
            return false;
        }

        DB::table('CEODecision')
            ->where('EmployeeID', '=', $employeeId)
            ->delete();

        return true;
    }


















//     public function getAllEmployeeDetails(){
//         return DB::select("SELECT
//          e.Company, CONCAT(e.FirstName,' ',e.LastName) AS name, e.JobDescription, eg.Grade, cd.decision
//             FROM Employee as e
//             Left join EmployeeGrading as eg on e.EmployeeID = eg.EmployeeID
//             Left join CEODecision as cd on e.EmployeeID = cd.EmployeeID

//             ");
//     }

//     public function pendingEmployeeDetails(){
//         $default_decision = Config('data.default_decision');
//         return DB::select("SELECT
//          e.Company, CONCAT(e.FirstName,' ',e.LastName) AS name, e.JobDescription, eg.Grade, cd.decision
//             FROM Employee as e
//             Left join EmployeeGrading as eg on e.EmployeeID = eg.EmployeeID
//             Left join CEODecision as cd on e.EmployeeID = cd.EmployeeID
//             WHERE cd.decision = ?
//             ", [$default_decision]);
// }

//     public function update($id, $data){
//         //wirte update logic here

//         if(isset($data['grade'])){
//             $dataAffected = DB::update("UPDATE EmployeeGrading SET Grade = ? WHERE EmployeeID = ?", [$data['grade'], $id]);

//             if($dataAffected == 0){
//                 DB::insert("INSERT INTO EmployeeGrading (EmployeeID, Grade) VALUES (?, ?)", [$id, $data['grade']]);
//             }
//         }
//         if(isset($data['decision'])){
//             $dataAffected = DB::update("UPDATE CEODecision SET decision = ? WHERE EmployeeID = ?", [$data['decision'], $id]);
            
//             if($dataAffected == 0){
//                 DB::insert("INSERT INTO CEODecision (EmployeeID, decision) VALUES (?, ?)", [$id, $data['decision']]);
//             }
//         }
//     }

//     public function deleteEmloyeeDecision($id){
//         //write delete logic here

//         DB::delete("DELETE FROM CEODecision WHERE EmployeeID = ?", [$id]);
//     }
}