<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    // Create loan api.

    public function create(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'loan_amount' => 'required|numeric',
            'loan_term' => 'required|numeric',
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 0,'message' => $validation->errors()], 403);
        }
        // create a loan in database
        $loan_id = Loan::insertGetId([
            'applicant_name' => $request->name,
            'loan_amount' => $request->loan_amount,
            'loan_term' => $request->loan_term,
            'uncleared_amount' => $request->loan_amount,
            'cleared_amount' => 0,
            'approved_by' => 0,
            'created_at' => Carbon::now(),
            'status' => 'PENDING'
        ]);
        return response()->json(['status' => 1,'message' => 'loan created successfuly', 'loan_id' => $loan_id], 200);
    }

    // Fetch loans 
    public function list(Request $request){
        $loans = Loan::orderBy('id','DESC');
        if(!empty($request->status)){
            $loans->where('status',$request->status);
        }
        $loans = $loans->get();
        return response()->json(['status' => 1,'message' => 'success', 'loans' => $loans], 200);
    }
    // Fetch loan details
    public function details(Request $request){
        $validation = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loans,id'
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 0,'message' => $validation->errors()], 403);
        }
        $loan = Loan::where('id',$request->loan_id)->first();
        $loan->repayments = LoanRepayment::where('loan_id',$loan->id)->get();
        
        return response()->json(['status' => 1,'message' => 'success', 'loan' => $loan], 200);
    }

    // loan status update.

    public function proccess(Request $request){
        $validation = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loans,id',
            'status' => 'required|in:APPROVED,REJECTED'
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 0,'message' => $validation->errors()], 403);
        }
        $loan = Loan::where('id',$request->loan_id)->first();
        if($loan->status!='PENDING'){
            return response()->json(['status' => 0,'message' => 'Invalid loan to approve'], 403);
        }
        // update loan status
        Loan::Where('id',$request->loan_id)->update([
            'approved_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
            'status' => $request->status
        ]);
        return response()->json(['status' => 1,'message' => 'loan Approved successfuly'], 200);
    }

    // loan Repayment.

    public function repayment(Request $request){
        $validation = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loans,id',
            'repayment_amount' => 'required|numeric'
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 0,'message' => $validation->errors()], 403);
        }
        $loan = Loan::where('id',$request->loan_id)->first();
        $term_amount = $loan->loan_amount/$loan->loan_term;
        if($request->repayment_amount < $term_amount){
            return response()->json(['status' => 0,'message' => 'repayment amount must be '.$term_amount], 403);
        }
        if($loan->uncleared_amount < $request->repayment_amount){
            return response()->json(['status' => 0,'message' => 'repayment amount not to be more than uncleared amount'], 403);
        }
        // create loan repayment
       $payment_id = LoanRepayment::insertGetId([
         'loan_id' => $request->loan_id,
         'paid_amount' => $request->repayment_amount,
         'balance' => ($loan->uncleared_amount-$request->repayment_amount),
         'created_at' => Carbon::now(),
         'updated_at' => Carbon::now()
        ]);
        
        Loan::Where('id',$request->loan_id)->update([
            'uncleared_amount' => ($loan->uncleared_amount-$request->repayment_amount),
            'cleared_amount' => ($loan->cleared_amount+$request->repayment_amount),
        ]);
        return response()->json(['status' => 1,'message' => 'loan repayment successfuly', 'payment_id' => $payment_id], 200);
    }

}
