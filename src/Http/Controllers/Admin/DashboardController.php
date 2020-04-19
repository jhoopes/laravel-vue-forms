<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Admin;

use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class DashboardController extends Controller
{



    public function index()
    {
        $this->authorizeAdminRequest();
        return view('forms::admin');
    }


}
