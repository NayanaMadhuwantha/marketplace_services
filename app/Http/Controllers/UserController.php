<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getCurrentUser(Request $request){
        $userId = Auth::id();
        return User::find($userId);
    }

    public function uploadProfilePicture(Request $request){
        try {
            $this->validate($request, [
                'picture' => 'mimes:jpeg,jpg,png,gif|required|max:10000' //max 10mb
            ]);

            $file = $request->file('picture');
            $fileName = $file->hashName();

            $filePath = $file->store('public/profile_pictures');

            if ($filePath){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Profile picture uploaded successfully',
                    'file_path'=>$filePath
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }
}
