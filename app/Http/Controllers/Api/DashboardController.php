<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Customer;
use App\Models\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStatistics(){
        $date = date('d/m/Y');

        $response['products'] = Product::all()->count();
        $response['customers'] = Customer::all()->count();
        $response['todayOrders'] = DB::table('orders')
            ->where('order_date',$date)->count();
        $response['todaySales'] = DB::table('orders')
            ->where('order_date',$date)
            ->sum('total');

        return response()->json($response);

    }

    public function getSalesByCategory(){
        $data = DB::table('order_details')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select( 'categories.category_name as category',DB::raw('count(order_details.id) as total'))
            ->groupBy('categories.category_name')
            ->get();

            return response()->json($data);
    }

    public function getTotalOrdersProduct(){
        $orders = DB::table('orders')
            ->select( 'orders.order_date',DB::raw('count(orders.id) as total'))
            ->groupBy('orders.order_date')
            ->orderBy('orders.id')
            ->take(7)
            ->get();
        $qtyproducts = DB::table('orders')
            ->select( 'orders.order_date',DB::raw('sum(orders.qty) as qty'))
            ->groupBy('orders.order_date')
            ->orderBy('orders.id')
            ->take(7)
            ->take(7)
            ->get();
        $response['days'] = [];
        $response['qtyProductSold'] = [];
        $response['orders'] = [];
        foreach ($orders as $order){
            array_push($response['days'],$order->order_date);
            array_push($response['orders'],$order->total);
        }

        foreach ($qtyproducts as $qtyproduct){
            array_push($response['qtyProductSold'],$qtyproduct->qty);

        }



        return response()->json($response);

    }


    public function getSalesBenefits(){
        $sales = DB::table('orders')
            ->select( 'orders.order_date',DB::raw('sum(orders.total) as totalTva'),DB::raw('sum(orders.sub_total) as total'))
            ->groupBy('orders.order_date')
            ->orderBy('orders.id')
            ->take(7)
            ->get();
        $netsales = DB::table('orders')
        ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->select( 'orders.order_date',DB::raw('sum(products.buying_price * order_details.product_qty) as benefits'))
            ->groupBy('orders.order_date')
            ->orderBy('orders.id')->get();


        $response['days'] = [];
        $response['sales'] = [];
        $response['salesTva'] = [];
        $response['benefits'] = [];
        foreach ($sales as $sale){
            array_push($response['days'],$sale->order_date);
            array_push($response['sales'],$sale->total);
            array_push($response['salesTva'],$sale->totalTva);
        }

        for ($i=0 ; $i< count($netsales) ; $i++){
            $benefits = $response['sales'][$i] - $netsales[$i]->benefits;
            array_push($response['benefits'],$benefits);

        }

        return response()->json($response);
    }
}
