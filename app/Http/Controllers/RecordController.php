<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;

class RecordController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getRecords()
    {
        $records = Record::all();
        return response()->json($records);
    }

    public function store(Request $request)
    {
        $record = $request->isMethod('put') ? Record::findOrFail($request->id) : new Record();

        $record->name = $request->name;
        $record->number = $request->number;
        $record->email = $request->email;
        $record->description = $request->description;

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $record->image = $filename;
        }

        $record->save();

        return response()->json(['success' => 'Record saved successfully']);
    }

    public function show($id)
    {
        $record = Record::find($id);
        return response()->json($record);
    }

    public function update(Request $request, $id)
    {
        $record = Record::find($id);
        $record->name = $request->name;
        $record->number = $request->number;
        $record->email = $request->email;
        $record->description = $request->description;

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $record->image = $filename;
        }

        $record->save();

        return response()->json(['success' => 'Record updated successfully']);
    }

    public function destroy($id)
    {
        $record = Record::find($id);
        $record->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
}
