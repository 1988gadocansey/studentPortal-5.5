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


}