<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Forms;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfiguration extends Controller
{


    public function show(Request $request, $formConfigId)
    {
        return \jhoopes\LaravelVueForms\Models\FormConfiguration::with('fields')->findOrFail($formConfigId);
    }

}