<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Employee;
use Illuminate\Http\Request;
use Image;


class EmployeeController extends Controller
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
       $employees = Employee::all()->skip($skip)->take($limit);
       foreach ($employees as $employe){
           $employe->photo =  $employe->photo!= null ? 'http://127.0.0.1/inventory/public/'.$employe->photo : null;
       }
       $totalItem = Employee::all()->count();
       $response['data'] = $employees;
       $response['total'] = $totalItem;
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'fullname' => 'required|unique:employees|max:255',
                'email' => 'required|email',
                'phone' => 'required',
                'adress' => 'required',
                'salary' => 'required'
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
            $employee = new Employee;
            $employee->fullname = $request->fullname;
            $employee->email = $request->email;
            $employee->phone = $request->phone;
            $employee->adress = $request->adress;
            $employee->nid = $request->nid;
            $employee->salary = $request->salary;
            $employee->joining_date = $request->joining_date;
            $employee->photo = $image_url;
            $employee->save();
        }else{
            $employee = new Employee;
            $employee->fullname = $request->fullname;
            $employee->email = $request->email;
            $employee->phone = $request->phone;
            $employee->adress = $request->adress;
            $employee->nid = $request->nid;
            $employee->salary = $request->salary;
            $employee->joining_date = $request->joining_date;
            $employee->save();
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
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
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
                'salary' => 'required'
            ]
        );
        $data = array();
        $data['fullname'] = $request->fullname;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $data['adress'] = $request->adress;
        $data['joining_date'] = $request->joining_date;
        $data['nid'] = $request->nid;
        $data['salary'] = $request->salary;
        $image = $request->fileSource;
        $img = Employee::findOrFail($id);
        $img_path = $img->photo;
        if($image && $image != ""){
            $position = strpos($image , ';');
            $sub = substr($image,0,$position);
            $ext = explode('/',$sub)[1];
            $name = time().".".$ext;
            $img = Image::make($image)->resize(240,240);
            $upload_path = 'backend/employee/';
            $image_url = $upload_path.$name;
            $succes = $img->save($image_url);

            if($succes){
                $data['photo'] = $image_url;
                if($img_path){
                    unlink($img_path);
                }
                $user = Employee::findOrFail($id)->update($data);
            }
        }else{

            $oldPhoto = $img_path;
            $data['photo'] = $oldPhoto;
            $user = Employee::findOrFail($id)->update($data);
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
        $employee = Employee::findOrFail($id);
        $photo = $employee->photo;
        if($photo){
            unlink($photo);
        }
        $employee->delete();
        return  response()->json($employee);
    }
}
