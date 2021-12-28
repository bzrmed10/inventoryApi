<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $skip = $request->skip;
        $limit = $request->limit;
//        $products = DB::table('products')
//            ->join('categories', 'categories.id', '=', 'products.category_id')
//            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
//            ->select('products.*', 'suppliers.shopname', 'categories.category_name')
//            ->skip($skip)->take($limit)
//            ->get();

        $products = Product::with('category:id,category_name')
            ->with('supplier:id,shopname')
            ->skip($skip)
            ->take($limit)->get();

        foreach ($products as $product){
            $product->product_image =  $product->product_image!= null ? 'http://127.0.0.1/inventory/public/'.$product->product_image : null;
        }
        $totalItem = Product::all()->count();
        $response['data'] = $products;
        $response['total'] = $totalItem;
        return response()->json($response);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dataValidation = $request->validate([
                'product_name' => 'required',
                'product_code' => 'required',
                'category_id' => 'required',
                'supplier_id' => 'required',
                'buying_price' => 'required',
                'selling_price' => 'required',
                'buying_date' => 'required',
                'product_quantity' => 'required'

            ]
        );

        if($request->fileSource){

            $position = strpos($request->fileSource , ';');
            $sub = substr($request->fileSource,0,$position);
            $ext = explode('/',$sub)[1];
            $name = time().".".$ext;
            $img = Image::make($request->fileSource)->resize(240,240);
            $upload_path = 'backend/products/';
            $image_url = $upload_path.$name;
            $img->save($image_url);
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->product_code = $request->product_code;
            $product->category_id = $request->category_id;
            $product->supplier_id = $request->supplier_id;
            $product->buying_price = $request->buying_price;
            $product->root = $request->root;
            $product->selling_price = $request->selling_price;
            $product->product_quantity = $request->product_quantity;
            $product->buying_date = $request->buying_date;
            $product->product_image = $image_url;
            $product->save();
        }else{
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->product_code = $request->product_code;
            $product->category_id = $request->category_id;
            $product->supplier_id = $request->supplier_id;
            $product->buying_price = $request->buying_price;
            $product->root = $request->root;
            $product->selling_price = $request->selling_price;
            $product->product_quantity = $request->product_quantity;
            $product->buying_date = $request->buying_date;
            $product->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dataValidation = $request->validate([
                'product_name' => 'required',
                'product_code' => 'required',
                'category_id' => 'required',
                'supplier_id' => 'required',
                'buying_price' => 'required',
                'selling_price' => 'required',
                'buying_date' => 'required',
                'product_quantity' => 'required'

            ]
        );

        $data = array();
        $data['product_name'] = $request->product_name;
        $data['product_code'] = $request->product_code;
        $data['category_id'] = $request->category_id;
        $data['supplier_id'] = $request->supplier_id;
        $data['buying_price'] = $request->buying_price;
        $data['selling_price'] = $request->selling_price;
        $data['buying_date'] = $request->buying_date;
        $data['product_quantity'] = $request->product_quantity;
        $data['root'] = $request->root;

        $image = $request->fileSource;
        $img = Product::findOrFail($id);
        $img_path = $img->product_image;
        if($image && $image != ""){
            $position = strpos($image , ';');
            $sub = substr($image,0,$position);
            $ext = explode('/',$sub)[1];
            $name = time().".".$ext;
            $img = Image::make($image)->resize(240,240);
            $upload_path = 'backend/products/';
            $image_url = $upload_path.$name;
            $succes = $img->save($image_url);

            if($succes){
                $data['product_image'] = $image_url;
                if($img_path){
                    unlink($img_path);
                }
                $product = Product::findOrFail($id)->update($data);
            }
        }else{

            $oldPhoto = $img_path;
            $data['product_image'] = $oldPhoto;
            $product = Product::findOrFail($id)->update($data);
        }

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id)->delete();
        return response()->json($product);
    }
}
