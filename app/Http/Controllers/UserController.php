<?php

namespace App\Http\Controllers;

use App\Models\AddressBook;
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
                $userId = Auth::id();
                $user = User::findOrFail($userId);
                $user->ProfileImageLink = $filePath;

                if($user->save()){
                    return response()->json([
                        'status'=>'success',
                        'message'=>'Profile picture uploaded successfully',
                        'file_path'=>env("APP_URL")."/".$filePath
                    ]);
                }
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function updateProfile(Request $request){
        try {
            $userId = Auth::id();
            $user = User::findOrFail($userId);
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->birthday = $request->birthday;
            $user->gender = $request->gender;
            $user->mobile = $request->mobile;
            $user->telephone = $request->telephone;

            if ($user->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile updated successfully'
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function addAddress(Request $request){
        try {
            $this->validate($request, [
                'fullName' => 'required',
                'mobile' => 'required',
                'streetAddress1' => 'required',
                'city' => 'required',
                'country' => 'required',
                'zipCode' => 'required',
            ]);

            $userId = Auth::id();
            $user = User::find($userId);

            $address = AddressBook::create($request->all());
            $user->addresses()->save($address);

            if ($user->save()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Address updated successfully',
                    'address' => $address
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function getAddresses(){
        try {
            $userId = Auth::id();
            $user = User::find($userId);

            return $user->addresses;
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function getAddress($id){
        try {
            $userId = Auth::id();
            $user = User::find($userId);

            foreach ($user->addresses as $address) {
                if($address->id==$id){
                    return $address;
                }
            }
            return response()->json([
                'status'=>'error',
                'message'=>'no address found'
            ]);

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function updateAddress(Request $request,$id){
        try {
            $this->validate($request, [
                'fullName' => 'required',
                'mobile' => 'required',
                'streetAddress1' => 'required',
                'city' => 'required',
                'country' => 'required',
                'zipCode' => 'required',
            ]);

            $address = AddressBook::findOrFail($id);

            $address->fullName = $request->fullName;
            $address->mobile = $request->mobile;
            $address->streetAddress1 = $request->streetAddress1;
            $address->city = $request->city;
            $address->country = $request->country;
            $address->zipCode = $request->zipCode;

            if($address->save()){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Address updated successfully',
                    'address' => $address
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function destroyAddress($id)
    {
        try {
            $address = AddressBook::findOrFail($id);

            if ($address->delete()){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Address deleted successfully'
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
