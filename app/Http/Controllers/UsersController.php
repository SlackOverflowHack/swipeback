<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class UsersController extends Controller {
    public function register(Request $request) {
        $request->validate([
            'email'    => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $data = $request->only(['email', 'password']);

        $firestore = new FirestoreClient();
        $response = $firestore->collection('users')->newDocument()->set($data);

        if (isset($response['updateTime'])) return 200;

        abort(500, 'error while registering user');
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $user = $firestore->collection('users')->document($request->id);
        if ($user->snapshot()->exists()) {
            $data = $request->only(['email', 'password', 'firstname', 'lastname', 'birthDate']);

            $response = $user->set($data, ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while updating user');
    }
}
