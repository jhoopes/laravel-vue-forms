<?php

namespace jhoopes\LaravelVueForms\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected function authorizeAdminRequest()
    {
        if(LaravelVueForms::adminAuthorization()) {
            $this->authorize(LaravelVueForms::adminAuthorization());
        }
    }
}
