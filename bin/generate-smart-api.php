<?php

/**
 * Generates the SMART API resource classes and endpoint documentation from the
 * Keystone OpenAPI specification published on https://developers.autoenrolment.co.uk/smart/.
 *
 * Usage:
 *   php bin/generate-smart-api.php                 # downloads the latest spec
 *   php bin/generate-smart-api.php path/to/spec.json
 *
 * Writes:
 *   src/SMART/Api/Resources/*.php                  one class per API tag, one method per endpoint
 *   src/SMART/Api/Resources/ResourceAccessors.php  $smart->employees(), $smart->contributions() ...
 *   docs/smart/resources/*.md                      per-resource reference
 *   docs/smart/endpoints.md                        index of every endpoint
 *
 * Generated files are overwritten; do not edit them by hand. Tweak naming through
 * the TAG_ALIASES / NAME_OVERRIDES tables below instead.
 */

const SPEC_URL = 'https://stoplight.io/api/v1/projects/smart-pension/smart-documentation-portal/nodes/swagger.json?fromExportButton=true&snapshotType=http_service&deref=optimizedBundle';
const DOCS_URL = 'https://developers.autoenrolment.co.uk/smart';

const HTTP_METHODS = ['get', 'post', 'put', 'patch', 'delete'];

/** Leading "{static}/{param}" pairs dropped from method names (the class already gives that context). */
const CONTEXT_SEGMENTS = ['companies', 'employees', 'advisers'];

/** Leading static segments that only namespace a service. */
const NAMESPACE_SEGMENTS = ['payments_service', 'identity', 'api'];

/** Trailing path segments that are verbs rather than resources. */
const ACTION_SEGMENTS = [
    'accept-terms', 'assign_ssif_headers_mapping', 'batch_create', 'bulk_update', 'cancel', 'change', 'complete',
    'confirm', 'confirm_all', 'confirm-personal-data', 'finalise', 'generate_ifa_report', 'generate_verification_url',
    'moved_to_eu_company', 'remind', 'request_benefit_checks', 'request_for_current_holdings', 'request_monetary',
    'resend', 'select', 'start', 'submit', 'submit_ras_consent', 'validate', 'verify', 'transfer_out',
    'preview_payroll_configuration_details', 'managed-employees',
];

/** Tag (as written in the spec) => resource class name. Unlisted tags are StudlyCased and pluralised. */
const TAG_ALIASES = [
    ''                              => 'Employees',
    'Account-Claiming'              => 'AccountClaiming',
    'Adviser'                       => 'Advisers',
    'Advisers'                      => 'CompanyAdvisers',
    'Batch'                         => 'Batch',
    'PensionForecast'               => 'PensionForecast',
    'Benefit'                       => 'Benefits',
    'BenefitForm'                   => 'BenefitForms',
    'BenefitSchedule'               => 'BenefitSchedules',
    'BenefitMoneyOutState'          => 'BenefitMoneyOutState',
    'Company'                       => 'Companies',
    'CompanyBranding'               => 'CompanyBranding',
    'CompanyLookup'                 => 'CompanyLookups',
    'CompanyRules'                  => 'CompanyRules',
    'CompanySchemeDetails'          => 'CompanySchemeDetails',
    'Constants'                     => 'Constants',
    'Contribution'                  => 'Contributions',
    'Currency'                      => 'Currencies',
    'CustomAppStyling'              => 'CustomAppStyling',
    'Customer'                      => 'Customers',
    'CustomerLead'                  => 'CustomerLeads',
    'DeclarationOfComplianceEmployeeTotals' => 'DeclarationOfComplianceEmployeeTotals',
    'DiscountedValuation'           => 'DiscountedValuations',
    'Discovery'                     => 'Discovery',
    'EconomicZone'                  => 'EconomicZones',
    'Employee'                      => 'Employees',
    'EmployeeConfiguration'         => 'EmployeeConfigurations',
    'EmploymentCategory'            => 'EmploymentCategories',
    'Enrolment'                     => 'Enrolments',
    'EnvelopeSigning'               => 'Envelopes',
    'ExpressionOfWish'              => 'ExpressionOfWish',
    'ExternalEmployeeDataAvailability' => 'ExternalEmployeeDataAvailability',
    'Group'                         => 'Groups',
    'HistoricalContribution'        => 'HistoricalContributions',
    'HistoricalTransferIn'          => 'HistoricalTransferIns',
    'Identity'                      => 'Identity',
    'Import'                        => 'Imports',
    'IncomeAdequacyProjection'      => 'IncomeAdequacyProjections',
    'Individual'                    => 'Individuals',
    'InvestmentsPerformance'        => 'InvestmentsPerformance',
    'Jwks'                          => 'Jwks',
    'LaterLifeProjection'           => 'LaterLifeProjections',
    'LegalBankLocation'             => 'Countries',
    'Letter'                        => 'Letters',
    'MandateChargeSchedule'         => 'MandateChargeSchedules',
    'MarketingPreference'           => 'MarketingPreferences',
    'MembershipFees'                => 'MembershipFees',
    'MoneySummary'                  => 'MoneySummaries',
    'Nationalities'                 => 'Countries',
    'NextPayPeriodDate'             => 'NextPayPeriodDates',
    'NotificationsAccounts'         => 'Notifications',
    'Onboarding'                    => 'Onboarding',
    'OptInRequest'                  => 'OptInRequests',
    'PageVisit'                     => 'PageVisits',
    'Password'                      => 'Passwords',
    'Payment'                       => 'Payments',
    'Payroll configuration details' => 'PayrollConfigurationDetails',
    'Payroll configurations'        => 'PayrollConfigurations',
    'PayrollConfiguration'          => 'PayrollConfigurations',
    'PayrollConfigurationDetail'    => 'PayrollConfigurationDetails',
    'PensionForecastInputs'         => 'PensionForecast',
    'PensionlabConsent'             => 'PensionlabConsent',
    'Postponement'                  => 'Postponements',
    'ProfileEnforcement'            => 'Profile',
    'ProfileMigration'              => 'Profile',
    'Projection'                    => 'Projections',
    'RegularIncomeProjection'       => 'RegularIncomeProjections',
    'Role'                          => 'CustomerRoles',
    'SSIF Import'                   => 'SSIFImports',
    'Scheme'                        => 'Schemes',
    'StagingDate'                   => 'StagingDates',
    'Subaccount Groups Valuations'  => 'SubaccountGroupsValuations',
    'Summaries'                     => 'ContributionSummaries',
    'Tax Preview'                   => 'TaxPreview',
    'TaxInformationChange'          => 'TaxInformationChanges',
    'TaxResidence'                  => 'TaxResidence',
    'ThirdPartyPaymentProvider'     => 'ThirdPartyPaymentProviders',
    'TotalFundsAMCs'                => 'TotalFundsAmcs',
    'TransferIn'                    => 'TransferIns',
    'TransferMoneyReview'           => 'TransferMoneyReviews',
    'UltimateBeneficialOwnerAndConfiguration' => 'UltimateBeneficialOwnerAndConfiguration',
    'User'                          => 'Users',
    'Valuations'                    => 'Valuations',
];

