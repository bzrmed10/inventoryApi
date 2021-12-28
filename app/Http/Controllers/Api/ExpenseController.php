<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
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
        $expenses = Expense::all()->skip($skip)->take($limit);

        $totalItem = Expense::all()->count();
        $response['data'] = $expenses;
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
                'details' => 'required',
                'amount' => 'required',

            ]
        );

        $expense = new Expense();
        $expense->details = $request->details;
        $expense->amount = $request->amount;
        $expense->expense_date = date('Y-m-d h:i:s');

        $expense->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
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
                'details' => 'required',
                'amount' => 'required',

            ]
        );

        $data = array();
        $data['details'] = $request->details;
        $data['amount'] = $request->amount;

        $expense = Expense::findOrFail($id)->update($data);
        return response()->json($expense);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id)->delete();
        return response()->json($expense);
    }
}
