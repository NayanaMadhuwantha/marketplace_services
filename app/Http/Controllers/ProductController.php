<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::paginate(20);
        foreach ($products as $product){
            $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
            $productSubCategory = ProductSubCategory::find($product->product_sub_category_id);
            $product->product_category_id = ProductCategory::find($productSubCategory->product_category_id)->id;
        }
        return $products;
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required|in:available,soldOut',
                'quantity' => 'required',
                'currentPrice' => 'required',
                'thumbnail' => 'mimes:jpeg,jpg,png,gif|required|max:1000',
                //'categoryId' => 'required',
                'subCategoryId' => 'required'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            $user = User::getCurrentUser();

            if (empty($user)){
                return response()->json(['status'=>'error', 'message'=>'token expired']);
            }

            $file = $request->file('thumbnail');
            $filePath = $file->store('public/product_thumbnails');

            $product = null;
            //$subCategory = ProductCategory::find($request->categoryId)->subCategories->find($request->subCategoryId);
            $subCategory = ProductSubCategory::find($request->subCategoryId);
            if ($subCategory){
                $product = Product::create([
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'specifications'=>$request->specifications,
                    'status'=>$request->status,
                    'quantity'=>$request->quantity,
                    'basePrise'=>$request->basePrise,
                    'thumbnailLink'=>$filePath,
                    'currentPrice'=>$request->currentPrice,
                ]);

                $user->products()->save($product);
                $subCategory->products()->save($product);
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product sub category not found'
                ]);
            }

            if (!empty($product)){
                $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
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

    public function storeExternalProduct(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required|in:available,soldOut',
                'quantity' => 'required',
                'currentPrice' => 'required',
                'thumbnail' => 'mimes:jpeg,jpg,png,gif|max:1000',
                //'categoryId' => 'required',
                'subCategoryId' => 'required',
                'externalLink' => 'required',
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            $user = User::getCurrentUser();

            $file = $request->file('thumbnail');
            $filePath = null;
            if ($file) {
                $filePath = $file->store('public/product_thumbnails');
            }

            //$subCategory = ProductCategory::find($request->categoryId)->subCategories->find($request->subCategoryId);
            $subCategory = ProductSubCategory::find($request->subCategoryId);
            if ($subCategory){
                $product = Product::create([
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'specifications'=>$request->specifications,
                    'status'=>$request->status,
                    'quantity'=>$request->quantity,
                    'basePrise'=>$request->basePrise,
                    'thumbnailLink'=>$filePath,
                    'currentPrice'=>$request->currentPrice,
                    'externalLink'=>$request->externalLink,
                    'isExternal' => 1
                ]);

                $user->products()->save($product);
                $subCategory->products()->save($product);
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product sub category not found'
                ]);
            }

            if (!empty($product)){
                if($product->thumbnailLink){
                    $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
                }
                return response()->json([
                    'status'=>'success',
                    'message'=>'Product created successfully',
                    'product'=> $product
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
        $product = Product::find($id);
        if (!$product){
            return response()->json([
                'status'=>'error',
                'message'=>'Product not found for id '.$id
            ]);
        }
        $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
        $productSubCategory = ProductSubCategory::find($product->product_sub_category_id);
        $product->product_category_id = ProductCategory::find($productSubCategory->product_category_id)->id;
        return $product;
    }

    public function getProductOfCurrentUser($id)
    {
        $product = Product::find($id);

        if (!$product){
            return response()->json([
                'status'=>'error',
                'message'=>'Product not found for id '.$id
            ]);
        }

        if($product->user->id!=User::getCurrentUser()->id){
            return response()->json([
                'status'=>'error',
                'message'=>'Product not found'
            ]);
        }

        $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
        return $product;
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product not found for id '.$id
                ]);
            }
            if ($product->isExternal){
                return response()->json([
                    'status'=>'error',
                    'message'=>'This is a external product'
                ]);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required|in:available,soldOut',
                'quantity' => 'required',
                'currentPrice' => 'required',
                'thumbnail' => 'mimes:jpeg,jpg,png,gif|max:1000',
                //'categoryId' => 'required',
                'subCategoryId' => 'required'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            if($product->user->id!=User::getCurrentUser()->id){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product not found'
                ]);
            }


            //$subCategory = ProductCategory::find($request->categoryId)->subCategories->find($request->subCategoryId);
            $subCategory = ProductSubCategory::find($request->subCategoryId);

            if ($subCategory){
                $product->name=$request->name;
                $product->description=$request->description;
                $product->specifications=$request->specifications;
                $product->status=$request->status;
                $product->quantity=$request->quantity;
                $product->basePrise=$request->basePrise;
                $product->currentPrice=$request->currentPrice;

                $file = $request->file('thumbnail');
                if ($file){
                    $filePath = $file->store('public/product_thumbnails');
                    $product->thumbnailLink=$filePath;
                }

                if ($product->save()){
                    $subCategory->products()->save($product);
                    $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
                    unset($product->user);
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
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product sub category not found'
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function updateExternalProduct(Request $request,$id){
        try {
            $product = Product::find($id);

            if (!$product){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product not found for id '.$id
                ]);
            }
            if (!$product->isExternal){
                return response()->json([
                    'status'=>'error',
                    'message'=>'This is not a external product'
                ]);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required|in:available,soldOut',
                'quantity' => 'required',
                'currentPrice' => 'required',
                'thumbnail' => 'mimes:jpeg,jpg,png,gif|max:1000',
                //'categoryId' => 'required',
                'subCategoryId' => 'required',
                'externalLink' => 'required',
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            if($product->user->id!=User::getCurrentUser()->id){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product not found'
                ]);
            }

            //$subCategory = ProductCategory::find($request->categoryId)->subCategories->find($request->subCategoryId);
            $subCategory = ProductSubCategory::find($request->subCategoryId);
            if ($subCategory){
                $product->name=$request->name;
                $product->description=$request->description;
                $product->specifications=$request->specifications;
                $product->status=$request->status;
                $product->quantity=$request->quantity;
                $product->basePrise=$request->basePrise;
                $product->currentPrice=$request->currentPrice;

                $file = $request->file('thumbnail');
                if ($file){
                    $filePath = $file->store('public/product_thumbnails');
                    $product->thumbnailLink=$filePath;
                }

                if ($product->save()){
                    $subCategory->products()->save($product);
                    $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
                    unset($product->user);
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


            }else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'Product sub category not found'
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
        $products = $user->products()->paginate(20);
        foreach ($products as $product){
            $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
        }
        return $products;
    }

    public function getProductsByCategory($subcategoryId){
        $subCategory = ProductSubCategory::find($subcategoryId);
        if ($subCategory){
            return $subCategory->products()->paginate(20);
        }
        else{
            return response()->json([
                'status'=>'error',
                'message'=>'Product sub category not found'
            ]);
        }
    }

    public function incrementPopularity($productId){
        try {
            $product = Product::find($productId);
            if($product->popularity){
                $product->popularity++;
            }
            else{
                $product->popularity=1;
            }
            $product->save();
            $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
            return $product;
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }
    public function getPopularProducts(){
        //$products = Product::all()->where('popularity','>','0')->sortByDesc('popularity');
        $queryProducts = Product::query();
        $queryProducts->orderBy('popularity','desc');
        $queryProducts->where('popularity','>','0');
        $products = $queryProducts->paginate(10);
        foreach ($products as $product){
            $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
        }
        return $products;
    }
    public function incrementTrend($productId){
        try {
            $product = Product::find($productId);
            if($product->trending){
                $product->trending++;
            }
            else{
                $product->trending=1;
            }
            $product->save();
            $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
            return $product;
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function getTrendingProducts(){
        $queryProducts = Product::query();
        $queryProducts->orderBy('trending','desc');
        $queryProducts->where('trending','>','0');
        $products = $queryProducts->paginate(10);
        foreach ($products as $product){
            $product->thumbnailLink = env("APP_URL")."/".$product->thumbnailLink;
        }
        return $products;
    }

    public function productCount(){
        $user = User::getCurrentUser();
        $products = $user->products()->count();
        return $products;
    }
}
