<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    /**
     * Creates new Street Data and returns ID
     * 
     * @param \Illuminate\Http\Request $request
     * @return Sector|\Illuminate\Database\Eloquent\Model
     */
    public function store(Request $request){
        $data = $request->validate([
            "name" => "required|string"
        ]);

        $sector = Sector::create($data);

        return $sector;
    }
}
