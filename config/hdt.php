<?php

return [

    'company' => 'HealthData Talent UK Limited',
    'contact_email' => env('HDT_CONTACT_EMAIL', 'hello@healthdatatalent.co.uk'),

    /*
    | Security and data protection
    */
    'require_2fa' => env('HDT_REQUIRE_2FA', true),
    'retention_months' => (int) env('HDT_RETENTION_MONTHS', 24),
    'privacy_version' => env('HDT_PRIVACY_VERSION', '2026-10'),
    'cv_max_kb' => 5120,
    // Full path to clamscan / clamdscan. When empty, uploads are marked "unscanned".
    'clamav_path' => env('HDT_CLAMAV_PATH'),

    /*
    | Form options. Keys are stored, values are shown.
    */
    'sectors' => [
        'NHS organisation', 'Health-tech company', 'Healthcare provider', 'University or research',
        'Public health', 'Health consultancy', 'NGO or international development', 'Other',
    ],

    'services' => [
        'recruitment' => 'Specialist recruitment',
        'sourcing' => 'Shortlist sourcing',
        'mapping' => 'Talent mapping',
        'ngo-doctor' => 'Doctor for remote NGO work',
        'unsure' => 'Not sure yet',
    ],

    'patterns' => ['On-site', 'Hybrid', 'Remote (UK)', 'Remote (international)'],
    'candidate_patterns' => ['On-site', 'Hybrid', 'Remote'],
    'contract_types' => ['Permanent', 'Fixed-term (engaged directly by employer)'],

    'areas' => [
        'data' => 'Health data',
        'informatics' => 'Informatics',
        'digital' => 'Digital health',
        'doctor' => 'Doctors / NGO',
    ],

    'years' => ['Less than 2', '2–4', '5–9', '10+'],

    'qualifications' => [
        'A-levels or equivalent', "Bachelor's degree", "Master's degree", 'PhD',
        'Medical degree (MBBS/MBChB/MD)', 'Professional certification',
    ],

    'expertise' => [
        'Health data analytics', 'Health informatics', 'Clinical informatics', 'EPR/EHR',
        'Population health', 'Data science', 'Data engineering', 'Digital transformation',
    ],

    'tools' => [
        'SQL', 'Power BI', 'Python', 'R', 'Tableau', 'Azure', 'FHIR/HL7', 'Epic',
        'Oracle Health/Cerner', 'SystmOne', 'EMIS', 'NHS datasets',
    ],

    'ngo_work' => [
        'Clinical advisory', 'Guideline development', 'Telemedicine', 'Research and evaluation', 'Training',
    ],

    'availability' => ['Immediately', '1 month', '3 months', 'Just exploring'],

    'right_to_work' => [
        'Yes, without sponsorship', 'Need sponsorship', 'Not applicable (remote international)',
    ],

    'candidate_statuses' => [
        'new' => 'New',
        'screened' => 'Screened',
        'active' => 'Active',
        'placed' => 'Placed',
        'inactive' => 'Inactive',
    ],

    'stages' => [
        'new' => 'New',
        'shortlisted' => 'Shortlisted',
        'interview' => 'Interview',
        'offer' => 'Offer',
        'placed' => 'Placed',
        'rejected' => 'Not progressed',
    ],

    'post_categories' => ['Hiring', 'Careers', 'Informatics', 'Global health'],
];
