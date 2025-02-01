<?php

namespace SMART\Company;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class NewPostBody implements PostBody
{
    /** @var array */
    private $companyData;
    

    /**
     * Validate the post body, it should throw an Exception if something is wrong.
     *
     * @throws InvalidPostBodyException
     */
    public function validate()
    {
        $requiredFields = [
            'name',
            'legal_structure',
            'signatories' => ['forename','surname','email'],
        ];

        $emptyFields = [];
        foreach ($requiredFields as $key => $requiredField) {
            if(empty($this->companyData)){
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
                    if (($data = $this->companyData[$key][$field] ?? '') == '') {
                        $emptyFields[] = $key.'_'.$field; 
                    }                    
                }
            }else{
                if (($data = $this->companyData[$key] ?? '') == '') {
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
        return (array) $this->companyData;
    }

    /**
     * @param array $companyData
     *
     * @return $this
     */
    public function setCompanyData(array $companyData): self
    {
        $this->companyData = $companyData;

        return $this;
    }
}
