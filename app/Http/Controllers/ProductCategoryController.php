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
        $categories = ProductCategory::all();
        foreach ($categories as $category){
            $subCategories = $category->subCategories;
        }
        return $categories;
    }
    public function showCategory($id)
    {
        $category = ProductCategory::find($id);
        $subCategories = $category->subCategories;
        return $category;
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
            $category = ProductCategory::find($categoryId);
            if ($category){

                $validator = Validator::make($request->all(), [
                    'name' => 'required'
                ]);

                if ($validator->fails()) {
                    return $validator->errors();
                }

                $subCategories = $category->subCategories;
                foreach ($subCategories as $subCategory){
                    if($subCategory->name == $request->name){
                        return response()->json([
                            'status'=>'error',
                            'message'=>'The sub category name has already been taken'
                        ]);
                    }
                }

                $subCategory = ProductSubCategory::create([
                    'name'=>$request->name,
                    'description'=>$request->description
                ]);

                if ($category->subCategories()->save($subCategory)){
                    return response()->json([
                        'status'=>'success',
                        'message'=>'Sub Category created successfully',
                        'category'=>$subCategory
                    ]);
                }

            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'No category for id '.$categoryId
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function getSubCategories($categoryId){
        try {
            $category = ProductCategory::find($categoryId);
            if ($category){
                $subCategories = $category->subCategories;
                return $subCategories;
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'No category for id '.$categoryId
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function showSubCategory($categoryId,$id){
        try {
            $category = ProductCategory::find($categoryId);
            if ($category){
                if($category->subCategories->find($id)){
                    return $category->subCategories->find($id);
                }

                return response()->json([
                    'status'=>'error',
                    'message'=>'No sub category for id '.$id
                ]);
            }
            else{
                return response()->json([
                    'status'=>'error',
                    'message'=>'No category for id '.$categoryId
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