/** "METHOD /path" => method name, for endpoints the naming rules do not describe well. */
const NAME_OVERRIDES = [
    'GET /'                                                              => 'discover',
    'POST /batch'                                                        => 'batch',
    'GET /accounts'                                                      => 'listAccounts',
    'POST /link_account'                                                 => 'linkAccount',
    'POST /account-claiming/attempts'                                    => 'createAttempt',
    'GET /account-claiming/attempts/{uuid}/'                             => 'getAttempt',
    'PATCH /account-claiming/attempts/{uuid}/additional-data'            => 'updateAttemptAdditionalData',
    'PATCH /account-claiming/attempts/{uuid}/email'                      => 'updateAttemptEmail',
    'PATCH /account-claiming/attempts/{uuid}/password'                   => 'updateAttemptPassword',
    'GET /account-claiming/current-attempt'                              => 'getCurrentAttempt',
    'GET /account-claiming/password-validations'                         => 'getPasswordValidations',
    'POST /account-claiming/confirm-personal-data'                       => 'confirmPersonalData',
    'GET /advisers/by_token'                                             => 'getAdviserByToken',
    'POST /advisers/{id}/managed-employees'                              => 'listManagedEmployees',
    'PATCH /adviser_companies/adviser/{adviser_id}/company/{company_id}' => 'updateAdviserCompany',
    'GET /companies/by_slug'                                             => 'getCompanyBySlug',
    'GET /customers/session'                                             => 'getCustomerSession',
    'GET /employees/session'                                             => 'getEmployeeSession',
    'GET /users/session'                                                 => 'getUserSession',
    'GET /employees/retirement'                                          => 'getRetirement',
    'GET /employees/retirement_options'                                  => 'getRetirementOptions',
    'GET /employees/employment_periods'                                  => 'listEmploymentPeriods',
    'GET /employee/callbacks/smartpension'                               => 'smartPensionCallback',
    'POST /oauth/token'                                                  => 'createToken',
    'GET /oauth/token/info'                                              => 'getTokenInfo',
    'POST /refresh_token'                                                => 'refreshToken',
    'GET /.well-known/jwks'                                              => 'getJwks',
    'PUT /individuals/retirement_age'                                    => 'updateRetirementAge',
    'POST /customers/password'                                           => 'createCustomerPassword',
    'POST /users/password'                                               => 'createUserPassword',
    'POST /customers/referrals'                                          => 'createReferral',
    'GET /cities/{country_code}/autocomplete'                            => 'autocompleteCities',
    'GET /company_lookups/{country_code}/{id}'                           => 'getCompanyLookupByCountry',
    'GET /countries/legal_bank_location'                                 => 'listLegalBankLocations',
    'GET /countries/nationality'                                         => 'listNationalities',
    'GET /mobile_app_versions/{mobile_platform}'                         => 'getMobileAppVersion',
    'GET /tpr/staging_date'                                              => 'getStagingDate',
    'GET /ssif_imports/download_template'                                => 'downloadTemplate',
    'GET /schemes/available'                                             => 'listAvailableSchemes',
    'GET /schemes/default'                                               => 'getDefaultScheme',
    'POST /profile/enforcement/start'                                    => 'startEnforcement',
    'POST /profile/enforcement/finalise'                                 => 'finaliseEnforcement',
    'GET /profile/migration/status'                                      => 'getMigrationStatus',
    'POST /onboarding/accept-terms'                                      => 'acceptTerms',
    'GET /onboarding/options'                                            => 'getOptions',
    'GET /onboarding/personal-data'                                      => 'getPersonalData',
    'PATCH /onboarding/personal-data'                                    => 'updatePersonalData',
    'GET /onboarding/status'                                             => 'getStatus',
    'GET /customer_leads/earliest_plan_effective_date'                   => 'getEarliestPlanEffectiveDate',
    'POST /pension_forecast/modeller_projection'                         => 'createModellerProjection',
    'GET /companies/{company_id}/company_fees/{company_fee_id}/invoice'  => 'getCompanyFeeInvoice',
    'GET /companies/{company_id}/envelope/signing'                       => 'getEnvelopeSigning',
    'GET /companies/{company_id}/envelope'                               => 'getEnvelope',
    'POST /companies/{company_id}/envelope/remind'                       => 'remindEnvelopeSigners',
    'GET /companies/{company_id}/contributions/summary'                  => 'getCompanySummary',
    'GET /companies/{company_id}/contributions/summary/payable'          => 'getCompanyPayableSummary',
    'GET /companies/{company_id}/contributions/summary/totals'           => 'getCompanySummaryTotals',
    'GET /companies/{company_id}/employees/{employee_id}/contributions/summary' => 'getEmployeeSummary',
    'GET /companies/{company_id}/bank_account_details/managable'         => 'listManageableBankAccountDetails',
    'GET /companies/{company_id}/bank_account_details/{bank_account_detail_id}/sensitive_data' => 'getBankAccountDetailSensitiveData',
    'GET /companies/{company_id}/third_party_payment_providers/{id}/sensitive_data' => 'getThirdPartyPaymentProviderSensitiveData',
    'GET /payments_service/companies/{company_uuid}/mandates/latest_next_possible_charge_date' => 'getLatestNextPossibleChargeDate',
    'GET /companies/{company_id}/payment_approvals/permitted'            => 'getPermittedPaymentApprovals',
    'GET /companies/{company_id}/ssif_imports/template'                  => 'getTemplate',
    'GET /companies/{company_id}/employees/{employee_id}/tax_residence/verify_home_country' => 'verifyTaxResidenceHomeCountry',
    'GET /companies/{company_id}/employees/{id}/opt_state'               => 'getOptState',
    'GET /companies/{company_id}/employees/{employee_id}/switches/delayability' => 'getSwitchDelayability',
    'GET /companies/{company_id}/employees/{employee_id}/verification_checks/state' => 'getVerificationCheckState',
    'GET /companies/{company_id}/employees/{employee_id}/verification_checks/verification_url' => 'getVerificationUrl',
    'GET /verification_checks/state'                                     => 'getVerificationCheckState',
    'GET /verification_checks/verification_url'                          => 'getVerificationUrl',
    'POST /verification_checks/generate_verification_url'                => 'generateVerificationUrl',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_form/verification_url' => 'getBenefitFormVerificationUrl',
    'POST /companies/{company_id}/employees/{employee_id}/benefit_form/verification_url' => 'generateBenefitFormVerificationUrl',
    'PATCH /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_question_responses/' => 'updateBenefitQuestionResponses',
    'POST /companies/{company_id}/employees/{employee_id}/add_money_forms/{type}' => 'createAddMoneyForm',
    'GET /companies/{company_id}/employees/{employee_id}/add_money_forms/{type}' => 'getAddMoneyForm',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_schedule' => 'getBenefitSchedule',
    'GET /companies/{company_id}/employees/{employee_id}/benefits/{id}/benefit_check_results' => 'listBenefitCheckResults',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_categories/{benefit_rule_set_id}' => 'getBenefitCategory',
    'GET /companies/{id}/employees/{employee_id}/benefit_categories/{benefit_rule_set_id}/tax_preview' => 'getTaxPreview',
    'GET /companies/{company_id}/employees/{employee_id}/transactions/{transaction_type}/{transaction_id}' => 'getTransaction',
    'GET /companies/{company_id}/employees/{employee_id}/subaccounts/{uuid}/default_fund_splits' => 'getSubaccountDefaultFundSplits',
    'POST /companies/{company_id}/employees/{employee_id}/subaccounts/{subaccount_uuid}/projections' => 'createProjection',
    'POST /companies/{company_id}/employees/{employee_id}/presigned_post' => 'createPresignedPost',
    'POST /companies/{company_id}/employees/{employee_id}/transfer_money_review' => 'createTransferMoneyReview',
    'POST /companies/{company_id}/employees/{id}/employee_rate_recommendation' => 'createEmployeeRateRecommendation',
    'POST /companies/{company_id}/employees/{id}/income_adequacy_projection' => 'createIncomeAdequacyProjection',
    'GET /companies/{company_id}/employees/{id}/pension_forecast_inputs' => 'getPensionForecastInputs',
    'GET /companies/{company_id}/employees/{employee_id}/pension_forecast' => 'getPensionForecast',
    'POST /companies/{company_id}/employees/{employee_id}/pension_forecast' => 'createPensionForecast',
    'GET /companies/{company_id}/employees/{employee_id}/money_summary'  => 'getMoneySummary',
    'GET /companies/{company_id}/employees/{employee_id}/employee_funds_valuations' => 'getEmployeeFundsValuations',
    'GET /companies/{company_id}/employees/{employee_id}/active_payroll_configuration' => 'getActivePayrollConfiguration',
    'GET /companies/{company_id}/employees/{employee_id}/recurring_contribution_schedule' => 'getRecurringContributionSchedule',
    'POST /companies/{company_id}/employees/{employee_id}/single_contribution_payments' => 'createSingleContributionPayment',
    'POST /companies/{company_id}/payroll_configurations/{payroll_id}/preview_payroll_configuration_details' => 'previewPayrollConfigurationDetails',
    'POST /companies/{company_id}/letters/postponement'                  => 'createPostponementLetter',
    'POST /companies/{company_id}/letters/welcome'                       => 'createWelcomeLetter',
    'POST /companies/{company_id}/letters'                               => 'createCompanyLetter',
    'GET /companies/{company_id}/letters'                                => 'listCompanyLetters',
    'GET /companies/{company_id}/letters/{id}'                           => 'getCompanyLetter',
    'DELETE /companies/{company_id}/letters/{id}'                        => 'deleteCompanyLetter',
    'POST /companies/{company_id}/benefit_statements/'                   => 'createCompanyBenefitStatements',
    'POST /partners/employees/{employee_id}/transfer_out'                => 'createTransferOut',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_group'  => 'getEmployeeBenefitGroup',
    'POST /notifications/accounts'                                       => 'createAccountNotification',
    'PUT /employees/marketing_preference'                                => 'replaceEmployeeMarketingPreference',
    'PATCH /employees/marketing_preference'                              => 'updateEmployeeMarketingPreference',
    'PUT /users/marketing_preference'                                    => 'replaceUserMarketingPreference',
    'PATCH /users/marketing_preference'                                  => 'updateUserMarketingPreference',
    'POST /employees/valuations'                                         => 'createMyValuation',
    'GET /employees/valuations'                                          => 'listMyValuations',
    'GET /employees/valuations/{id}'                                     => 'getMyValuation',
    'GET /employees/subaccount_groups_valuations'                        => 'listMySubaccountGroupsValuations',
    'POST /identity/api/profiles/emails/change'                          => 'requestEmailChange',
    'PUT /identity/api/profiles/emails/change'                           => 'confirmEmailChange',
    'POST /identity/api/profiles/emails/change/resend'                   => 'resendEmailChange',
    'POST /identity/api/profiles/emails/change/verify'                   => 'verifyEmailChange',
    'POST /identity/api/profiles/passwords/change'                       => 'changePassword',
    'GET /identity/api/profiles/passwords/validations'                   => 'getPasswordValidations',
    'GET /identity/api/profiles/personal-data'                           => 'getPersonalData',
    'PATCH /identity/api/profiles/personal-data'                         => 'updatePersonalData',
    'GET /identity/api/profiles/registration-data'                       => 'getRegistrationData',
    'POST /companies/{company_id}/employees/{employee_id}/generate_ifa_report' => 'generateIfaReport',
    'PATCH /companies/{company_id}/employees/{id}/moved_to_eu_company'   => 'markMovedToEuCompany',
    'POST /companies/{company_id}/employees/{employee_id}/benefits/{id}/request_benefit_checks' => 'requestBenefitChecks',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidences' => 'listBenefitEvidenceTypes',
    'POST /companies/{company_id}/employees/{employee_id}/benefit_categories/{benefit_category_id}/benefit_form' => 'createBenefitForm',
    'PUT /companies/{company_id}/employees/{employee_id}/benefit_categories/{benefit_category_id}/benefit_form' => 'replaceBenefitForm',
    'PATCH /companies/{company_id}/employees/{employee_id}/benefit_categories/{benefit_category_id}/benefit_form' => 'updateBenefitForm',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidence_uploads' => 'listBenefitEvidenceUploads',
    'POST /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidence_uploads' => 'createBenefitEvidenceUpload',
    'PATCH /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidence_uploads/confirm_all' => 'confirmAllBenefitEvidenceUploads',
    'PUT /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidence_uploads/{benefit_evidence_upload_id}' => 'replaceBenefitEvidenceUpload',
    'PATCH /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidence_uploads/{benefit_evidence_upload_id}' => 'updateBenefitEvidenceUpload',
    'DELETE /companies/{company_id}/employees/{employee_id}/benefit_form/benefit_evidence_uploads/{benefit_evidence_upload_id}' => 'deleteBenefitEvidenceUpload',
    'PUT /companies/{company_id}/employees/{employee_id}/benefits/{benefit_id}/benefit_check_results/{id}' => 'replaceBenefitCheckResult',
    'PATCH /companies/{company_id}/employees/{employee_id}/benefits/{benefit_id}/benefit_check_results/{id}' => 'updateBenefitCheckResult',
    'GET /companies/{company_id}/employees/{employee_id}/benefit_categories/{benefit_category_id}/estimate_benefit_valuations' => 'getEstimateBenefitValuations',
    'GET /companies/{company_id}/payroll_configurations/{payroll_configuration_id}/payroll_configuration_details' => 'listPayrollConfigurationDetails',
    'POST /companies/{company_id}/payroll_configurations/{payroll_configuration_id}/payroll_configuration_details/batch_create' => 'batchCreatePayrollConfigurationDetails',
    'GET /companies/{company_id}/payroll_configurations/{payroll_configuration_id}/payroll_configuration_details/{id}' => 'getPayrollConfigurationDetail',
    'PUT /companies/{company_id}/payroll_configurations/{payroll_configuration_id}/payroll_configuration_details/{id}' => 'replacePayrollConfigurationDetail',
    'PATCH /companies/{company_id}/payroll_configurations/{payroll_configuration_id}/payroll_configuration_details/{id}' => 'updatePayrollConfigurationDetail',
    'GET /companies/{company_id}/ssif_imports/{ssif_import_id}/file_headers' => 'listFileHeaders',
    'POST /companies/{company_id}/ssif_imports/{ssif_import_id}/assign_ssif_headers_mapping' => 'assignHeadersMapping',
    'GET /companies/{company_id}/ssif_imports/{ssif_import_id}/preview'  => 'getPreview',
    'GET /companies/{company_id}/ssif_imports/{ssif_import_id}/preview/ssif_import_missing_employees' => 'listPreviewMissingEmployees',
    'GET /companies/{company_id}/ssif_imports/{ssif_import_id}/preview/ssif_import_results' => 'listPreviewResults',
    'GET /companies/{company_id}/ssif_imports/{ssif_import_id}/ssif_import_missing_employees' => 'listMissingEmployees',
    'GET /companies/{company_id}/ssif_imports/{ssif_import_id}/ssif_import_results' => 'listResults',
    'GET /companies/{company_id}/imports/{import_id}/import_results'     => 'listImportResults',
    'POST /companies/{company_id}/employees/{employee_id}/verification_checks/generate_verification_url' => 'generateVerificationUrl',
    'POST /companies/{company_id}/employees/{employee_id}/switches/request_for_current_holdings' => 'requestSwitchForCurrentHoldings',
    'POST /companies/{company_id}/employees/{employee_id}/switches/request_monetary' => 'requestMonetarySwitch',
    'POST /companies/{company_id}/employees/{employee_id}/employee_external_pensions/bulk_update' => 'bulkUpdateEmployeeExternalPensions',
    'GET /constants'                                                     => 'getConstants',
    'GET /companies/{company_id}/company_rules'                          => 'getCompanyRules',
    'PATCH /companies/{company_id}/employees/{employee_id}/todo_items/complete' => 'completeTodoItems',
];

