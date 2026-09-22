<?php

return [
    // --- Grupo 1: Configuración Local / Original ---
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASSWORD' => '',
    'DB_NAME' => 'orion',
    'APP_ENV' => 'development',
    'SMTP_EMAIL' => 'mytscloudcomputing@gmail.com',
    'SMTP_PASSWORD' => 'bwqm axmq xwlf uhxu',
    // 'NIT' => '06150911851010',
    // 'PRIVATE_KEY' => 'rmA1189203',
    // 'PUBLIC_KEY' => 'ac4310Rmriflextore1',
    // 'AUTH_KEY' => 'Ac4310RmA11892-03',
    'NIT' => '12172607490014',
    'PRIVATE_KEY' => 'Snagustin@20281',
    'PUBLIC_KEY' => 'Snagustin@172607',
    'AUTH_KEY' => 'ApiRest@20281',
    'CALLMEBOT_API_KEY' => '123456', // Placeholder, update locally
    'CERT_PATH' => 'C:\\Program Files\\NebulaPOS\\DET_service\\certificates\\',
    
    // --- Facturación Electrónica (Ministerio de Hacienda) ---
    'API_FACTURA_URL' => 'https://admin.factura.gob.sv/login',
    
    'API_AUTH_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/seguridad/auth',
    'API_AUTH_PROD_URL' => 'https://api.dtes.mh.gob.sv/seguridad/auth',
    
    'API_SIGNER_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/fesv/recepciondte',
    'API_SIGNER_PROD_URL' => 'https://api.dtes.mh.gob.sv/fesv/recepciondte',

    'API_SIGNERLOT_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/fesv/recepcionlote/',
    'API_SIGNERLOT_PROD_URL' => 'https://api.dtes.mh.gob.sv/fesv/recepcionlote/',

    'API_QUERY_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/fesv/recepcion/consultadte/',
    'API_QUERY_PROD_URL' => 'https://api.dtes.mh.gob.sv/fesv/recepcion/consultadte/',

    'API_QUERYLOT_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/fesv/recepcion/consultadtelote/',
    'API_QUERYLOT_PROD_URL' => 'https://api.dtes.mh.gob.sv/fesv/recepcion/consultadtelote/',

    'API_EVENT_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/fesv/contingencia',
    'API_EVENT_PROD_URL' => 'https://api.dtes.mh.gob.sv/fesv/contingencia',

    'API_NULL_TEST_URL' => 'https://apitest.dtes.mh.gob.sv/fesv/anulardte',
    'API_NULL_PROD_URL' => 'https://api.dtes.mh.gob.sv/fesv/anulardte',

    // --- Supabase / Logs ---
    'SUPABASE_URL' => 'https://iyteellzegojaoozwhev.supabase.co',
    'SUPABASE_KEY' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Iml5dGVlbGx6ZWdvamFvb3p3aGV2Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc0NDU2MzI2OSwiZXhwIjoyMDYwMTM5MjY5fQ.ObsQmuzfFiAc4tT0VGBQENdzycszozJ8ecHk6rqvA8c',
    'SUPABASE_TABLE' => 'firma_logs',

    // --- Grupo 2: Configuración de Base de Datos Externa (PostgreSQL) ---
    'DB_HOST_2' => 'aws-0-us-east-2.pooler.supabase.com',
    'DB_PORT' => '5432',
    'DB_NAME_2' => 'postgres',
    'DB_USER_2' => 'postgres.iyteellzegojaoozwhev',
    'DB_PASSWORD_2' => '6QtxhADJfRSci3jF',
    'POOLED_CONNECTION' => true,
    'DB_SSL_MODE' => 'require'
];