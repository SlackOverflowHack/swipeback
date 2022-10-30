<?php

namespace App\Http\Controllers;

use App\Http\GuzzleRequest;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $fields = [
            'email'     => 'required|email|max:255',
            'password'  => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'birthDate' => 'required'
        ];

        $request->validate($fields);
        $request->only(array_keys($fields));

        // user has all necessary information

        // register him at firebase auth manager
        $newUserData = $this->registerFirebaseUser($request->email, $request->password);

        // save new user id
        $request->session()->flash('userID', $newUserData->localId);

        // set firestore collection
        $this->update($request);

        return $newUserData;
    }

    private function registerFirebaseUser($email, $password)
    {
        $apiKey = env('FIREBASE_API_KEY');
        $url = 'https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=' . $apiKey;
        $req = new GuzzleRequest($url);
        $json = $req->post(["email" => $email, "password" => $password]);

        $response = json_decode($json);

        if (isset($response->error)) abort($response->error->code, $response->error->message);

        return $response;
    }

    public function update(Request $request)
    {
        $firestore = new FirestoreClient();

        $user = $firestore->collection('users')->document($request->session()->get('userID'));
        $data = $request->only(['firstname', 'lastname', 'birthDate']);

        $response = $user->set($data, ['merge' => true]);

        if (!isset($response['updateTime'])) abort(500, "error updating user information");

        return 200;
    }
}
