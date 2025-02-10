<?php

namespace SMART\Adviser\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class NewPostBody implements PostBody
{
    /** @var array */
    private $adviserData;

    public function __construct(array $adviserData)
    {
        $this->adviserData = $adviserData;
    }
    

    /**
     * Handles company registration or update request.
     *
     * @param string $logo The company logo URL or base64-encoded string.
     * @param string $name Required. The company name.
     * @param string $address Required. The company address.
     * @param string|null $signup_start The start date for signup.
     * @param string|null $signup_finish The end date for signup.
     * @param string|null $channel The channel through which the company was registered.
     * @param string|null $agent_number The agent number associated with the company.
     * @param string $email Required. The company's email address.
     * @param string $telephone Required. The company's telephone number.
     * @param bool|null $annuity_option_enabled Whether annuity options are enabled.
     * @param bool|null $marketing_included Whether marketing is included.
     * @param string $password Required. The account password.
     * @param string|null $title The title of the company representative (e.g., Mr, Mrs, Dr).
     * @param string $forename Required. The forename of the company representative.
     * @param string $surname Required. The surname of the company representative.
     * @throws InvalidPostBodyException
     */
    
    public function validate()
    {
        $requiredFields = [
            'name' => 'name',
            'address' => 'address',
            'email' => 'email',
            'telephone' => 'telephone',
            'password' => 'password',
            'forename' => 'forename',
            'surname' => 'surname',
        ];
        $emptyFields = [];
        foreach ($requiredFields as $key => $requiredField) {
            if(empty($this->adviserData)){
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
                    foreach ($this->adviserData[$key] as $sKey => $fields) {
                        if (($data = $this->adviserData[$key][$sKey][$field] ?? '') == '') {
                            $emptyFields[] = $key.'_'.$field; 
                        }
                    }
                                        
                }
            }else{
                if (($data = $this->adviserData[$key] ?? '') == '') {
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
        return (array) $this->adviserData;
    }

    /**
     * @param array $adviserData
     *
     * @return $this
     */
    public function setAdviserData(array $adviserData): self
    {
        $this->adviserData = $adviserData;

        return $this;
    }
}
