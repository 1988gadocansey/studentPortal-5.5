<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use App\User;
use PhpParser\Node\Expr\AssignOp\Mod;


class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
         @$user=\Auth::user()->id;
        $date=new \Datetime();
        @User::where("id", $user)->update(array("last_sign_in"=>$date));
        $agent = new Agent();
        /*$agent->is('Windows');
        $agent->is('Firefox');
        $agent->is('iPhone');
        $agent->is('OS X');
        $agent->isAndroidOS();
        $agent->isNexus();
        $agent->isSafari();
        $agent->isMobile();
        $agent->isTablet();
        $device = $agent->device();
        $platform = $agent->platform();
        $browser = $agent->browser();
        $agent->isDesktop();
        $agent->isPhone();
        $browser = $agent->browser();
        $version = $agent->version($browser);*/

        //$platform = $agent->platform();
        //$version = $agent->version($platform);
        //dd($agent->device());
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(SystemController $sys, Request $request)
    {
        
        dd($request);
    //$newIndex=$sys->assignIndex(@\Auth::user()->programme);
//dd($newIndex);
        $lastVisit=\Carbon\Carbon::createFromTimeStamp(strtotime(@\Auth::user()->last_sign_in))->diffForHumans();
        $student=@\Auth::user()->username;
        //dd( $student);
        $userModel=User::query()->where('username',$student)->where('active','1')->first();
        $studentUpdate=$userModel->biodata_update;
        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;

        $feeTotal = $sys->getTotalPayment($student,$year);

        $newCgpa=@$sys->getCGPA($student);
                                    $class=@$sys->getClass($newCgpa);
                                    Models\StudentModel::where("INDEXNO",$student)->update(array("CGPA"=>$newCgpa,"CLASS"=>$class,"PAID"=>$feeTotal));
                                    \DB::commit();
       
        $studentDetail=Models\StudentModel::query()->where('INDEXNO',$student)->first();
        //where('STATUS','In school')->orwhere('STATUS','Alumni')->orwhere('STATUS','Admitted')->first();
        //dd($studentDetail);
        $currentLevel=$studentDetail->LEVEL;
        $program=$studentDetail->PROGRAMMECODE;
        $totalCredit=$studentDetail->TOTAL_CREDIT_DONE;
        $leftComplete=$studentDetail->CREDIT_LEFT_COMPLETE;
        $asses=$studentDetail->QUALITY_ASSURANCE;
        $level=$studentDetail->LEVEL;
        $currentStatus=$studentDetail->STATUS;
        $currentFees=0;
        if ($currentStatus == 'In school' || $currentStatus == 'In School') {
           $currentFees=@$sys->getStudentFee($student,$currentLevel);
           if (is_null($currentFees)){
             $currentFees=0;
         }

        }
        
        $balance=(@$studentDetail->BALANCE);
        //dd($balance, $currentFees);
        $totalBill = @$currentFees + @$balance;
        
        
        
        
                                    Models\StudentModel::where("INDEXNO",$student)->update(array("BILLS"=>$totalBill,"PAID"=>$feeTotal));
                                    \DB::commit();

         $totalOwing=@($studentDetail->BILLS - $studentDetail->PAID);
        $SemesterBill=@$sys->formatMoney($studentDetail->BILLS); 

        Models\StudentModel::where("INDEXNO",$student)->update(array("BILLS"=>$totalBill,"BILL_OWING"=>$totalOwing));
                                    \DB::commit();                          

        $cgpa=$studentDetail->CGPA;
        $class=$studentDetail->CLASS;
        $status=$studentDetail->STATUS;
       // $totalOwing=@$sys->formatMoney($studentDetail->BILL_OWING+$studentDetail->BILLS);
        //Payment details
        $paymentDetail=  Models\FeePaymentModel::query()->where('SEMESTER',$sem)->where('YEAR',$year)->where('INDEXNO',$student)->get();
        $totalPaid= @$sys->formatMoney($studentDetail->PAID);
        // $totalPaid= \DB::table('tpoly_feedetails')->where('YEAR',$year)->where('INDEXNO',$student)->SUM("AMOUNT");
        //$_SESSION['paidd']=$totalPaid;
         $resitQuery=  Models\AcademicRecordsModel::where("indexno",$student)->where("grade","F")->where("resit","no")->orderby("code")->orderby("resit")->get()->toArray();
        // mounted courses
        $courseDetail= Models\MountedCourseModel::query()->where('COURSE_SEMESTER',$sem)->where('COURSE_LEVEL',$studentDetail->LEVEL)->where('PROGRAMME',\Auth::user()->programme)->where('COURSE_YEAR',$year)->get();
         //dd( $courseDetail);
        $studentNew=Models\StudentModel::where("indexno",  $student)->select("REGISTERED")->first();
        if($studentUpdate==1){
         if($studentNew->REGISTERED==1){
              $query = @Models\AcademicRecordsModel::where("indexno",  $student)->where("year", $year
                    )
                    ->where('sem',$sem)
                     ->get();

         }

            if($studentUpdate==1){
                $studentUpdate1 = 'Yes';
                $studentUpdateS = 'uk-text-success';
            }
            else{
             $studentUpdate1 = 'No';  
             $studentUpdateS = 'uk-text-danger'; 
            }

            if($asses==1){
                $asses1 = 'Yes';
                $assesS = 'uk-text-success';
            }
            else{
             $asses1 = 'No'; 
             $assesS = 'uk-text-danger';  
            }

            if($studentNew->REGISTERED==1){
                $register1 = 'Yes';
                $registerS = 'uk-text-success';
            }
            else{
             $register1 = 'No';  
             $registerS = 'uk-text-danger'; 
            }

            return view('dashboard')->with('data', $paymentDetail)
                                ->with('bill', $SemesterBill)
                                ->with('currentFees', $currentFees)
                                ->with('cgpa', $cgpa)
                                ->with('class', $class)
                                ->with('lastVisit', $lastVisit)
                                ->with('credit', $totalCredit)
                                ->with('status', $status)
                                ->with('left', $leftComplete)
                                ->with('course', $courseDetail)
                                ->with('year', $year)
                                ->with('level', $level)
                                ->with('sem', $sem)
                                 ->with('paid', $totalPaid)
                                ->with('register1', $register1)
                                ->with('resitQuery', $resitQuery)
                                ->with('registerQuery', @$query)
                                ->with('balance', $balance)
                                ->with('asses1', $asses1)
                                ->with('studentUpdate1', $studentUpdate1)
                                ->with('registerS', $registerS)
                                ->with('assesS', $assesS)
                                ->with('studentUpdateS', $studentUpdateS)
                                ->with('program', $program)
                                ->with('totalowe', $totalOwing);
        
        
        }else{
            return redirect('/biodataUpdate');
        }
        
    }
    public function accountStatement(Request $request, SystemController $sys) {
        $student=@\Auth::user()->username;
        
         
        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;
       
        $studentDetail=Models\StudentModel::where('INDEXNO',$student)->orWhere("STNO",$student)->first();
        
         $id=$studentDetail->ID;
        $outstandingBill=@$sys->formatMoney($studentDetail->BILL_OWING);
        $SemesterBill=@$sys->formatMoney($studentDetail->BILLS);
          //Payment details
        $paymentDetail=  Models\FeePaymentModel::where('student',$id)->orWhere("INDEXNO",$studentDetail->INDEXNO)->orderBy('LEVEL','DESC')->orderBy('YEAR','DESC')->orderBy('SEMESTER','DESC')->paginate();

        //dd($paymentDetail);

        return view("students.account_statement")->with("transaction", $paymentDetail)
                ->with('balance', $outstandingBill)
                ->with('semesterBill', $paymentDetail)->with("year",$year);
    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $request->user()->tasks()->create([
            'name' => $request->name,
        ]);

        return redirect('/tasks');
    }

    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();

        return redirect('/tasks');
    }
}
