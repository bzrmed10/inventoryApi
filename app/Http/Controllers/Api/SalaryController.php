<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function paySalary(Request $request)
    {

        $dataValidation = $request->validate([
                'salary_month' => 'required',
            ]
        );
        $month = $request->salary_month;
        $id = $request->employee_id;
        $check = DB::table('salaries')
                ->where('employee_id',$id)
                ->where('salary_month',$month)
                ->first();
        if($check){
            return response()->json('salary already paied');

        }else{
            $data = array();
            $data['employee_id'] = $id;
            $data['salary_month'] = $month;
            $data['salary'] = $request->salary;
            $data['salary_date'] = date('d/m/Y');
            $data['salary_year'] = date('Y');
            $insert = DB::table('salaries')->insert($data);

            return response()->json($insert);
        }


    }

    public function allSalary(){
        $months = DB::table('salaries')
            ->select('salary_month')
            ->groupBy('salary_month')
            ->get();

        return response()->json($months);
    }

    public function allSalaryByMonth(Request $request){
        $skip = $request->skip;
        $limit = $request->limit;
        $month = $request->month;

        $salaries = DB::table('salaries')
            ->join('employees','salaries.employee_id','employees.id')
            ->select('salaries.*','employees.fullname')
            ->where('salaries.salary_month',$month)
            ->skip($skip)->take($limit)
            ->get();


        $totalItem = DB::table('salaries')
            ->join('employees','salaries.employee_id','employees.id')
            ->select('salaries.*','employees.fullname')
            ->where('salaries.salary_month',$month)->count();
        $response['data'] = $salaries;
        $response['total'] = $totalItem;

        return response()->json($response);
    }

    public function deletePay($id)
    {
        $pay =  DB::table('salaries')->where('id',$id)->delete();;

        return  response()->json($pay);
    }
}
