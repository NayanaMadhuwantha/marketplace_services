<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'api'], function () use ($router){
    $router->post('/register','AuthController@register');
    $router->post('/login','AuthController@login');

    $router->get('product/{id}','ProductController@show');

    $router->group(['prefix'=>'products'], function () use ($router){
        $router->get('/', 'ProductController@index');
        $router->get('/categories', 'ProductCategoryController@getCategories');
        $router->get('/{categoryId}/subcategories', 'ProductCategoryController@getSubCategories');
        $router->get('/{categoryId}/{subcategoryId}','ProductController@getProductsByCategory');

        $router->get('/popular','ProductController@getPopularProducts');
        $router->get('/trending','ProductController@getTrendingProducts');
        $router->post('/incrementPopularity/{productId}','ProductController@incrementPopularity');
        $router->post('/incrementTrend/{productId}','ProductController@incrementTrend');
    });

    $router->group(['middleware'=>'auth'],function() use ($router){

        $router->post('/logout','AuthController@logout');

        $router->get('/posts','PostController@index');
        $router->post('/posts', 'PostController@store');
        $router->put('/posts/{id}', 'PostController@update');
        $router->delete('/posts/{id}', 'PostController@destroy');

        $router->group(['prefix'=>'products'], function () use ($router){
            //Product Categories
            $router->post('/category', 'ProductCategoryController@storeCategory');
            $router->get('/category/{id}', 'ProductCategoryController@showCategory');
            $router->put('/category/{id}', 'ProductCategoryController@updateCategory');
            $router->delete('/category/{id}', 'ProductCategoryController@deleteCategory');

            //Product Sub Categories
            $router->post('/{categoryId}/subcategory', 'ProductCategoryController@storeSubCategory');
            $router->get('/{categoryId}/subcategory/{id}', 'ProductCategoryController@showSubCategory');

            //later
            //$router->put('/{categoryId}/subcategory/{id}', 'ProductCategoryController@updateSubCategory');
            //$router->delete('/{categoryId}/subcategory/{id}', 'ProductCategoryController@deleteSubCategory');
        });

        $router->group(['prefix'=>'user'], function () use ($router){
            $router->post('/refreshtoken','AuthController@refreshToken');

            $router->post('/product','ProductController@store');
            $router->get('/product/{id}','ProductController@getProductOfCurrentUser');
            $router->get('/products','ProductController@getProductsOfCurrentUser');
            $router->delete('/product/{id}','ProductController@destroy');
            $router->post('/updateProduct/{id}','ProductController@update');

            $router->get('/currentuser','UserController@getCurrentUser');
            $router->post('/upload/profilepicture','UserController@uploadProfilePicture');
            $router->put('/profile','UserController@updateProfile');

            $router->post('/address','UserController@addAddress');
            $router->delete('/address/{id}','UserController@destroyAddress');
            $router->get('/addresses','UserController@getAddresses');
            $router->get('/address/{id}','UserController@getAddress');
            $router->put('/address/{id}','UserController@updateAddress');

            $router->post('/addtocart/{productId}','CartController@addToCart');
            $router->get('/cart','CartController@showCart');
            $router->post('/removefromcart/{productId}','CartController@RemoveFromCart');
            $router->post('/cart/setquantity/{productId}','CartController@setQuantity');
        });

    });

});
