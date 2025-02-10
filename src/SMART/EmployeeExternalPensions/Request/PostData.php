<?php

namespace SMART\EmployeeExternalPensions\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class PostData implements PostBody
{
    /**
     * Updates an employee's external pension details for a company.
     *
     * @param array $employeeData An array of external pension objects containing:
     *     - 'employee_external_pensions' (array) An array of external pension objects containing:
     *      - 'id' (string) The ID of the external pension record.
     *      - 'name' (string) The name of the external pension.
     *      - 'type' (string) The type of external pension.
     *      - 'amount' (string) The amount associated with the pension.
     *      - '_destroy' (boolean) If true, marks the record for deletion.
     *
     * @return array The updated external pension details.
     */
    private $employeeData;

    public function __construct(array $employeeData)
    {
        $this->employeeData = $employeeData;
    }
    

    /**
     * Validate the post body, it should throw an Exception if something is wrong.
     *
     * @throws InvalidPostBodyException
     */
    public function validate()
    {
        $requiredFields = [
            'employee_external_pensions' => ['id','name','type','amount'],
        ];

        $emptyFields = [];
        foreach ($requiredFields as $key => $requiredField) {
            if(empty($this->employeeData)){
                if(is_array($requiredField)){
                    foreach($requiredField as $field){
                        $emptyFields[] = $key.'_'.$field;
                    }
                }else{
                    $emptyFields[] = $requiredField;
                }
            }
            if(is_array($requiredField)){
                foreach($requiredField as $field){
                    foreach ($this->employeeData[$key] as $sKey => $fields) {
                        if (($data = $this->employeeData[$key][$sKey][$field] ?? '') == '') {
                            $emptyFields[] = $key.'_'.$field; 
                        }
                    }
                                        
                }
            }else{
                if (($data = $this->employeeData[$key] ?? '') == '') {
                    $emptyFields[] = $requiredField;
                }
            }
        }

        if (count($emptyFields) > 0) {
            $emptyFieldsString = str_replace('_', ' ', implode(', ', $emptyFields));
            throw new InvalidPostBodyException("Missing post body fields ({$emptyFieldsString}).");
        }
    }

    /**
     * Return post body as an array to be used to call.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this->employeeData;
    }

    /**
     * @param array $employeeData
     *
     * @return $this
     */
    public function setEmployeeData(array $employeeData): self
    {
        $this->employeeData = $employeeData;

        return $this;
    }
}
