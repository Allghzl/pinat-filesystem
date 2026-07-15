<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function __construct(private StorageService $storageService)
    {
        $this->storageService = $storageService;
    }
}
