<?php

namespace SMART\Group\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class PostData implements PostBody
{
    /**
     * Stores or updates employee data based on the given parameters.
     *
     * @param string $name Required. Gropu name.
     * @param float $employee_percentage  employee contribution percentage.
     * @param float $company_percentage company contribution percentage.
     * @param string $payment_frequency Customer's title. Allowed values: 'Mr', 'Mrs', 'Ms', 'Miss', 'Mx', 'Dr'.
     *
     * @return array The processed customer data.
     */
    /** @var array */
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
            'name' => 'name',
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
