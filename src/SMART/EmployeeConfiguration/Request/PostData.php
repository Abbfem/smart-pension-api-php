<?php

namespace SMART\EmployeeConfiguration\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class PostData implements PostBody
{
    /**
     * Updates an employee's configuration for a company.
     *
     * @param array $contributionsData The employee's status and preference settings, including:
     *      - 'benefits_out_suspended' (boolean) Whether benefits are suspended.
     *      - 'valuation_under_review' (boolean) Whether the valuation is under review.
     *      - 'disabled_switching' (boolean) Whether switching is disabled.
     *      - 'gone_away' (boolean) Whether the employee is considered "gone away".
     *      - 'blocked_rejoin' (boolean) Whether rejoin is blocked.
     *      - 'subject_to_money_purchase_annual_allowance' (boolean) Whether the employee is subject to money purchase annual allowance.
     *      - 'enable_auto_increase_percentage' (boolean) Whether auto increase percentage is enabled.
     *      - 'investments_locked' (boolean) Whether the investments are locked.
     *      - 'money_purchase_annual_allowance_effective_on' (string) Date when the money purchase annual allowance becomes effective.
     *      - 'preferred_currency' (string) The employee's preferred currency.
     *      - 'redirection_strategy' (string) The redirection strategy.
     *      - 'preferred_locale' (string) The employee's preferred locale.
     *      - 'requires_income_adequacy_page_visit' (boolean) Whether the income adequacy page visit is required.
     *
     * @return array The updated employee status and preferences.
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
