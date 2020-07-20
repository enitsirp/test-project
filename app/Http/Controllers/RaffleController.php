<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\RaffleEntry;


class RaffleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('raffle');
    }


    public function list()
    {
       $names = RaffleEntry::selected(false)->get();
       return response()->json($names);
    }

    public function selected()
    {
        $names = RaffleEntry::selected(true)->get();
        return response()->json($names);
    }

    public function restart()
    {
        $raffle = RaffleEntry::where('is_selected', true)->update(['is_selected'=> false]);
        return response()->json(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        
        $validator = Validator::make($data, [
            'raffle_name' => ['required', 'string', 'max:255', 'unique:raffle_entries']
        ]);

        if ($validator->fails()) {
          //  return redirect()->back()->withInput()->withErrors($validator);
        //   dd($validator);
            return response()->json($validator->errors(), 400);
        }

        return RaffleEntry::create($data); 
    }


    public function select()
    {
        $random = RaffleEntry::selected(false)->inRandomOrder();
      
        if($random->count() > 0){
            $randomData = $random->first();
            $selected = RaffleEntry::find($randomData->id);
            $selected->update(['is_selected' => true]);
            return response()->json($selected);
        }else{
            return response()->json(false);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
