<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Customer;
use Illuminate\Http\Request;
use Image;

class CustomerController extends Controller
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
        $customers = Customer::all()->skip($skip)->take($limit);
        foreach ($customers as $customer){
            $customer->photo =  $customer->photo!= null ? 'http://127.0.0.1/inventory/public/'.$customer->photo : null;
        }
        $totalItem = Customer::all()->count();
        $response['data'] = $customers;
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
                'fullname' => 'required|unique:customers|max:255',
                'email' => 'required|email',
                'phone' => 'required',
                'adress' => 'required',

            ]
        );

        if($request->fileSource){

            $position = strpos($request->fileSource , ';');
            $sub = substr($request->fileSource,0,$position);
            $ext = explode('/',$sub)[1];
            $name = time().".".$ext;
            $img = Image::make($request->fileSource)->resize(240,240);
            $upload_path = 'backend/employee/';
            $image_url = $upload_path.$name;
            $img->save($image_url);
            $customer = new Customer();
            $customer->fullname = $request->fullname;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->adress = $request->adress;
            $customer->photo = $image_url;
            $customer->save();
        }else{
            $customer = new Employee;
            $customer->fullname = $request->fullname;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->adress = $request->adress;
            $customer->save();
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
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
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
                'fullname' => 'required|max:255',
                'email' => 'required|email',
                'phone' => 'required',
                'adress' => 'required',
            ]
        );
        $data = array();
        $data['fullname'] = $request->fullname;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['adress'] = $request->adress;
        $image = $request->fileSource;
        $img = Customer::findOrFail($id);
        $img_path = $img->photo;
        if($image && $image != ""){
            $position = strpos($image , ';');
            $sub = substr($image,0,$position);
            $ext = explode('/',$sub)[1];
            $name = time().".".$ext;
            $img = Image::make($image)->resize(240,240);
            $upload_path = 'backend/customers/';
            $image_url = $upload_path.$name;
            $succes = $img->save($image_url);

            if($succes){
                $data['photo'] = $image_url;
                if($img_path){
                    unlink($img_path);
                }
                $user = Customer::findOrFail($id)->update($data);
            }
        }else{

            $oldPhoto = $img_path;
            $data['photo'] = $oldPhoto;
            $user = Customer::findOrFail($id)->update($data);
        }

        return response()->json($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $photo = $customer->photo;
        if($photo){
            unlink($photo);
        }
        $customer->delete();
        return  response()->json($customer);
    }
}
