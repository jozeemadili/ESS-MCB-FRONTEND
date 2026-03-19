<?php

namespace App\Http\Controllers\API\Places;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Region;
use App\Models\Ward;
use Illuminate\Http\Request;

class PlacesController extends Controller
{
    public function getRegions()
    {
      return Region::get();
    }

    public function searchRegions(Request $request)
    {
      return Region::where('name', $request->name)->with('districts.wards')->get();
    }

    public function getDistricts()
    {
      return District::with('wards')->with('region')->get();
    }

    public function searchDistricts(Request $request)
    {
      return District::where('name', $request->name)->with('wards')->with('region')->get();
    }

    public function getWards()
    {
      return Ward::with('district.region')->get();
    }

    public function searchWards(Request $request)
    {
      return Ward::where('name', $request->name)->with('district.region')->get();
    }
}
