<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    public function index(){
        return view('back.pricelist.price-list');
    }
}
