<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function add_template(Request $request){
        $request->validate([
//           'template_name'=>'required',
           'message'=>'required'
        ]);

        $template = new Template();
        $template->user_id = auth()->user()->id;
        $template->template_name = $request->template_name;
        $template->message = $request->message;
        $template->save();

        return response()->json([
           'message'=>'template create successfully',
            'template'=>$template
        ]);
    }

    public function list()
    {
        $templates = Template::where('user_id', '=', auth()->user()->id)->get();
        return response()->json([
            'message' => 'success',
            'template' => $templates
        ]);
    }

    public function delete($id){
        $template = Template::find($id);
        $user_id = auth()->user()->id;
        if ($template){
            if ($template->user_id == $user_id){
                $template->delete();
                return  response()->json([
                    'message'=>'Template Successfully deleted'
                ],200);
            }else{
                return response()->json([
                    'message'=>'This is not your template'
                ],401);
            }
        }else{
            return response()->json([
               'message'=>'Template not found'
            ],404);
        }



    }
}
