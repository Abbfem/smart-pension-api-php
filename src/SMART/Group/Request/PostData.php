<?php

namespace SMART\Group\Request;

use SMART\Exceptions\InvalidPostBodyException;
use SMART\Request\PostBody;

class PostData implements PostBody
{
    /**
     * Stores or updates employee data based on the given parameters.
     *
     * @param string $date_of_birth Required. Customer's date of birth.
     * @param string $starts_on Required. Start date of employment or membership.
     * @param string $ends_on End date of employment or membership.
     * @param string $title Customer's title. Allowed values: 'Mr', 'Mrs', 'Ms', 'Miss', 'Mx', 'Dr'.
     * @param string $forename Required. Customer's first name.
     * @param string $surname Required. Customer's last name.
     * @param string $telephone Customer's telephone number.
     * @param string $external_id External reference ID.
     * @param string $email Customer's email address.
     * @param string $national_insurance_number Customer's National Insurance Number.
     * @param string $exit_reason Reason for exiting the scheme.
     * @param string $opt_state Customer's opt-in status. Allowed values: 'ignition', 'opted_in', 'opted_out', 'rejoined', 'ceased_membership'.
     * @param string $gender Required. Customer's gender. Allowed values: 'Male', 'Female'.
     * @param float $percentage Customer's contribution percentage.
     * @param float $company_percentage Company's contribution percentage.
     * @param bool $company_match Indicates if the company matches contributions.
     * @param int $group_id ID of the associated group.
     * @param bool $works_in_uk Indicates if the customer works in the UK.
     * @param bool $contribute_if_entitled Determines if the customer contributes when entitled.
     * @param int $customer_id Unique customer identifier.
     * @param int $retirement_age Customer's planned retirement age.
     * @param string $retirement_date Customer's planned retirement date.
     * @param string $scheme_origin Source of scheme enrollment. Allowed values: 'standard', 'migrated'.
     * @param string $secondary_email Secondary email address.
     * @param string $middle_name Customer's middle name.
     * @param string $civil_status Marital status. Allowed values: 'Single', 'Married', 'Civil Partnered', 'Divorced', 'Widowed', 'Other', 'Undeclared'.
     * @param string $effective_date Effective date of employment or status change.
     * @param string $employment_status Current employment status.
     * @param float $part_time_percentage Percentage of part-time work.
     * @param string $line1 Address line 1.
     * @param string $line2 Address line 2.
     * @param string $city City of residence.
     * @param string $postcode Required. Postal code.
     * @param string $employment_category Category of employment.
     * @param bool $contractual_enrolment Indicates if contractual enrollment is applicable.
     * @param int $target_retirement_age Targeted retirement age.
     * @param string $line3 Address line 3.
     * @param string $country_code ISO country code.
     * @param float $avc_percentage Additional voluntary contribution percentage.
     * @param bool $salary_sacrifice_signed Indicates if the salary sacrifice agreement is signed.
     * @param float $annual_earnings1 Annual earnings category 1.
     * @param float $annual_earnings2 Annual earnings category 2.
     * @param string $region Customer's region.
     * @param string $benefit_group_assigned_at Timestamp of benefit group assignment.
     * @param string $opt_in_on Timestamp of opt-in event.
     * @param float $minimum_percentage Minimum allowed contribution percentage.
     * @param float $maximum_percentage Maximum allowed contribution percentage.
     * @param float $minimum_company_percentage Minimum allowed company contribution percentage.
     * @param float $maximum_company_percentage Maximum allowed company contribution percentage.
     * @param float $minimum_roth_percentage Minimum Roth contribution percentage.
     * @param float $maximum_roth_percentage Maximum Roth contribution percentage.
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
            'date_of_birth' => 'date_of_birth',
            'starts_on' => 'starts_on',
            'forename' => 'forename',
            'surname' => 'surname',
            'gender' => 'gender',
            'postcode' => 'postcode',
            // 'signatories' => ['forename','surname','email'],
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
