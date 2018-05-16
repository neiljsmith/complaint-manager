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
        $this->middleware('checkRoles:' . Role::ROLE_SUPER_ADMIN);
    }

    /**
     * Fetches remaining reward data and formats
     * it suitable for rendering in a table.
     *
     * @return \Illuminate\Http\Response
     */
    public function stock()
    {
        $stock = Reward::stockReport();

        return view('reports.stock', compact('stock'));
    }
}
