<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Sop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class SopShortcutCreateController extends Controller
{
    public function createShortcut(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description'        => 'required',
                'category'        => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 200);
            }

            $sop                  = new Sop;
            $sop->name            = $request->name;
            $sop->category        = $request->category;
            $sop->chat_message_id = $request->chat_message_id;
            $sop->content         = $request->description;
            $sop->save();

        return response()->json(['status' => true, 'message' => 'Sop Created Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
