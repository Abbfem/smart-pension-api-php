<?php

namespace SMART\ExpressionOfWish\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class PostData implements PostBody
{
    /**

        * Stores or updates beneficiary data for a company and employee.
        *
        * @param string $company_id Required. The ID of the company.
        * @param string $employee_id Required. The ID of the employee.
        * @param bool $follow_local_law Determines if local law should be followed.
        * @param array $beneficiaries List of primary beneficiaries.
        * @param array $contingent_beneficiaries List of contingent beneficiaries.
        *
        * @return array The processed beneficiary data.
        * @throws Exception If required parameters are missing or invalid.
        */
    private $expressionOfWish;

    public function __construct(array $expressionOfWish)
    {
        $this->expressionOfWish = $expressionOfWish;
    }
    

    /**
     * Validate the post body, it should throw an Exception if something is wrong.
     *
     * @throws InvalidPostBodyException
     */
    public function validate()
    {
        $requiredFields = [
            'starts_on' => 'starts_on',
            'ends_on' => 'ends_on',
            'period_type' => 'period_type',
            'gross_qualifying_earnings' => 'gross_qualifying_earnings',
        ];

        $emptyFields = [];
        foreach ($requiredFields as $key => $requiredField) {
            if(empty($this->expressionOfWish)){
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
                    foreach ($this->expressionOfWish[$key] as $sKey => $fields) {
                        if (($data = $this->expressionOfWish[$key][$sKey][$field] ?? '') == '') {
                            $emptyFields[] = $key.'_'.$field; 
                        }
                    }
                                        
                }
            }else{
                if (($data = $this->expressionOfWish[$key] ?? '') == '') {
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
        return (array) $this->expressionOfWish;
    }

    /**
     * @param array $expressionOfWish
     *
     * @return $this
     */
    public function setexpressionOfWish(array $expressionOfWish): self
    {
        $this->expressionOfWish = $expressionOfWish;

        return $this;
    }
}
