<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Illuminate\Http\Request;

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
            $this->validate($request, [
                'name' => 'required'
            ]);
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
            $this->validate($request, [
                'name' => 'required'
            ]);
            $category = ProductCategory::findorfail($id);
            $category->name = $request->name;
            $category->description = $request->description;

            if ($category->save()){
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
            $this->validate($request, [
                'name' => 'required'
            ]);
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
