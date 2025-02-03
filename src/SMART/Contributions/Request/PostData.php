<?php

namespace SMART\Contributions\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class PostData implements PostBody
{
    /**
     * Creates or updates an employee's contribution record.
     *
     * @param array $contributionsData The contribution details in JSON format, including:
     *      - 'starts_on' (string) Contribution start date.
     *      - 'ends_on' (string) Contribution end date.
     *      - 'period_type' (string) Type of period for contribution.
     *          -"Weekly",
     *          -"Fortnightly",
     *          -"FourWeekly",
     *          -"Monthly",
     *          -"Quarterly",
     *          -"BiAnnually",
     *          -"Annually"
     *      - 'category' (string) Employee category. Allowed values:
     *          - 'No duties'
     *          - 'Entitled workers'
     *          - 'Non-eligible jobholders'
     *          - 'Eligible jobholders'
     *      - 'contribution_type' (string) Allowed value: 'regular'.
     *      - 'pensionable_earnings' (number) Pensionable earnings amount.
     *      - 'gross_qualifying_earnings' (number) Gross qualifying earnings.
     *      - 'second_gross_qualifying_earnings_amount' (number) Second gross qualifying earnings amount.
     *      - 'company_amount' (number) Employer contribution amount.
     *      - 'employee_amount' (number) Employee contribution amount.
     *      - 'employee_percentage' (number) Employee contribution percentage.
     *      - 'company_percentage' (number) Employer contribution percentage.
     *      - 'avc_percentage' (number) Additional voluntary contribution percentage.
     *      - 'voluntary_amount' (number) Voluntary contribution amount.
     *
     */
    private $contributionsData;

    public function __construct(array $contributionsData)
    {
        $this->contributionsData = $contributionsData;
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
            if(empty($this->contributionsData)){
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
                    foreach ($this->contributionsData[$key] as $sKey => $fields) {
                        if (($data = $this->contributionsData[$key][$sKey][$field] ?? '') == '') {
                            $emptyFields[] = $key.'_'.$field; 
                        }
                    }
                                        
                }
            }else{
                if (($data = $this->contributionsData[$key] ?? '') == '') {
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
        return (array) $this->contributionsData;
    }

    /**
     * @param array $contributionsData
     *
     * @return $this
     */
    public function setContributionsData(array $contributionsData): self
    {
        $this->contributionsData = $contributionsData;

        return $this;
    }
}
