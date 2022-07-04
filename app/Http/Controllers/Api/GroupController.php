<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Group;
use App\Models\User;
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

        foreach ($bookData['contacts'] as $key => $value) {
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
                'contact' => $bookData['contacts'],

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


        public function list(){
            $user_id = auth()->user()->id;
            $groups = Group::where('user_id','=',$user_id)->get();
            if ($groups ->isEmpty()){
                return response()->json([
                    'message'=>'No group found'
                ],404);
            }else{
                return  response()->json([
                   'message'=>'Success',
                   'groups'=>$groups
                ]);
            }
        }

        public function delete($id){
            $user_id = auth()->user()->id;
            $group = Group::find($id);
            if ($group){
                if ($group->user_id == $user_id){

                    $group->delete();

                    return response()->json([
                        'message'=>'group delete successful',
                    ],200);
                }else{
                    return response()->json([
                        'message'=>'this is not your group',

                    ],401);
                }
            }else{
                return  response()->json([
                    'message'=>'group not found'
                ],404);
            }

        }

        public function contact_delete($id){

            $contact = Contact::find($id);
            $user_id = auth()->user()->id;
            if ($contact){
                if ($contact->user_id == $user_id){
                    $contact->delete();
                    return  response()->json([
                        'message'=>"contact delete successfully"
                    ],200);
                }else{
                    return  response()->json([
                       'message'=>'This is not your contact '
                    ],401);
                }
            }else{
                return response()->json([
                   'message'=>'Contact not found'
                ]);
            }

        }

        public function group_contact($id){
            $group = Group::find($id);
            $user_id = auth()->user()->id;
            if ($group){
                if ($group->use_id == $user_id){
                    $contacts = Contact::where('group_id','=',$id)->get();
                    $data = ['group'=>$group,'contacts' => $contacts];
                    return  response()->json([
                       'message'=>'success',
                       'data'=>$data
                    ]);
                }else{
                    return  response()->json([
                       'message'=>'this is not your group'
                    ],401);
                }
            }else{
                return response()->json([
                   'message'=>'group not found'
                ],404);
            }
        }

}
