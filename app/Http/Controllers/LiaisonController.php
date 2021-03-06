<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Models;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LiaisonController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');


    }

    public function showForm(Request $request, SystemController $sys)
    {

        $studentSessionId = @\Auth::user()->username;
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
            return view('liaison.form')->with('data', $query)
                ->with('programme', $programme)
                ->with('zone', $zone)
                ->with('address', $address);

        } else {
            return redirect("/dashboard")->with("error", "Attachment form has been closed.Please contact Industrial Liaison Office");

        }
    }

    public function processForm(Request $request, SystemController $sys)
    {
        $this->validate($request, [
            'cname' => 'required',
            'clocation' => 'required',


            'term' => 'required'


        ]);
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $level = @\Auth::user()->level;
        $indexno = @\Auth::user()->username;
        $cname = $request->input('cname');
        $cphone = $request->input('cphone');
        $cto = $request->input('cto');

        $clocation = $request->input('clocation');

        $term = $request->input('term');
        $caddress = $request->input('caddress');
        $csuper = $request->input('csuper');
        $cdate = $request->input('cdate');
        $czone = $request->input('czone');
        $cemail = $request->input('cemail');

        $checkQuery = Models\LiaisonModel::where("indexno", $indexno)
            ->where("year", $year)->first();
        if (empty($checkQuery)) {
            $data = new Models\LiaisonModel();
            $data->company_name = ucwords($cname);

            $data->company_address = "none";
            $data->company_location = ucwords($clocation);

            $data->company_email = ucwords($cemail);
            $data->company_address_to = ucwords($cto);
            $data->terms = $term;
            $data->status = 0;

            $data->indexno = $indexno;
            $data->year = $year;
            $data->level = $level;
            $sql = $data->save();



        } else {
            $sql = Models\LiaisonModel::where("indexno", $indexno)
                ->update(array(
                    "company_name" => ucwords($cname),

                    "company_address_to" => ucwords($cto),
                    "company_email" => ucwords($cemail),
                    "company_address" => ucwords($caddress),
                    "company_location" => ucwords($clocation),


                ));

        }
        if ($sql) {

            Models\StudentModel::where("INDEXNO",$indexno)->orWhere("STNO",$indexno)
                ->update(array("LIAISON"=>1));

            return response()->json(['status' => 'success', 'message' => ' Data sent to Industrial Liaison Office.. Going to print page... ']);

        } else {
            return response()->json(['status' => 'error', 'message' => ' Error sending data. try again ']);

        }
    }



    public function printAttachmentForm(Request $request, SystemController $sys)
    {
        $studentSessionId = @\Auth::user()->username;
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
            return redirect("/dashboard")->with("error", "Attachment form has been closed.Please contact Industrial Liaison Office");

        }
    }

    public function showSemesterOutForm(Request $request, SystemController $sys)
    {

        $studentSessionId = @\Auth::user()->username;
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        $status = $array[0]->LIAISON;
        if ($status == 1) {

            // make sure only students who are currently in school can update their data
            $query = Models\SemesterOutModel::where('indexno', $studentSessionId)->where('year', $year)->first();

            $programme = $sys->getProgramList();
            $zone = $sys->getZones();
            $address = $sys->getAddress();
            return view('liaison.semesterOutForm')->with('data', $query)
                ->with('programme', $programme)
                ->with('zone', $zone)
                ->with('address', $address);

        } else {
            return redirect("/dashboard")->with("error", "Semester out form has been closed.Please contact Industrial Liaison Office");

        }
    }

    public function processSemesterOutForm(Request $request, SystemController $sys)
    {
        $this->validate($request, [
            'cname' => 'required',
            'clocation' => 'required',

            
            'term' => 'required'


        ]);
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $level = @\Auth::user()->level;
        $indexno = @\Auth::user()->username;
        $cname = $request->input('cname');
        $cphone = $request->input('cphone');
        $cto = $request->input('cto');

        $clocation = $request->input('clocation');

        $term = $request->input('term');
        $caddress = $request->input('caddress');
        $csuper = $request->input('csuper');
        $cdate = $request->input('cdate');
        $czone = $request->input('czone');
        $cemail = $request->input('cemail');

        $checkQuery = Models\SemesterOutModel::where("indexno", $indexno)
            ->where("year", $year)->first();
        if (empty($checkQuery)) {
            $data = new Models\SemesterOutModel();
            $data->company_name = ucwords($cname);

            $data->company_address = ucwords($caddress);
            $data->company_location = ucwords($clocation);

            $data->company_email = ucwords($cemail);
            $data->company_address_to = ucwords($cto);
            $data->terms = $term;
            $data->status = 0;

            $data->indexno = $indexno;
            $data->year = $year;
            $data->level = $level;
            $sql = $data->save();



        } else {
            $sql = Models\SemesterOutModel::where("indexno", $indexno)
                ->update(array(
                    "company_name" => ucwords($cname),

                    "company_address_to" => ucwords($cto),
                    "company_email" => ucwords($cemail),
                    "company_address" => ucwords($caddress),
                    "company_location" => ucwords($clocation),


                ));

        }
        if ($sql) {



            return response()->json(['status' => 'success', 'message' => ' Data sent to Industrial Liaison Office.. Going to print page... ']);

        } else {
            return response()->json(['status' => 'error', 'message' => ' Error sending data. try again ']);

        }
    }
    public function printSemesterOut(Request $request, SystemController $sys)
    {
        $studentSessionId = @\Auth::user()->username;
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;

        $status = $array[0]->LIAISON;
        if ($status == 1) {

            // make sure only students who are currently in school can update their data
            $query = Models\SemesterOutModel::where('indexno', $studentSessionId)->where('year', $year)->first();

            $programme = $sys->getProgramList();
            $zone = $sys->getZones();
            $address = $sys->getAddress();
            return view('liaison.semesterOut')->with('data', $query);


        } else {
            return redirect("/dashboard")->with("error", "Semester out letter printing has been closed.Please contact Industrial Liaison Office");

        }
    }

    


}