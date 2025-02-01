<?php

namespace SMART\Employee\Crud;

use SMART\Employee\Request\PostData;
use SMART\Employee\Request\PostRequest;


class Create extends PostRequest
{

    /**
     * Create new employee for a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '415'.
     * @param PostData $postBody The employee details to update in JSON format with the following fields:
     *      - 'maximum_roth_percentage' (number) Maximum Roth percentage.
     *      - 'date_of_birth' (string) Required. Employee's date of birth.
     *      - 'starts_on' (string) Required. Start date of employment.
     *      - 'ends_on' (string) End date of employment.
     *      - 'title' (string) Allowed values: 'Mr', 'Mrs', 'Ms', 'Miss', 'Mx', 'Dr'.
     *      - 'forename' (string) Required. Employee's first name.
     *      - 'surname' (string) Required. Employee's last name.
     *      - 'telephone' (string) Employee's contact number.
     *      - 'external_id' (string) External reference ID.
     *      - 'email' (string) Employee's email address.
     *      - 'national_insurance_number' (string) NI number.
     *      - 'exit_reason' (string) Reason for leaving.
     *      - 'opt_state' (string) Allowed values: 'ignition', 'opted_in', 'opted_out', 'rejoined', 'ceased_membership'.
     *      - 'gender' (string) Required. Allowed values: 'Male', 'Female'.
     *      - 'percentage' (number) Employee contribution percentage.
     *      - 'company_percentage' (number) Company contribution percentage.
     *      - 'company_match' (boolean) Whether company matches contributions.
     *      - 'group_id' (integer) Associated group ID.
     *      - 'works_in_uk' (boolean) Whether the employee works in the UK.
     *      - 'contribute_if_entitled' (boolean) Contribution entitlement flag.
     *      - 'customer_id' (integer) Customer ID.
     *      - 'retirement_age' (integer) Retirement age.
     *      - 'retirement_date' (string) Retirement date.
     *      - 'scheme_origin' (string) Allowed values: 'standard', 'migrated'.
     *      - 'secondary_email' (string) Employee's secondary email.
     *      - 'middle_name' (string) Employee's middle name.
     *      - 'civil_status' (string) Allowed values: 'Single', 'Married', 'Civil Partnered', 'Divorced', 'Widowed', 'Other', 'Undeclared'.
     *      - 'effective_date' (string) Effective date of employment change.
     *      - 'employment_status' (string) Current employment status.
     *      - 'part_time_percentage' (number) Part-time work percentage.
     *      - 'line1' (string) Address line 1.
     *      - 'line2' (string) Address line 2.
     *      - 'city' (string) City name.
     *      - 'postcode' (string) Required. Postal code.
     *      - 'employment_category' (string) Employment category.
     *      - 'contractual_enrolment' (boolean) Whether contractual enrolment applies.
     *      - 'target_retirement_age' (integer) Target retirement age.
     *      - 'line3' (string) Address line 3.
     *      - 'country_code' (string) Country code.
     *      - 'avc_percentage' (number) Additional voluntary contribution percentage.
     *      - 'salary_sacrifice_signed' (boolean) Whether salary sacrifice is signed.
     *      - 'annual_earnings1' (number) First annual earnings figure.
     *      - 'annual_earnings2' (number) Second annual earnings figure.
     *      - 'region' (string) Employee's region.
     *      - 'benefit_group_assigned_at' (string) Date benefit group was assigned.
     *      - 'opt_in_on' (string) Date when employee opted in.
     *      - 'minimum_percentage' (number) Minimum contribution percentage.
     *      - 'maximum_percentage' (number) Maximum contribution percentage.
     *      - 'minimum_company_percentage' (number) Minimum company contribution percentage.
     *      - 'maximum_company_percentage' (number) Maximum company contribution percentage.
     *      - 'minimum_roth_percentage' (number) Minimum Roth contribution percentage.
     *      - 'maximum_roth_percentage' (number) Maximum Roth contribution percentage.
     *
     */
    public function __construct($company_id,PostData $postBody)
    {
        parent::__construct($company_id,$postBody);
    }

    protected function getSubApiPath(): string
    {
        return '/employees';
    }

    
}
