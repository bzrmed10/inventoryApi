<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
        $suppliers = Supplier::all()->skip($skip)->take($limit);

        $totalItem = Supplier::all()->count();
        $response['data'] = $suppliers;
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
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'adress' => 'required',
                'shopname' => 'required|unique:suppliers'
            ]
        );

            $supplier = new Supplier();
            $supplier->name = $request->name;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->adress = $request->adress;
            $supplier->shopname = $request->shopname;
            $supplier->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier);
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
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'adress' => 'required',
                'shopname' => 'required'
            ]
        );
        $data = array();
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['adress'] = $request->adress;
        $data['shopname'] = $request->shopname;
        $supplier = Supplier::findOrFail($id)->update($data);

        return response()->json($supplier);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id)->delete();;

        return  response()->json($supplier);
    }

    public function getAllSuppliers(){

        $suppliers = Supplier::all();

        return response()->json($suppliers);

    }
}