$root = dirname(__DIR__);
$specSource = $argv[1] ?? SPEC_URL;

fwrite(STDERR, "Reading spec from {$specSource}\n");
$json = file_get_contents($specSource);
if ($json === false) {
    fwrite(STDERR, "Unable to read the spec.\n");
    exit(1);
}

$spec = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
$generator = new SmartApiGenerator($spec, $root);
$generator->run();

final class SmartApiGenerator
{
    private array $spec;
    private string $root;

    /** @var array<string, array<int, array>> class => operations */
    private array $classes = [];

    public function __construct(array $spec, string $root)
    {
        $this->spec = $spec;
        $this->root = $root;
    }

    public function run(): void
    {
        $this->collect();
        $this->resolveNames();

        $resourceDir = "{$this->root}/src/SMART/Api/Resources";
        $docsDir = "{$this->root}/docs/smart/resources";
        @mkdir($resourceDir, 0777, true);
        @mkdir($docsDir, 0777, true);

        foreach (glob("{$resourceDir}/*.php") as $file) {
            if (!in_array(basename($file), ['AbstractResource.php'], true)) {
                unlink($file);
            }
        }
        foreach (glob("{$docsDir}/*.md") as $file) {
            unlink($file);
        }

        ksort($this->classes);
        $count = 0;

        foreach ($this->classes as $class => $operations) {
            usort($operations, fn ($a, $b) => [$a['path'], $a['order']] <=> [$b['path'], $b['order']]);
            $this->classes[$class] = $operations;
            file_put_contents("{$resourceDir}/{$class}.php", $this->renderClass($class, $operations));
            file_put_contents("{$docsDir}/{$class}.md", $this->renderClassDocs($class, $operations));
            $count += count($operations);
        }

        file_put_contents("{$resourceDir}/ResourceAccessors.php", $this->renderAccessors());
        file_put_contents("{$this->root}/docs/smart/endpoints.md", $this->renderIndex($count));

        fwrite(STDERR, sprintf("Generated %d resources with %d endpoints (API %s).\n", count($this->classes), $count, $this->spec['info']['version'] ?? '?'));
    }

