<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function getCategories()
    {
        return ProductCategory::all();
    }
    public function showCategory($id)
    {
        return ProductCategory::find($id);
    }
    public function storeCategory(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:product_categories'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            $category = ProductCategory::create([
                'name'=>$request->name,
                'description'=>$request->description
            ]);
            if ($category){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Category created successfully',
                    'category'=>$category
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function updateCategory(Request $request,$id){
        try {
            $category = ProductCategory::find($id);
            if (!$category){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Category not found for id '.$id
                ]);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:product_categories,name,'.$category->id
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            $category->name = $request->name;
            $category->description = $request->description;

            if ($category->update()){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Category updated successfully',
                    'category'=>$category
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function deleteCategory($id){
        try {
            $category = ProductCategory::findOrFail($id);

            if ($category->delete()){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Category deleted successfully'
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function storeSubCategory(Request $request,$categoryId){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            $subCategory = ProductSubCategory::create([
                'name'=>$request->name,
                'description'=>$request->description
            ]);

            $category = ProductCategory::find($categoryId);

            if ($category){
                $category->subCategory()->save($category);
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'No category for id '.$categoryId
                ]);
            }

            if ($subCategory){
                return response()->json([
                    'status'=>'success',
                    'message'=>'Sub Category created successfully',
                    'category'=>$subCategory
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
