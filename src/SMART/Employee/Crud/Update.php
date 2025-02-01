<?php

namespace SMART\Employee\Crud;

use SMART\Employee\Request\PostData;
use SMART\Employee\Request\PutRequest;



class Update extends PutRequest
{
    /** @var string */
    private $employee_id;

    /**
     * Updates employee details for a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '647'.
     * @param string $id Required. The ID of the employee. Example: '392'.
     * @param PostData $postBody The employee details to update in JSON format with the following fields:
     *      - 'employee_contribution_configuration_starts_at' (string) Date when contribution starts.
     *      - 'date_of_birth' (string) Employee's date of birth.
     *      - 'starts_on' (string) Start date of employment.
     *      - 'ends_on' (string) End date of employment.
     *      - 'title' (string) Allowed values: 'Mr', 'Mrs', 'Ms', 'Miss', 'Mx', 'Dr'.
     *      - 'forename' (string) Employee's first name.
     *      - 'surname' (string) Employee's last name.
     *      - 'telephone' (string) Employee's contact number.
     *      - 'external_id' (string) External reference ID.
     *      - 'email' (string) Employee's email address.
     *      - 'national_insurance_number' (string) NI number.
     *      - 'exit_reason' (string) Reason for leaving.
     *      - 'opt_state' (string) Allowed values: 'ignition', 'opted_in', 'opted_out', 'rejoined', 'ceased_membership'.
     *      - 'gender' (string) Allowed values: 'Male', 'Female'.
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
     *      - 'postcode' (string) Postal code.
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
     *      - 'password' (string) New password (if updating).
     *      - 'current_password' (string) Current password for verification.
     *      - 'opt_out_window_starts_on' (string) Date when opt-out window starts.
     *      - 'opt_in_on' (string) Date when employee opted in.
     *      - 'opt_out_on' (string) Date when employee opted out.
     *      - 'onboarded_at' (string) Onboarding date.
     *      - 'online_login_disabled' (boolean) Whether online login is disabled.
     *
     */

    public function __construct(string $company_id, string $employee_id, PostData $postBody)
    {
        parent::__construct($company_id, $postBody);
        $this->employee_id = $employee_id;
    }

    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}";
    }

    
}
