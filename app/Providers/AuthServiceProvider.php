<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\FireAuth;

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

        // add token guard for firebase user client devices
        Auth::viaRequest('fireuser', function(Request $request) {

            $authString = $request->header('Authorization');
            if($authString === null) abort(401, 'No accessToken provided');

            $authString = str_replace('Bearer ', '', $authString);

            $fa = new FireAuth();
            $auth = $fa->authenticate($authString);

            if(!$auth['success']) abort(401, 'you have no valid accessToken for this project');

            $request->session()->flash('userID', $auth['token']);

            return $auth['token'];
        });

        // add passport routes
        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }

        Fortify::authenticateUsing(function ($request) {

            $validated = Auth::validate([
                'mail' => $request->email,
                'password' => $request->password
            ]);

            return $validated ? Auth::getLastAttempted() : null;
        });
    }
}
