<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Models;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class APIController extends Controller
{


    public function __construct()

    {

        // $this->middleware('auth');





    }

    public function convert_number($number)
    {

        if (($number < 0) || ($number > 999999999)) {
            return "$number";
        }

        $Gn = floor($number / 1000000); /* Millions (giga) */
        $number -= $Gn * 1000000;
        $kn = floor($number / 1000); /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100); /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10); /* Tens (deca) */
        $n = $number % 10; /* Ones */

        $res = "";

        if ($Gn) {
            $res .= $this->convert_number($Gn) . " Million";
        }

        if ($kn) {
            $res .= (empty($res) ? "" : " ") .
                $this->convert_number($kn) . " Thousand";
        }

        if ($Hn) {
            $res .= (empty($res) ? "" : " ") .
                $this->convert_number($Hn) . " Hundred";
        }

        $ones = array(
            "",
            "One",
            "Two",
            "Three",
            "Four",
            "Five",
            "Six",
            "Seven",
            "Eight",
            "Nine",
            "Ten",
            "Eleven",
            "Twelve",
            "Thirteen",
            "Fourteen",
            "Fifteen",
            "Sixteen",
            "Seventeen",
            "Eighteen",
            "Nineteen");
        $tens = array(
            "",
            "",
            "Twenty",
            "Thirty",
            "Fourty",
            "Fifty",
            "Sixty",
            "Seventy",
            "Eighty",
            "Ninety");

        if ($Dn ||
            $n) {
            if (!empty($res)) {
                $res .= " and ";
            }

            if ($Dn <
                2) {
                $res .= $ones[$Dn *
                10 +
                $n];
            } else {
                $res .= $tens[$Dn];

                if ($n) {
                    $res .= "-" . $ones[$n];
                }
            }
        }

        if (empty($res)) {
            $res = "zero";
        }

        return $res;

//$thea=explode(".",$res);
    }

    public function convert($amt)
    {
//$amt = "190120.09" ;

        $amt = number_format($amt, 2, '.', '');
        $thea = explode(".", $amt);

//echo $thea[0];

        $words = $this->convert_number($thea[0]) . " Ghana Cedis ";
        if ($thea[1] >
            0) {
            $words .= $this->convert_number($thea[1]) . " Pesewas";
        }

        return $words;
    }
    public function printreceipt(Request $request, $receiptno,SystemController $sys)
    {

        // $this->show_query();
        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;


        $transaction = Models\FeePaymentModel::where("RECEIPTNO", $receiptno)->with("students", "bank"
        )->first();


        if (empty($transaction)) {
            abort(434, "No Fee payment   with this receipt <span class='uk-text-bold uk-text-large'>{{$receiptno}}</span>");
        } else {


            $data = Models\StudentModel::where("ID", $transaction->STUDENT)->first();
            //dd($data);
            $words = $this->convert($transaction->AMOUNT);


            return view("students.receipt")->with("student", $data)
                ->with("transaction", $transaction)->with('words', $words)->with("year",$year);
        }

    }

    public function printAttachmentForm(Request $request, SystemController $sys,$indexno)
    {
        $studentSessionId =$indexno;
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        $status = $array[0]->LIAISON;
        if ($status == 1) {

            // make sure only students who are currently in school can update their data
            $query = Models\LiaisonModel::where('indexno', $studentSessionId)->where('year', $year)->first();

            $programme = $sys->getProgramList();
            $zone = $sys->getZones();
            $address = $sys->getAddress();
            return view('liaison.attachmentPrint')->with('data', $query)
                ->with('programme', $programme)
                ->with('zone', $zone)
                ->with('address', $address);

        } else {
            die ( "Attachment form has been closed.Please contact Industrial Liaison Office");

        }
    }

    public function printAssumptionForm(Request $request, SystemController $sys,$indexno)
    {
        $studentSessionId = $indexno;
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        $status = $array[0]->LIAISON;
        if ($status == 1) {

            // make sure only students who are currently in school can update their data
            $query = Models\AssumptionDutyModel::where('indexno', $studentSessionId)->where('year', $year)->first();

            $programme = $sys->getProgramList();
            $zone = $sys->getZones();
            $address = $sys->getAddress();
            return view('liaison.assumptionLetter')->with('data', $query)
                ->with('programme', $programme)
                ->with('zone', $zone)
                ->with('address', $address);

        } else {
            return redirect("/dashboard")->with("error", "Attachment form has been closed.Please contact Industrial Liaison Office");

        }
    }


}