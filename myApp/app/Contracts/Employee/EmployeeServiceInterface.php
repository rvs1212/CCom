<?php
namespace App\Contracts\Employee;

interface EmployeeServiceInterface{


//get details. update, remove
    
// public function getAllEmployeeDetails();
// public function pendingEmployeeDetails();
// public function update($id, $data);
// public function deleteEmloyeeDecision($id);


    public function getEmpNotHandled();
    public function getAllEmployeeDetails();

    public function updateGrade($employeeId, $grade);
    public function updateDecision($employeeId, $decision);

    public function deleteDecision($employeeId);


}