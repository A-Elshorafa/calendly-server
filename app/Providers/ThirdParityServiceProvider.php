<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ThirdParityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->call([$this, "thirdParityBinding"]);
    }

    /**
     * predict the related third parity provider from the request data
     * bind defined third parity provider to it's interface
     * 
     * Eventually we have an interface to access the current relevent repository (Zoom, Google , ...)
    */
    public function thirdParityBinding(Request $request)
    {
        $thirdParity = $request->get('third_parity_name');
        if (!empty($thirdParity) && class_exists("\App\Repositories\\". ucfirst($thirdParity) ."Repository")) {
            $this->app->bind(
                \App\Repositories\ThirdPartyRepositoryInterface::class, 
                "\App\Repositories\\". ucfirst($thirdParity) ."Repository", 
            );
        }
    }
}
