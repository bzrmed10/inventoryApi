<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getAllOrders(Request $request){
        $skip = $request->skip;
        $limit = $request->limit;

        $orders = DB::table('orders')
            ->join('customers','orders.customer_id','=','customers.id')
            ->select(['customers.fullname','orders.*'])
            ->orderBy('orders.id','DESC')
            ->skip($skip)
            ->take($limit)->get();

        $totalItem = DB::table('orders')
            ->join('customers','orders.customer_id','=', 'customers.id')
            ->select(['customers.fullname','orders.*'])
            ->orderBy('orders.id','DESC')
            ->count();

        $response['data'] = $orders;
        $response['total'] = $totalItem;
        return response()->json($response);

    }

    public function getOrderDetail($id){
        $productsDetail = DB::table('order_details')
            ->join('products','order_details.product_id','=','products.id')
            ->where('order_id',$id)
            ->select(['products.*','order_details.*'])
            ->get();

        $order =  DB::table('orders')
            ->join('customers','orders.customer_id','=','customers.id')
            ->where('orders.id',$id)
            ->select(['customers.*','orders.*'])->first();

        foreach ($productsDetail as $product){
            $product->product_image =  $product->product_image!= null ? 'http://127.0.0.1/inventory/public/'.$product->product_image : null;
        }
        $response['products'] = $productsDetail;
        $response['order'] = $order;
        return response()->json($response);
    }
}
