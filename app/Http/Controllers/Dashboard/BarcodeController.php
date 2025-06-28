<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('dashboard.barcode');
    }

    public function store(Request $request)
    {
        $code = $request->input('barcode');

        // Do something with $code: e.g. search products, store logs, etc.
        return response()->json(['message' => 'Barcode received', 'code' => $code]);
    }
}
