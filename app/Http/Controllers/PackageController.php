<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Category;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $package = Package::where('package_type', request()->type)->get();

        $this->responseCode = 200;
        $this->responseData = $package;

        return response()->json($this->getResponse(), $this->responseCode);
    }
}