    // ------------------------------------------------------------------ collection & naming

    private function collect(): void
    {
        $order = ['get' => 0, 'post' => 1, 'put' => 2, 'patch' => 3, 'delete' => 4];

        foreach ($this->spec['paths'] as $path => $item) {
            foreach (HTTP_METHODS as $method) {
                if (!isset($item[$method])) {
                    continue;
                }

                $op = $item[$method];
                $tag = $op['tags'][0] ?? '';
                $class = TAG_ALIASES[$tag] ?? self::pluralStudly($tag);
                $params = array_merge($item['parameters'] ?? [], $op['parameters'] ?? []);
                $params = array_map(fn ($p) => $this->deref($p), $params);

                $this->classes[$class][] = [
                    'method'  => strtoupper($method),
                    'path'    => $path,
                    'op'      => $op,
                    'tag'     => $tag,
                    'order'   => $order[$method],
                    'params'  => $params,
                    'service' => $this->serviceFor($op),
                    'name'    => NAME_OVERRIDES[strtoupper($method).' '.$path] ?? null,
                    'context' => null,
                ];
            }
        }
    }

    private function resolveNames(): void
    {
        foreach ($this->classes as $class => &$operations) {
            foreach ($operations as &$operation) {
                if ($operation['name'] === null) {
                    [$operation['name'], $operation['context']] = self::deriveName($operation['method'], $operation['path']);
                }
            }
            unset($operation);

            // disambiguate clashes inside a class with the stripped context (Company/Employee/Adviser)
            $groups = [];
            foreach ($operations as $i => $operation) {
                $groups[strtolower($operation['name'])][] = $i;
            }

            foreach ($groups as $indexes) {
                if (count($indexes) < 2) {
                    continue;
                }

                foreach ($indexes as $i) {
                    $context = $operations[$i]['context'];
                    if ($context) {
                        $operations[$i]['name'] = preg_replace('/^([a-z]+)/', '$1'.self::studly(self::singular($context)), $operations[$i]['name']);
                    }
                }
            }

            $seen = [];
            foreach ($operations as $i => $operation) {
                $key = strtolower($operation['name']);
                if (isset($seen[$key])) {
                    throw new RuntimeException("Duplicate method {$class}::{$operation['name']} for {$operation['method']} {$operation['path']}; add a NAME_OVERRIDES entry.");
                }
                $seen[$key] = true;
            }
        }
        unset($operations);
    }

