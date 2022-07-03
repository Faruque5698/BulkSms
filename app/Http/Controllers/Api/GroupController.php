<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'group_name' => 'required',
        ]);

        $group = new Group();
        $group->user_id = auth()->user()->id;
        $group->group_name = $request->group_name;
        $group->save();

        return response()->json([
            'message' => 'success',
            'user' => auth()->user(),
            'group' => $group
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'group_id' => 'required',
//            'name' => 'required',
//            'number' => 'required'
        ]);

//        $group = new Group();
//        $group->user_id = auth()->user()->id;
//        $group->group_name = $request->group_name;
//        $group->save();

//        if($request->isMethod('post')){
        $bookData = $request->all();

        foreach ($bookData['books'] as $key => $value) {
            $book = new Contact();
            $book->user_id = auth()->user()->id;
            $book->group_id = $request->group_id;
            $book->name = $value['name'];
            $book->number = $value['number'];
            $book->save();
            }
//            return response()->json(['message'=>'Books added Successfully']);

//        $contact = new Contact();
//        $contact->user_id = auth()->user()->id;
//        $contact->group_id = $group->id;
//        $contact->name = $request->name;
//        $contact->number=$request->number;
//        $contact->save();

            return response()->json([
                'message' => 'success',
                'contact' => $bookData['books'],

            ]);
        }

        public function csv(Request $request){

        $request->validate([
           'csv'=>'required'
        ]);

        $csv = $request->has('csv');
            $cols = ["name","number"];

            $output = [];

            foreach ($csv as $line_index => $line) {
                if ($line_index > 0) { // I assume the the first line contains the column names.
                    $newLine = [];
                    $values = explode(',', $line);
                    foreach ($values as $col_index => $value) {
                        $newLine[$cols[$col_index]] = $value;
                    }
                    $output[] = $newLine;
                }
            }

            $json_output = json_encode($output);



        }

}
