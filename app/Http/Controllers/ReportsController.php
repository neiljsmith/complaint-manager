<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reward;
use App\Role;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stock()
    {
        $stock = Reward::stockReport();

        return view('reports.stock', compact('stock'));
    }
}
