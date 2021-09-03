<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request,$productId){
        try {
            $product = Product::find($productId);
            $currentUser = User::getCurrentUser();
            $cart = $currentUser->carts->where('product_id','=',$product->id);

            if ($cart->isEmpty()){
                $cart = $currentUser->carts()->save(Cart::create([
                    'quantity' => $request->quantity
                ]));
                $product->carts()->save($cart);

                $added_products = [];
                foreach (User::getCurrentUser()->carts as $userCart) {
                    $product = $userCart->product;
                    $product->quantity_in_cart = $userCart->quantity;
                    array_push($added_products,$userCart->product);
                }

                return response()->json([
                    'status'=>'success',
                    'message'=>'Product added successfully',
                    'added_items' => $added_products
                ]);

            }else{
                $added_products = [];
                foreach (User::getCurrentUser()->carts as $userCart) {
                    $product = $userCart->product;
                    $product->quantity_in_cart = $userCart->quantity;
                    array_push($added_products,$userCart->product);
                }

                return response()->json([
                    'status'=>'error',
                    'message'=>'Product already added to cart',
                    'added_items' => $added_products
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function showCart(){
        $added_products = [];
        foreach (User::getCurrentUser()->carts as $userCart) {
            $product = $userCart->product;
            $product->quantity_in_cart = $userCart->quantity;
            array_push($added_products,$userCart->product);
        }
        return $added_products;
    }

    public function RemoveFromCart($productId){
        if($cart = User::getCurrentUser()->carts->where('product_id','=',$productId)->first()){
            $cart->delete();
            return response()->json([
                'status'=>'success',
                'message'=>'Product removed from cart'
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'Product not found in cart'
            ]);
        }
    }

    public function setQuantity(Request $request,$productId){
        try {
            $currentUser = User::getCurrentUser();
            $cart = $currentUser->carts->where('product_id','=',$productId)->first();
            if ($cart){
                $cart->quantity = $request->quantity;
                if ($cart->save()){
                    return response()->json([
                        'status'=>'success',
                        'message'=>'set quantity as '.$cart->quantity
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
}
