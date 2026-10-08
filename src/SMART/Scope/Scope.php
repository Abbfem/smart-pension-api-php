<?php

namespace SMART\Scope;

/**
 * OAuth 2.0 scopes of the Keystone API.
 *
 * Authorization code flow: SMP_USER (advisers), SMP_CUSTOMER (employer admins), SMP_EMPLOYEE (members).
 * Client credentials flow: the read:* scopes below (a read scope also permits writes); they must be
 * enabled for your app by Smart (api@smartpension.co.uk).
 *
 * @see https://developers.autoenrolment.co.uk/smart/8746c0c6c82b7-o-auth
 */
class Scope
{
    const SMP_USER = 'user';
    const SMP_CUSTOMER = 'customer';
    const SMP_EMPLOYEE = 'employee';

    const SMP_COMPANIES = 'read:companies';
    const SMP_CUSTOMERS = 'read:customers';
    const SMP_EMPLOYEES = 'read:employees';
    const SMP_EXPRESSION_OF_WISHES = 'read:expression_of_wishes';
    const SMP_FUNDS = 'read:funds';
    const SMP_FUND_SPLITS = 'read:fund_splits';
    const SMP_SSIF_IMPORT_RESULTS = 'read:ssif_import_results';
    const SMP_SSIF_IMPORTS = 'read:ssif_imports';
    const SMP_BANK_ACCOUNT_DETAILS = 'read:bank_account_details';
    const SMP_BANK_DETAILS = 'read:bank_details';
    const SMP_BENEFIT_GROUPS = 'read:benefit_groups';
    const SMP_COMPANIES_AUTOMATIONS = 'read:companies_automations';
    const SMP_CONTRIBUTIONS = 'read:contributions';
    const SMP_DEFAULT_INVESTMENT_INSTRUMENTS = 'read:default_investment_instruments';
    const SMP_ECONOMIC_ZONES = 'read:economic_zones';
    const SMP_EMPLOYEE_CONFIGURATIONS = 'read:employee_configurations';
    const SMP_EMPLOYEE_PLAN_PARTICIPATIONS = 'read:employee_plan_participations';
    const SMP_EMPLOYMENTS_PLAN_STATUSES = 'read:employments_plan_statuses';
    const SMP_ENVELOPES = 'read:envelopes';
    const SMP_GLIDEPATHS = 'read:glidepaths';
    const SMP_GLIDEPATH_STEPS = 'read:glidepath_steps';
    const SMP_GROUPS = 'read:groups';
    const SMP_KNOW_YOUR_CUSTOMER_DATA = 'read:know_your_customer_data';
    const SMP_MARKETING_PREFERENCES = 'read:marketing_preferences';
    const SMP_PAYROLL_CONFIGURATIONS = 'read:payroll_configurations';
    const SMP_PORTFOLIOS = 'read:portfolios';
    const SMP_POSTPONEMENTS = 'read:postponements';
    const SMP_PROVIDER_SCHEME_MIGRATIONS = 'read:provider_scheme_migrations';
    const SMP_PAYROLL_SALARIES = 'read:salaries';
    const SMP_SALARIES = 'read:salaries';
    const SMP_SCHEME_DETAILS = 'read:scheme_details';
    const SMP_COMPANY_TAX_RELIEFS = 'read:company_tax_reliefs';
    const SMP_SCHEMES = 'read:schemes';
    const SMP_SHORTCUTS = 'read:shortcuts';
    const SMP_TARGET_DATE_FUND_GROUPS = 'read:target_date_fund_groups';
    const SMP_VALUATIONS = 'read:valuations';
    const SMP_ADVISER_COMPANIES = 'read:adviser_companies';
}
