<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            return Product::where('user_id', '=', JWTAuth::parseToken()->authenticate()->id)
                               ->get(['name', 'price', 'quantity'])
                               ->toArray();
        }
        catch(Exception $exception)
        {
            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // validate data
        try
        {
            $this->validate($request, [
                'name' => 'required',
                'price' => 'required|integer',
                'quantity' => 'required|integer'
            ]);
        }
        catch(ValidationException $exception)
        {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        // create new Product with new data
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->user_id = JWTAuth::parseToken()->authenticate()->id;

        // save new Product
        if( $product->save() )
        {
            return response()->json([
                'success' => true,
                'message' => 'New product added successfully',
                'data' => $product
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sorry, product can not be added'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // find product by id
        try
        {
            $product = Product::whereId($id)->first();

            // if product not found, send error message
            if( !$product )
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, product with id ' . $id . ' cannot be found.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // if product found, but current user is not valid to access, send error message
            if( $product->user_id != JWTAuth::parseToken()->authenticate()->id )
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, you have no permission to access this product.'
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
        catch(Exception $exception)
        {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // validate updated product data
        $data = $request->only(
            'name',
            'price',
            'quantity'
        );

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'price' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        // for invalid request, send failure message
        if( $validator->fails() )
        {
            return response()->json([
                'error' => $validator->messages()
            ], Response::HTTP_OK);
        }

        // request is valid
        // find product by id
        $product = $this->getProductByIdOrNull($id);
        if( !$product )
        {
            // product does not exist or current user do not have access
            return response()->json([
                'success' => false,
                'message' => 'Product does not exist or you do not have access.'
            ], Response::HTTP_FORBIDDEN);
        }

        // update product data
        $product->name = $request->name;
        $product->price = $request->price;
        $product->quantity = $request->quantity;

        if( $product->save() )
        {
            // product updated, return success response
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ], Response::HTTP_OK);
        }

        // product not updated, return failure message
        return response()->json([
            'success' => false,
            'message' => 'Sorry, product can not be updated'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // find product by id
        $product = $this->getProductByIdOrNull($id);
        if( !$product )
        {
            // product does not exist or current user do not have access to modify
            return response()->json([
                'success' => false,
                'message' => 'Product does not exist or you do not have access to modify.'
            ], Response::HTTP_FORBIDDEN);
        }

        if( $product->delete() )
        {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product could not be deleted'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * search for product by id, and return the product
     * if product does not exist, return null
     * if product exists, but current user does not have access to the product, return null
     */
    public function getProductByIdOrNull($id)
    {
        try
        {
            return Product::whereId($id)
                          ->where('user_id', '=', JWTAuth::parseToken()->authenticate()->id)
                          ->first();
        }
        catch(Exception $exception)
        {
            return null;
        }
    }
}
