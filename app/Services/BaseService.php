<?php

namespace App\Services;

use Illuminate\Http\Request;

abstract class BaseService
{
    abstract public function store(Request $request);
}
