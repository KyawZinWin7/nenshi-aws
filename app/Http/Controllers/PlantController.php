<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plant;
use App\Http\Resources\PlantResource;
use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;

class PlantController extends Controller
{
    public function index()
        {

            $plants = PlantResource::collection(Plant::all());
            return inertia('Plants/Index',[
                'plants'=> $plants,
            ]);
        }
    
    public function create()
        {
            return inertia('Plants/Create');
        }

    public function store(StorePlantRequest $request)

        {
            Plant::create($request->validated());

            return redirect()->route('plants.index');
        }

     
    public function edit(Plant $plant)
        {
           return inertia('Plants/Edit',[
            'plant'=> PlantResource::make($plant)
           ]) ;
        }


    public function update(UpdatePlantRequest $request, Plant $plant)

        {
            $plant->update($request->validated());

            return redirect()->route('plants.index');
        }



    public function destroy(Plant $plant)

    {
        $plant->delete();


        return redirect ()->route('plants.index');
    }
}
