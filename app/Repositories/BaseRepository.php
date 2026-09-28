<?php

namespace App\Repositories;

use Illuminate\Http\Request;

abstract class BaseRepository
{
    abstract public function store(Request $request);
}
