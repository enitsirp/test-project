<?php

namespace App\Http\Controllers;

use App\Notes;
use Illuminate\Http\Request;
use Auth;

class NoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        return view('notes');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $notes = [];
        if(!$user->is_admin){
            
            $notes = Notes::where('created_by', $user->id)->get();

        } else {
            $notes = Notes::all();
        }
   
        return response()->json($notes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $test = array('key1' => "value" );
        return response($test->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       return Notes::create([
                    'title' => $request->title,
                    'note_details' => $request->note_details,
                    'created_by' => Auth::user()->id
                ]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $notes = Notes::find($id);
        $updates = $notes->update($request->all());
        
        return $updates;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notes  $notes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notes = Notes::find($id);
        $delete = $notes->delete();
        return response()->json($delete);
    }
}
