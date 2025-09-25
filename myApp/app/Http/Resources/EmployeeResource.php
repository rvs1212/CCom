<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        
        $get = fn($key) => is_array($this->resource) ? $this->resource[$key] : $this->resource->{$key};

        return [
            'employeeId' => $get('EmployeeID'),
            'company' => $get('Company'),
            'firstName' => $get('FirstName'),
            'lastName' => $get('LastName'),
            'jobDescription' => $get('JobDescription'),
            'grade' => $get('Grade'),
            'decision' => $get('decision') ?? config('data.default_decision'),
        ];
        // return parent::toArray($request);
    }
}
