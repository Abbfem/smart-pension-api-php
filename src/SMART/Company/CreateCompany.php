<?php

namespace SMART\Company;

use SMART\Company\Request\NewPostBody;
use SMART\Company\Request\PostRequest;


class CreateCompany extends PostRequest
{
    /**
     * Create new company and scheme details.
     *
     * @param NewPostBody $postBody The company and scheme details in JSON format with the following fields:
     *      - 'employee_can_change_contribution' (boolean) Whether employees can change contributions.
     *      - 'scheme_origin' (string) Allowed value: 'standard'.
     *      - 'scheme_origin_provider_id' (string) Provider ID for the scheme origin.
     *      - 'scheme_origin_provider_name' (string) Provider name for the scheme origin.
     *      - 'name' (string) Required. Company name.
     *      - 'registration_number' (string) Company registration number.
     *      - 'legal_structure' (string) Required. Legal structure of the company.
     *      - 'tax_office_employer_reference' (string) Employer reference from the tax office.
     *      - 'tax_office_number' (string) Tax office number.
     *      - 'reg_address1' (string) Registered address line 1.
     *      - 'reg_address2' (string) Registered address line 2.
     *      - 'reg_address3' (string) Registered address line 3.
     *      - 'reg_address4' (string) Registered address line 4.
     *      - 'reg_address_postcode' (string) Registered address postcode.
     *      - 'reg_address_country_code' (string) Country code for registered address.
     *      - 'trading_address1' (string) Trading address line 1.
     *      - 'trading_address2' (string) Trading address line 2.
     *      - 'trading_address3' (string) Trading address line 3.
     *      - 'trading_address4' (string) Trading address line 4.
     *      - 'trading_address_postcode' (string) Trading address postcode.
     *      - 'trading_address_country_code' (string) Country code for trading address.
     *      - 'state_of_incorporation' (string) State where the company is incorporated.
     *      - 'taxed_as' (string) Tax classification.
     *      - 'tax_year_end_day' (integer) Tax year end day.
     *      - 'tax_year_end_month' (integer) Tax year end month.
     *      - 'pensionable_earning_type' (string) Allowed values: 'banded', 'unbanded', 'tier1', 'tier2', 'tier3'.
     *      - 'pension_regulator_letter_code' (string) Letter code from pension regulator.
     *      - 'default_percentage' (number) Default contribution percentage.
     *      - 'signatories' (array[object]) List of signatories with:
     *          - 'date_of_birth' (string) Signatory's date of birth.
     *          - 'title' (string) Title of the signatory.
     *          - 'forename' (string) Required. First name.
     *          - 'middlename' (string) Middle name.
     *          - 'surname' (string) Required. Last name.
     *          - 'telephone' (string) Contact number.
     *          - 'alternative_telephone' (string) Alternative contact number.
     *          - 'email' (string) Required. Email address.
     *          - 'password' (string) Password for access.
     *          - 'referee_id' (string) Referee ID.
     *          - 'referee_type' (string) Allowed values: 'customer', 'employee'.
     *          - 'line1' (string) Address line 1.
     *          - 'line2' (string) Address line 2.
     *          - 'city' (string) City.
     *          - 'county' (string) County.
     *          - 'postcode' (string) Postal code.
     *      - 'admins' (array[object]) List of administrators with:
     *          - 'title' (string) Admin's title.
     *          - 'forename' (string) Required. Admin's first name.
     *          - 'middlename' (string) Admin's middle name.
     *          - 'surname' (string) Required. Admin's last name.
     *          - 'telephone' (string) Contact number.
     *          - 'alternative_telephone' (string) Alternative contact number.
     *          - 'email' (string) Required. Admin's email address.
     *          - 'referee_id' (string) Referee ID.
     *          - 'referee_type' (string) Allowed values: 'customer', 'employee'.
     *          - 'password' (string) Admin password.
     *      - 'scheme_detail' (object) Scheme details with:
     *          - 'starts_on' (string) Scheme start date.
     *          - 'tax_relief_basis_type' (string) Tax relief basis type.
     *      - 'adviser_token' (string) Adviser token.
     *      - 'incorporated_on' (string) Company incorporation date.
     *      - 'employee_can_change_contribution' (boolean) Whether employees can change contributions.
     *
     */
    public function __construct(NewPostBody $postBody)
    {
        parent::__construct($postBody);
    }

    protected function getCompanyApiPath(): string
    {
        return '';
    }

    
}
