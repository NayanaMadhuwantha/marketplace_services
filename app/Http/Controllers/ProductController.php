<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required',
                'quantity' => 'required',
                'basePrise' => 'required'
            ]);

            $userId = Auth::id();
            $user = User::find($userId);

            if (empty($user)){
                return response()->json(['status'=>'error', 'message'=>'token expired']);
            }

            $product = Product::create($request->all());
            $user->products()->save($product);

            if (!empty($product)){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Product created successfully',
                    'product'=>$product
                ]);
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product not created'
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        return Product::find($id);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->delete()){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Product deleted successfully'
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
