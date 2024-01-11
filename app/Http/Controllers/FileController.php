<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::get();
       return view('demo',get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|mimes:doc,docx,pdf,xls,xlsx|max:10240',
        ]);
        
        if($request->file('files') == ''){
            return redirect()->back()->with('success', 'Please select File(s) for upload');
        }else{
        foreach ($request->file('files') as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);

            // Store file details in the database
            $fileModel = new File;
            $fileModel->filename = $fileName;
            $fileModel->path = 'uploads/' . $fileName;
            $fileModel->save();
        }
        return redirect()->back()->with('success', 'Files uploaded successfully');
    }
    }

    public function FileById(Request $request){
        $data = File::find($request->id);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        //
    }
}
