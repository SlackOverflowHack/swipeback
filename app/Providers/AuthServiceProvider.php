<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Laravel\Auth\BindFailureListener;

// used for token guard
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\mosquittoTokens;
use App\Models\frameTokens;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // add homes api token guard
        Auth::viaRequest('token', function(Request $request) {

            $validator = Validator::make($request->all(), [
                'token' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'version' => 'required|string|max:30'
            ]);

            if($validator->fails()) abort(403, 'token, password or version missing');

            $token = mosquittoTokens::find($request->token);
            if($token === null) abort(403, 'token or password wrong');

            if(!Hash::check($request->password, $token->password)) abort(403, 'token or password wrong');

            return $token;
        });

        // add token guard for lamaframe client devices
        Auth::viaRequest('frameDevice', function(Request $request) {

            $authString = $request->header('X-LamaFrame-Auth');
            if($authString === null) abort(401);
            
            $authStringParts = explode(':', $authString);

            try {
                $credentials = [
                    'token' => $authStringParts[0],
                    'password' => $authStringParts[1],
                    'version' => $authStringParts[2]
                ];

                $validator = Validator::make($credentials, [
                    'token' => 'required|string|max:255',
                    'password' => 'required|string|max:255',
                    'version' => 'required|string|max:30'
                ]);

                if($validator->fails()) abort(403, 'token, password or version missing');
            } catch(\Exception $e) {
                abort(403, 'token, password or version missing');
            }

            $token = frameTokens::find($credentials['token']);
            if($token === null) abort(403, 'token or password wrong');

            if(!Hash::check($credentials['password'], $token->password)) abort(403, 'token or password wrong');

            return $token;
        });

        // add passport routes
        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }

        Passport::tokensCan([
            'devices-read' => 'Geräteinformationen lesen',
            'devices-set' => 'Geräte schalten'
        ]);

        Fortify::authenticateUsing(function ($request) {

            $validated = Auth::validate([
                'mail' => $request->email,
                'password' => $request->password
            ]);

            return $validated ? Auth::getLastAttempted() : null;
        });
    }
}
