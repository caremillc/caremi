<?php return array (
  'providers' => 
  array (
    0 => 'App\\Providers\\AppServiceProvider',
    1 => 'App\\Providers\\ReportServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'App\\Providers\\AppServiceProvider',
  ),
  'deferred' => 
  array (
    'reports' => 'App\\Providers\\ReportServiceProvider',
    'App\\Services\\Reports\\ReportManager' => 'App\\Providers\\ReportServiceProvider',
  ),
);
