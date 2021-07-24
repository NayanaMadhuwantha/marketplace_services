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
        return Product::paginate(20);
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required',
                'quantity' => 'required',
                'currentPrice' => 'required',
                'thumbnail' => 'mimes:jpeg,jpg,png,gif|required|max:1000'
            ]);

            $user = User::getCurrentUser();

            if (empty($user)){
                return response()->json(['status'=>'error', 'message'=>'token expired']);
            }

            $file = $request->file('thumbnail');
            $filePath = $file->store('public/product_thumbnails');

            $product = Product::create([
                'name'=>$request->name,
                'description'=>$request->description,
                'specifications'=>$request->specifications,
                'status'=>$request->status,
                'quantity'=>$request->quantity,
                'rating'=>$request->rating,
                'popularity'=>$request->popularity,
                'trending'=>$request->trending,
                'basePrise'=>$request->basePrise,
                'thumbnailLink'=>$filePath,
                'currentPrice'=>$request->currentPrice,
            ]);

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
        try {
            $this->validate($request, [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required',
                'quantity' => 'required',
                'currentPrice' => 'required',
                'thumbnail' => 'mimes:jpeg,jpg,png,gif|required|max:1000'
            ]);

            $product = Product::findorfail($id);

            if($product->user->id!=User::getCurrentUser()->id){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product is belongs to another user'
                ]);
            }

            $file = $request->file('thumbnail');
            $filePath = $file->store('public/product_thumbnails');

            $product->name=$request->name;
            $product->description=$request->description;
            $product->specifications=$request->specifications;
            $product->status=$request->status;
            $product->quantity=$request->quantity;
            $product->rating=$request->rating;
            $product->popularity=$request->popularity;
            $product->trending=$request->trending;
            $product->basePrise=$request->basePrise;
            $product->currentPrice=$request->currentPrice;
            $product->thumbnailLink=$filePath;

            if ($product->save()){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Product updated successfully',
                    'product'=>$product
                ]);
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product not updated'
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
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

    public function getProductsOfCurrentUser(){
        $user = User::getCurrentUser();
        return $user->products()->paginate(20);
    }
}