    /**
     * @return array{0: string, 1: ?string} [method name, last stripped context segment]
     */
    private static function deriveName(string $method, string $path): array
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        $items = array_map(fn ($s) => ['name' => $s, 'param' => (bool) preg_match('/^\{.+\}$/', $s)], $segments);
        $context = null;

        while (true) {
            $statics = array_filter($items, fn ($i) => !$i['param']);
            if (count($items) >= 3 && !$items[0]['param'] && $items[1]['param']
                && in_array($items[0]['name'], CONTEXT_SEGMENTS, true) && count($statics) > 1) {
                $context = $items[0]['name'];
                $items = array_slice($items, 2);
                continue;
            }
            if (count($items) >= 2 && !$items[0]['param'] && in_array($items[0]['name'], NAMESPACE_SEGMENTS, true)) {
                $items = array_slice($items, 1);
                continue;
            }
            break;
        }

        $endsWithParam = $items && end($items)['param'];
        $nouns = [];
        foreach ($items as $i => $item) {
            if ($item['param']) {
                continue;
            }
            $followedByParam = isset($items[$i + 1]) && $items[$i + 1]['param'];
            $nouns[] = ['name' => $item['name'], 'item' => $followedByParam];
        }

        $verb = null;
        $last = end($nouns)['name'] ?? '';

        if (!$endsWithParam && $method !== 'GET' && count($nouns) > 1 && in_array($last, ACTION_SEGMENTS, true)) {
            $verb = lcfirst(self::studly($last));
            array_pop($nouns);
            $nouns[count($nouns) - 1]['item'] = true;
        }

        if ($verb === null) {
            if ($endsWithParam) {
                $verb = ['GET' => 'get', 'POST' => 'create', 'PUT' => 'replace', 'PATCH' => 'update', 'DELETE' => 'delete'][$method];
            } else {
                $plural = self::singular($last) !== $last;
                $verb = ['GET' => $plural ? 'list' : 'get', 'POST' => 'create', 'PUT' => 'replace', 'PATCH' => 'update', 'DELETE' => 'delete'][$method];
                if ($method === 'POST' && $nouns) {
                    $nouns[count($nouns) - 1]['item'] = true;
                }
            }
        }

        $noun = implode('', array_map(fn ($n) => self::studly($n['item'] ? self::singular($n['name']) : $n['name']), $nouns));

