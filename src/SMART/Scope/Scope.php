<?php

namespace SMART\Scope;

class Scope
{
    const SMP_USER = 'user';
    const SMP_CUSTOMER = 'customer';

    const SMP_COMPANIES = 'read:companies';
    const SMP_CUSTOMERS = 'read:customers';
    const SMP_EMPLOYEES = 'read:employees';
    const SMP_BENEFIT_GROUPS = 'read:benefit_groups';
    const SMP_CONTRIBUTIONS = 'read:contributions';
    const SMP_EMPLOYEE_CONFIGURATIONS = 'read:employee_configurations';
    const SMP_GROUPS = 'read:groups';
    const SMP_PAYROLL_CONFIGURATIONS = 'read:payroll_configurations';
    const SMP_PAYROLL_SALARIES = 'read:salaries';
    const SMP_SCHEME_DETAILS = 'read:scheme_details';
    const SMP_COMPANY_TAX_RELIEFS = 'read:company_tax_reliefs';
    const SMP_SCHEMES = 'read:schemes';
    const SMP_ADVISER_COMPANIES = 'read:adviser_companies'; 
}
