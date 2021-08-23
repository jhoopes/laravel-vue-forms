<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Admin;

use jhoopes\LaravelVueForms\App\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $this->authorizeAdminRequest();
        return view('forms::admin');
    }

}
