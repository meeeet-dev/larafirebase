<?php

return [
    /**
     * Firebase Project Id.
     */
    'project_id' => env('FIREBASE_PROJECT_ID', ''),

    /**
     * Firebase credentials file path.
     */
    'firebase_credentials' => env('FIREBASE_CREDENTIALS_FILE_PATH', '') ?: public_path('firebase_credentials.json')
];
