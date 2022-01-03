<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function addToCart(Request $request){

        $product = DB::table('products')
                    ->where('id',$request->id)
                    ->select(['product_name','selling_price'])->first();
        $check = DB::table('pos')->where('product_id',$request->id)->first();

        if ($check){
            DB::table('pos')->where('product_id',$request->id)
                ->increment('product_qty');
            $getProduct = DB::table('pos')->where('product_id',$request->id)->first();

            $sub_total = $getProduct->product_qty * $getProduct->product_price;
            $pos = DB::table('pos')->where('product_id',$request->id)
                ->update(["sub_total" =>$sub_total]);
        }else{
            $data = array();
            $data['product_id'] = $request->id;
            $data['product_name'] = $product->product_name;
            $data['product_qty'] = 1;
            $data['product_price'] = $product->selling_price;
            $data['sub_total'] = $product->selling_price;

            $pos = DB::table('pos')->insert($data);
        }



      return response()->json($pos);
    }

    public function incrementProduct(Request $request){
        DB::table('pos')->where('id',$request->id)
            ->increment('product_qty');
        $getProduct = DB::table('pos')->where('id',$request->id)->first();

        $sub_total = $getProduct->product_qty * $getProduct->product_price;
        $pos = DB::table('pos')->where('id',$request->id)
            ->update(["sub_total" =>$sub_total]);
        return response()->json($pos);
    }


    public function decrementProduct(Request $request){
        DB::table('pos')->where('id',$request->id)
            ->decrement('product_qty');
        $getProduct = DB::table('pos')->where('id',$request->id)->first();

        $sub_total = $getProduct->product_qty * $getProduct->product_price;
        $pos = DB::table('pos')->where('id',$request->id)
            ->update(["sub_total" =>$sub_total]);

        return response()->json($pos);
    }

    public function getAllCart(){

        $pos = DB::table('pos')->get();

        return response()->json($pos);

    }

    public function deleteFromCart($id){

        $pos = DB::table('pos')->where('id',$id)->delete();
        return response()->json($pos);
    }

    public function orderDone(Request $request){

        $validateData = $request->validate([
            'customer_id' => 'required',
            'payby' => 'required'
        ]);

        $data =array();
        $data['customer_id'] = $request->customer_id;
        $data['qty'] = $request->qty;
        $data['sub_total'] = $request->sub_total;
        $data['tva'] = $request->tva;
        $data['total'] = $request->total;
        $data['pay'] = $request->pay;
        $data['due'] = $request->due;
        $data['payby'] = $request->payby;
        $data['order_date'] = date('d/m/Y');
        $data['order_month'] = date('F');
        $data['order_year'] = date('Y');

        $order_id = DB::table('orders')->insertGetId($data);
        $contents = DB::table('pos')->get();
        $odata =array();
        if($contents){
            foreach ($contents as $content){
                $odata['order_id'] = $order_id;
                $odata['product_id'] = $content->product_id;
                $odata['product_qty'] = $content->product_qty;
                $odata['product_price'] = $content->product_price;
                $odata['sub_total'] = $content->sub_total;
                DB::table('order_details')->insert($odata);

                DB::table('products')->where('id',$content->product_id)
                    ->update(['product_quantity'=>DB::raw('product_quantity -'.$content->product_qty)]);

            }
        }


        $del = DB::table('pos')->delete();
        return response()->json($del);


    }
}