        return [$verb.$noun, $context];
    }

    private function serviceFor(array $op): ?string
    {
        $url = $op['servers'][0]['url'] ?? null;
        if ($url && preg_match('#^https?://([a-z0-9-]+)\.#', $url, $m) && $m[1] !== 'api') {
            return $m[1];
        }

        return null;
    }

    // ------------------------------------------------------------------ spec helpers

    private function deref($node, int $depth = 0)
    {
        if (is_array($node) && isset($node['$ref']) && is_string($node['$ref']) && $depth < 20) {
            $target = $this->spec;
            foreach (explode('/', substr($node['$ref'], 2)) as $part) {
                $target = $target[str_replace(['~1', '~0'], ['/', '~'], $part)] ?? null;
            }

            return $this->deref($target, $depth + 1) ?? [];
        }

        return $node;
    }

    private function scopes(array $op): ?array
    {
        if (!array_key_exists('security', $op)) {
            return null;
        }

        $scopes = [];
        foreach ($op['security'] as $requirement) {
            foreach ($requirement as $list) {
                foreach ($list as $scope) {
                    foreach (preg_split('/\s+/', $scope) as $s) {
                        $scopes[$s] = true;
                    }
                }
            }
        }

        return array_keys($scopes);
    }

    private function requiresAuth(array $op): bool
    {
        return array_key_exists('security', $op) && $op['security'] !== [];
    }

    private function bodyInfo(array $op): ?array
    {
        if (!isset($op['requestBody'])) {
            return null;
        }

        $body = $this->deref($op['requestBody']);
        $content = $body['content'] ?? [];
        $type = isset($content['multipart/form-data']) ? 'multipart/form-data' : (array_key_first($content) ?? 'application/json');
        $media = $content[$type] ?? [];
        $example = $media['example'] ?? null;

        if ($example === null && !empty($media['examples'])) {
            $first = $this->deref(reset($media['examples']));
            $example = $first['value'] ?? null;
        }

        return [
            'type'     => $type,
            'schema'   => $this->deref($media['schema'] ?? []),
            'example'  => $example,
            'required' => $body['required'] ?? false,
        ];
    }

    private function successResponse(array $op): ?array
    {
        foreach ($op['responses'] ?? [] as $code => $response) {
            if ((int) $code >= 200 && (int) $code < 300) {
                $response = $this->deref($response);
                $media = $response['content']['application/json'] ?? null;
                $example = $media['example'] ?? null;
                if ($example === null && !empty($media['examples'])) {
                    $first = $this->deref(reset($media['examples']));
                    $example = $first['value'] ?? null;
                }

                return ['code' => (string) $code, 'description' => $response['description'] ?? '', 'example' => $example];
            }
        }

        return null;
    }

    /**
     * Flattens a JSON schema into rows of [name, type, required, description].
     */
    private function schemaRows(array $schema, string $prefix = '', int $depth = 0): array
    {
        $schema = $this->deref($schema);
        $rows = [];

        if (($schema['type'] ?? null) === 'array' && isset($schema['items']) && $prefix === '') {
            return $this->schemaRows($schema['items'], '[]', $depth);
        }

        $required = array_flip($schema['required'] ?? []);

        foreach ($schema['properties'] ?? [] as $name => $property) {
            $property = $this->deref($property);
            $full = $prefix === '' ? $name : "{$prefix}.{$name}";
            $rows[] = [$full, $this->typeOf($property), isset($required[$name]), $this->describe($property)];

            if ($depth < 3) {
                if (($property['type'] ?? null) === 'object' || isset($property['properties'])) {
                    $rows = array_merge($rows, $this->schemaRows($property, $full, $depth + 1));
                } elseif (($property['type'] ?? null) === 'array' && isset($property['items'])) {
                    $items = $this->deref($property['items']);
                    if (isset($items['properties'])) {
                        $rows = array_merge($rows, $this->schemaRows($items, "{$full}[]", $depth + 1));
                    }
                }
            }
        }

        return $rows;
    }

    private function typeOf(array $schema): string
    {
        $schema = $this->deref($schema);
        $type = $schema['type'] ?? (isset($schema['properties']) ? 'object' : 'mixed');

        if (is_array($type)) {
            $type = implode('|', $type);
        }
        if ($type === 'array') {
            $type = $this->typeOf($schema['items'] ?? []).'[]';
        }
        if (($schema['format'] ?? null) === 'binary') {
            $type = 'file';
        } elseif (isset($schema['format'])) {
            $type .= " ({$schema['format']})";
        }
        if (!empty($schema['nullable'])) {
            $type .= ', nullable';
        }

        return $type;
    }

    private function describe(array $schema): string
    {
        $parts = [];
        if (!empty($schema['description'])) {
            $parts[] = self::oneLine($schema['description']);
        }
        if (!empty($schema['enum'])) {
            $parts[] = 'One of: '.implode(', ', array_map(fn ($v) => '`'.(is_scalar($v) ? var_export($v, true) : json_encode($v)).'`', array_filter($schema['enum'], fn ($v) => $v !== null)));
        }
        $items = isset($schema['items']) ? $this->deref($schema['items']) : [];
        if (!empty($items['enum'])) {
            $parts[] = 'Items one of: '.implode(', ', array_map(fn ($v) => '`'.$v.'`', array_filter($items['enum'], 'is_scalar')));
        }
        if (isset($schema['example']) && is_scalar($schema['example'])) {
            $parts[] = 'Example: `'.$schema['example'].'`';
        }

        return implode('. ', $parts);
    }

    // ------------------------------------------------------------------ PHP rendering

    private function renderClass(string $class, array $operations): string
    {
        $methods = [];
        $tags = array_unique(array_map(fn ($o) => $o['tag'] ?: 'untagged', $operations));

        foreach ($operations as $operation) {
            $methods[] = $this->renderMethod($operation);
        }

        $tagList = implode(', ', $tags);
        $body = implode("\n\n", $methods);

        return <<<PHP
<?php

// This file is generated by bin/generate-smart-api.php from the Keystone OpenAPI specification. Do not edit.

namespace SMART\\Api\\Resources;

use SMART\\Response\\Response;

/**
 * {$class} endpoints of the Smart Pension (Keystone) API.
 *
 * API tags: {$tagList}
 *
 * @see docs/smart/resources/{$class}.md
 */
class {$class} extends AbstractResource
{
{$body}
}

PHP;
    }

    private function renderMethod(array $operation): string
    {
        $op = $operation['op'];
        $method = $operation['method'];
        $pathParams = $this->pathParams($operation);
        $queryParams = array_values(array_filter($operation['params'], fn ($p) => ($p['in'] ?? '') === 'query'));
        $headerParams = array_values(array_filter($operation['params'], fn ($p) => ($p['in'] ?? '') === 'header' && strtolower($p['name']) !== 'authorization'));
        $body = $this->bodyInfo($op);
        $hasBody = $body !== null || in_array($method, ['POST', 'PUT', 'PATCH'], true);
        $multipart = $body && $body['type'] === 'multipart/form-data';

        $doc = [];
        $doc[] = self::oneLine($op['summary'] ?? "{$method} {$operation['path']}");
        if (!empty($op['description'])) {
            $doc[] = '';
            foreach (self::wrap(self::stripMarkdown($op['description'])) as $line) {
                $doc[] = $line;
            }
        }
        $doc[] = '';
        $doc[] = "{$method} {$operation['path']}";

        $scopes = $this->scopes($op);
        $doc[] = $scopes === null ? 'Authentication: none required' : 'OAuth scopes: '.($scopes ? implode(', ', $scopes) : 'any valid token');

        if ($operation['service']) {
            $doc[] = "Host: {$operation['service']} service";
        }

        if ($queryParams) {
            $doc[] = '';
            $doc[] = 'Query parameters ($query):';
            foreach ($queryParams as $param) {
                $schema = $this->deref($param['schema'] ?? []);
                $line = "  - {$param['name']} ({$this->typeOf($schema)})".(!empty($param['required']) ? ' required' : '');
                $desc = $this->describe($schema + ['description' => $param['description'] ?? ($schema['description'] ?? null)]);
                if (isset($schema['properties'])) {
                    $desc = trim($desc.' Keys: '.implode(', ', array_keys($schema['properties'])));
                }
                $doc[] = $line.($desc !== '' ? ": {$desc}" : '');
            }
        }

        if ($body) {
            $rows = array_filter($this->schemaRows($body['schema']), fn ($r) => strpos($r[0], '.') === false && strpos($r[0], '[]') !== 0);
            if ($rows) {
                $doc[] = '';
                $doc[] = 'Body fields ($body'.($multipart ? ', multipart/form-data' : ', JSON').'):';
                foreach ($rows as [$name, $type, $required]) {
                    $doc[] = "  - {$name} ({$type})".($required ? ' required' : '');
                }
            } elseif ($multipart) {
                $doc[] = '';
                $doc[] = 'Body is sent as multipart/form-data.';
            }
        }

        if ($headerParams) {
            $doc[] = '';
            $doc[] = 'Headers ($headers):';
            foreach ($headerParams as $param) {
                $doc[] = "  - {$param['name']}".(!empty($param['required']) ? ' required' : '').(!empty($param['description']) ? ': '.self::oneLine($param['description']) : '');
            }
        }

        $doc[] = '';
        $signature = [];
        $pathArray = [];
        foreach ($pathParams as $param) {
            $var = self::camel($param['name']);
            $doc[] = "@param string|int \${$var} ".self::oneLine($param['description'] ?? "{$param['name']} path parameter");
            $signature[] = "string|int \${$var}";
            $pathArray[] = "'{$param['name']}' => \${$var}";
        }
        if ($hasBody) {
            $doc[] = '@param array $body '.($multipart ? 'multipart fields; files as fopen() resources or [\'contents\' => ..., \'filename\' => ...]' : 'JSON request body');
            $signature[] = 'array $body = []';
        }
        $doc[] = '@param array $query query string parameters';
        $doc[] = '@param array $headers extra request headers';
        $signature[] = 'array $query = []';
        $signature[] = 'array $headers = []';
        $doc[] = '';
        $doc[] = '@throws \\SMART\\Api\\Exceptions\\ApiException';
        $doc[] = '@throws \\GuzzleHttp\\Exception\\GuzzleException';

        $options = ["'query' => \$query", "'headers' => \$headers"];
        if ($hasBody) {
            $options[] = $multipart ? "'multipart' => \$body" : "'json' => \$body";
        }
        if (!$this->requiresAuth($op)) {
            $options[] = "'auth' => \\SMART\\Api\\SmartClient::AUTH_OPTIONAL";
        }
        if ($operation['service']) {
            $options[] = "'service' => '{$operation['service']}'";
        }

        $docBlock = "    /**\n".implode("\n", array_map(fn ($l) => rtrim('     * '.str_replace('*/', '*\/', $l)), $doc))."\n     */";
        $sig = implode(', ', $signature);
        $pathArg = var_export($operation['path'], true);
        $pathArrayCode = '['.implode(', ', $pathArray).']';
        $optionsCode = implode(",\n            ", $options);

        return <<<PHP
{$docBlock}
    public function {$operation['name']}({$sig}): Response
    {
        return \$this->send('{$method}', {$pathArg}, {$pathArrayCode}, [
            {$optionsCode},
        ]);
    }
PHP;
    }

    private function pathParams(array $operation): array
    {
        preg_match_all('/\{([^}]+)\}/', $operation['path'], $matches);
        $declared = [];
        foreach ($operation['params'] as $param) {
            if (($param['in'] ?? '') === 'path') {
                $declared[$param['name']] = $param;
            }
        }

        return array_map(fn ($name) => $declared[$name] ?? ['name' => $name, 'in' => 'path'], array_unique($matches[1]));
    }

    private function renderAccessors(): string
    {
        $methods = [];
        foreach (array_keys($this->classes) as $class) {
            $accessor = self::accessor($class);
            $count = count($this->classes[$class]);
            $methods[] = <<<PHP
    /**
     * {$class} endpoints ({$count}).
     *
     * @see docs/smart/resources/{$class}.md
     */
    public function {$accessor}(): {$class}
    {
        return \$this->resource({$class}::class);
    }
PHP;
        }

        $body = implode("\n\n", $methods);

        return <<<PHP
<?php

// This file is generated by bin/generate-smart-api.php from the Keystone OpenAPI specification. Do not edit.

namespace SMART\\Api\\Resources;

/**
 * Resource accessors mixed into \\SMART\\Api\\SmartClient.
 */
trait ResourceAccessors
{
{$body}
}

PHP;
    }

    // ------------------------------------------------------------------ Markdown rendering

    private function renderIndex(int $count): string
    {
        $version = $this->spec['info']['version'] ?? '';
        $out = [];
        $out[] = '# SMART API endpoint reference';
        $out[] = '';
        $out[] = '> Generated by `bin/generate-smart-api.php` from the Keystone OpenAPI specification ('.$version.'). Do not edit by hand.';
        $out[] = '';
        $out[] = "Every one of the **{$count} endpoints** documented at <".DOCS_URL."> is available as a method on `SMART\\Api\\SmartClient`.";
        $out[] = 'Start with the [usage guide](README.md); click a resource for parameters, body fields and examples.';
        $out[] = '';
        $out[] = '## Resources';
        $out[] = '';
        $out[] = '| Resource | Accessor | Endpoints |';
        $out[] = '| --- | --- | --- |';
        foreach ($this->classes as $class => $operations) {
            $out[] = "| [{$class}](resources/{$class}.md) | `\$smart->".self::accessor($class).'()` | '.count($operations).' |';
        }
        $out[] = '';
        $out[] = '## All endpoints';
        $out[] = '';
        $out[] = '| HTTP | Path | PHP | Scopes |';
        $out[] = '| --- | --- | --- | --- |';

        $all = [];
        foreach ($this->classes as $class => $operations) {
            foreach ($operations as $operation) {
                $all[] = [$operation, $class];
            }
        }
        usort($all, fn ($a, $b) => [$a[0]['path'], $a[0]['order']] <=> [$b[0]['path'], $b[0]['order']]);

        foreach ($all as [$operation, $class]) {
            $scopes = $this->scopes($operation['op']);
            $anchor = strtolower($operation['name']);
            $out[] = "| `{$operation['method']}` | `{$operation['path']}` | [`".self::accessor($class)."()->{$operation['name']}()`](resources/{$class}.md#{$anchor}) | ".($scopes === null ? 'public' : (implode(', ', $scopes) ?: 'any')).' |';
        }

        return implode("\n", $out)."\n";
    }

    private function renderClassDocs(string $class, array $operations): string
    {
        $accessor = self::accessor($class);
        $out = [];
        $out[] = "# {$class}";
        $out[] = '';
        $out[] = '> Generated by `bin/generate-smart-api.php`. Do not edit by hand.';
        $out[] = '';
        $out[] = "Accessor: `\$smart->{$accessor}()` &middot; Class: `SMART\\Api\\Resources\\{$class}` &middot; [All endpoints](../endpoints.md) &middot; [Usage guide](../README.md)";
        $out[] = '';
        $out[] = '| Method | HTTP | Path |';
        $out[] = '| --- | --- | --- |';
        foreach ($operations as $operation) {
            $out[] = "| [`{$operation['name']}`](#".strtolower($operation['name']).") | `{$operation['method']}` | `{$operation['path']}` |";
        }

        foreach ($operations as $operation) {
            $out[] = '';
            $out[] = '---';
            $out[] = '';
            $out = array_merge($out, $this->renderOperationDocs($class, $operation));
        }

        return implode("\n", $out)."\n";
    }

    private function renderOperationDocs(string $class, array $operation): array
    {
        $op = $operation['op'];
        $out = [];
        $out[] = "## {$operation['name']}";
        $out[] = '';
        $out[] = "`{$operation['method']} {$operation['path']}`".($operation['service'] ? " &middot; host: `{$operation['service']}.*`" : '');
        $out[] = '';
        $out[] = '**'.self::oneLine($op['summary'] ?? '').'**';

        if (!empty($op['description'])) {
            $out[] = '';
            $out[] = trim($op['description']);
        }

        $scopes = $this->scopes($op);
        $out[] = '';
        $out[] = $scopes === null
            ? 'Authentication: not required.'
            : 'OAuth scopes: '.($scopes ? implode(', ', array_map(fn ($s) => "`{$s}`", $scopes)) : 'any valid token').'.';

        $pathParams = $this->pathParams($operation);
        $queryParams = array_values(array_filter($operation['params'], fn ($p) => ($p['in'] ?? '') === 'query'));
        $headerParams = array_values(array_filter($operation['params'], fn ($p) => ($p['in'] ?? '') === 'header'));

        if ($pathParams) {
            $out[] = '';
            $out[] = '**Path parameters**';
            $out[] = '';
            $out[] = '| Argument | Name | Example |';
            $out[] = '| --- | --- | --- |';
            foreach ($pathParams as $param) {
                $out[] = '| `$'.self::camel($param['name'])."` | `{$param['name']}` | ".(isset($param['example']) && is_scalar($param['example']) ? "`{$param['example']}`" : '').' |';
            }
        }

        if ($queryParams) {
            $out[] = '';
            $out[] = '**Query parameters** (`$query`)';
            $out[] = '';
            $out[] = '| Name | Type | Required | Notes |';
            $out[] = '| --- | --- | --- | --- |';
            foreach ($queryParams as $param) {
                $schema = $this->deref($param['schema'] ?? []);
                $notes = $this->describe($schema + ['description' => $param['description'] ?? ($schema['description'] ?? null)]);
                $name = $param['name'];
                $out[] = "| `{$name}` | {$this->typeOf($schema)} | ".(!empty($param['required']) ? 'yes' : 'no').' | '.self::cell($notes).' |';
                foreach ($this->schemaRows($schema) as [$sub, $type, $required, $desc]) {
                    $out[] = '| `'.$name.'['.str_replace('.', '][', $sub)."]` | {$type} | ".($required ? 'yes' : 'no').' | '.self::cell($desc).' |';
                }
            }
        }

        if ($headerParams) {
            $out[] = '';
            $out[] = '**Headers** (`$headers`)';
            $out[] = '';
            $out[] = '| Name | Required | Notes |';
            $out[] = '| --- | --- | --- |';
            foreach ($headerParams as $param) {
                $out[] = "| `{$param['name']}` | ".(!empty($param['required']) ? 'yes' : 'no').' | '.self::cell(self::oneLine($param['description'] ?? '')).' |';
            }
        }

        $body = $this->bodyInfo($op);
        if ($body) {
            $rows = $this->schemaRows($body['schema']);
            $out[] = '';
            $out[] = '**Request body** (`$body`, '.$body['type'].')';
            if ($rows) {
                $out[] = '';
                $out[] = '| Field | Type | Required | Notes |';
                $out[] = '| --- | --- | --- | --- |';
                foreach ($rows as [$name, $type, $required, $desc]) {
                    $out[] = "| `{$name}` | {$type} | ".($required ? 'yes' : 'no').' | '.self::cell($desc).' |';
                }
            }
            if ($body['example'] !== null) {
                $out[] = '';
                $out[] = 'Example body:';
                $out[] = '';
                $out[] = '```json';
                $out[] = self::truncateJson($body['example'], 40);
                $out[] = '```';
            }
        }

        $out[] = '';
        $out[] = '**PHP**';
        $out[] = '';
        $out[] = '```php';
        $out[] = $this->phpExample($class, $operation, $body, $queryParams);
        $out[] = '```';

        $success = $this->successResponse($op);
        if ($success) {
            $out[] = '';
            $out[] = "**Response** `{$success['code']}`".($success['description'] ? ' &ndash; '.self::oneLine($success['description']) : '');
            if ($success['example'] !== null) {
                $out[] = '';
                $out[] = '```json';
                $out[] = self::truncateJson($success['example'], 30);
                $out[] = '```';
            }
        }

        $errors = array_filter(array_keys($op['responses'] ?? []), fn ($c) => (int) $c >= 400);
        if ($errors) {
            $out[] = '';
            $out[] = 'Documented error statuses: '.implode(', ', array_map(fn ($c) => "`{$c}`", $errors)).'.';
        }

        return $out;
    }

    private function phpExample(string $class, array $operation, ?array $body, array $queryParams): string
    {
        $args = [];
        foreach ($this->pathParams($operation) as $param) {
            $args[] = isset($param['example']) && is_scalar($param['example']) ? var_export($param['example'], true) : '$'.self::camel($param['name']);
        }

        $hasBody = $body !== null || in_array($operation['method'], ['POST', 'PUT', 'PATCH'], true);
        if ($hasBody) {
            if ($body && is_array($body['example']) && $body['type'] !== 'multipart/form-data') {
                $args[] = self::shortPhpArray($body['example']);
            } elseif ($body && $body['type'] === 'multipart/form-data') {
                $fields = [];
                foreach (array_keys($body['schema']['properties'] ?? []) as $field) {
                    $type = $this->typeOf($body['schema']['properties'][$field]);
                    $fields[] = var_export($field, true).' => '.($type === 'file' ? "fopen('/path/to/file', 'r')" : '/* ... */');
                }
                $args[] = '['.implode(', ', array_slice($fields, 0, 4)).']';
            } else {
                $args[] = '[/* body */]';
            }
        }

        $names = array_map(fn ($p) => $p['name'], $queryParams);
        if (array_intersect(['limit', 'offset'], $names)) {
            $args[] = "['limit' => 50, 'offset' => 0]";
        }

        $call = '$response = $smart->'.self::accessor($class)."()->{$operation['name']}(".implode(', ', $args).');';

        return $call."\n\$data = \$response->getArray();";
    }

    // ------------------------------------------------------------------ string helpers

    public static function studly(string $value): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

        return implode('', array_map(fn ($w) => ucfirst($w), $words));
    }

    /**
     * Accessor name for a resource class: SSIFImports -> ssifImports, Companies -> companies.
     */
    public static function accessor(string $class): string
    {
        if (preg_match('/^([A-Z]+)([A-Z][a-z].*)$/', $class, $m)) {
            return strtolower($m[1]).$m[2];
        }

        return lcfirst($class);
    }

    public static function camel(string $value): string
    {
        return lcfirst(self::studly($value));
    }

    public static function singular(string $word): string
    {
        $irregular = ['status' => 'status', 'details' => 'detail', 'wishes' => 'wish', 'analysis' => 'analysis', 'series' => 'series', 'data' => 'data'];
        if (isset($irregular[strtolower($word)])) {
            return $irregular[strtolower($word)];
        }
        if (preg_match('/ies$/i', $word)) {
            return substr($word, 0, -3).'y';
        }
        if (preg_match('/(ss|sh|ch|x)es$/i', $word)) {
            return substr($word, 0, -2);
        }
        if (preg_match('/(ss|us|is)$/i', $word)) {
            return $word;
        }
        if (preg_match('/s$/i', $word)) {
            return substr($word, 0, -1);
        }

        return $word;
    }

    private static function pluralStudly(string $tag): string
    {
        $studly = self::studly($tag);
        if ($studly === '' || preg_match('/(s|Data|Information|Performance)$/', $studly)) {
            return $studly;
        }

        return preg_match('/y$/', $studly) ? substr($studly, 0, -1).'ies' : $studly.'s';
    }

    private static function oneLine(?string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $text));
    }

    private static function cell(string $text): string
    {
        return str_replace('|', '\|', $text);
    }

    private static function stripMarkdown(string $text): string
    {
        $text = preg_replace('/<[^>]+>/', ' ', $text);

        return trim(str_replace(['**', '`'], '', $text));
    }

    private static function wrap(string $text, int $width = 100): array
    {
        $lines = [];
        foreach (preg_split('/\R/', $text) as $paragraph) {
            $paragraph = rtrim($paragraph);
            if ($paragraph === '') {
                $lines[] = '';
                continue;
            }
            foreach (explode("\n", wordwrap($paragraph, $width)) as $line) {
                $lines[] = $line;
            }
        }

        // collapse repeated blank lines
        return array_values(array_filter($lines, function ($line, $i) use ($lines) {
            return $line !== '' || ($i > 0 && $lines[$i - 1] !== '');
        }, ARRAY_FILTER_USE_BOTH));
    }

    private static function truncateJson($value, int $maxLines): string
    {
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
        $lines = explode("\n", (string) $json);

        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lines[] = '  ... (truncated)';
        }

        return implode("\n", $lines);
    }

    private static function shortPhpArray($value, int $indent = 0): string
    {
        if (!is_array($value)) {
            return var_export($value, true);
        }
        if ($value === []) {
            return '[]';
        }

        $isList = array_keys($value) === range(0, count($value) - 1);
        $pad = str_repeat('    ', $indent + 1);
        $items = [];
        $i = 0;
        foreach ($value as $key => $item) {
            if ($i++ >= 12) {
                $items[] = $pad.'// ...';
                break;
            }
            $items[] = $pad.($isList ? '' : var_export($key, true).' => ').self::shortPhpArray($item, $indent + 1).',';
        }

        return "[\n".implode("\n", $items)."\n".str_repeat('    ', $indent).']';
    }
}
