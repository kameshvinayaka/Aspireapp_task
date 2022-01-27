<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Loan extends Model {
    public $timestamps = true;
    protected $fillable = ['applicant_name','loan_amount','loan_term'];
}
