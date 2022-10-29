<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $data = $request->only(['email', 'password']);

        $firestore = new FirestoreClient();
        $response = $firestore->collection('users')->newDocument()->set($data);

        if(!isset($response['updateTime'])) abort(500, 'error while registering user');

        return 200;
    }
}
