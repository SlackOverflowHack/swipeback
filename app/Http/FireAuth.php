<?php

namespace App\Http;

use Auth;
use Illuminate\Contracts\Session\Session;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class FireAuth {

    /**
     * authenticates a firebase user
     * @param String user access token
     * @return ['success' => isAuthenticated, 'userID' => userId ]
     */
    public function authenticate($accessToken) {

        $projectId = env('FIREBASE_PROJECT_ID');

        $verifier = IdTokenVerifier::createWithProjectId($projectId);
    
        try {
            $response = $verifier->verifyIdToken($accessToken);

            $token = $response->payload()['user_id'];

            return [
                'success' => true,
                'token' => $token
            ];
        } catch (IdTokenVerificationFailed $e) {
            return [
                'success' => false
            ];
    }
    }
}