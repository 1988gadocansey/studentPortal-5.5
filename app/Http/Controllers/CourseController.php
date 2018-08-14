<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;

use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\User;
use Jenssegers\Agent\Agent;

class CourseController extends Controller
{

    /*
     * Create a new controller instance.
     *

     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');


    }
    public function log_query() {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }
    public function transcript(SystemController $sys,Request $request){
        $student=  \Auth::user()->username;

        $userModel=User::where('username',$student)->where('active','1')->first();
        $studentUpdate=$userModel->biodata_update;

        $sql=Models\StudentModel::where("INDEXNO",$student)->where("ALLOW_RESULT",1)->first();
        if(!empty( $sql)) {

            //$studentUpdate=@$sql->biodata_update;
            if ($studentUpdate == 0) {
                return redirect('/biodataUpdate');
            }

            $qa = @$sql->QUALITY_ASSURANCE;

            if ($qa == 0) {
                return redirect('/lecturer/assessment');
            }

            $regis = @$sql->REGISTERED;

           

            $owing = @$sql->BILL_OWING;

            $array=$sys->getSemYear();
            $semT=$array[0]->SEMESTER;
            $yearT=$array[0]->YEAR;
            if ($semT == 1) {
                if($sql->BALANCE>0){
                return redirect('/dashboard')->with("error","check fee status");
            } 
            }
            elseif($semT == 2) {
                if($sql->PAID<=0.99*$sql->BILLS){
                return redirect('/dashboard')->with("error","check fee status");
            }
             if ($regis == 0) {
                return redirect('/course_registration');
            }
            }
            
            

            $data = $this->transcriptHeader($sql, $sys);
            $record = $this->generateTranscript($sql->INDEXNO, $sys);

            $agent = new Agent();

            $agent->is('Windows');
            $agent->is('Firefox');
            $agent->is('iPhone');
            $agent->is('OS X');
            $agent->isMobile();
            $agent->isTablet();

            $agent->isAndroidOS();
            $agent->isNexus();
            $agent->isSafari();
            $device = $agent->device();
            $platform = $agent->platform();
            $browser = $agent->browser();

            $browser = $agent->browser();
            $version = $agent->version($browser);

            $platform = $agent->platform();
            $version = $agent->version($platform);
            // dd($version);
            $agent->isDesktop();

            if($agent->isPhone()){


                return view("courses.transcriptMobile")->with('grade', $record)->with("student", $data)->with("owing", $owing);
            }
            else{
                return view("courses.transcript")->with('grade', $record)->with("student", $data)->with("owing", $owing);

            }


        }
        else{
            return redirect('/dashboard')->with("error","Results - awaiting departmental awards meeting.Try again later");
        }


    }

    public function generateTranscript($sql,  SystemController $sys){

        $arrayb=$sys->getSemYear();
        $resultb=$arrayb[0]->RESULT_BLOCK;
        // dd($resultb);

        $resultba = explode(',',$resultb);
        $resultbayear = $resultba[0];
        $resultbasem = $resultba[1];//[\DB::raw("CONCAT(year,',',sem)"),'!=', $resultb],


        $records=  Models\AcademicRecordsModel::where([['indexno','=',$sql],['grade','!=', 'E'],['grade','!=', 'IC'],['grade','!=', 'NC']])->groupBy("year")->groupBy("level")->orderBy("level")->get();
        $programObject=Models\StudentModel::where('INDEXNO',$sql)->select("PROGRAMMECODE")->get();
        $program=$programObject[0]->PROGRAMMECODE;
        $rsaProgram = $sys->getProgramResult($program);
        ?>


        <table width='700px' style="text-align:left; margin-top:-2px; font-size: 16px" height="90" class=""  border="0">
            <tr>

                <td  style=" " align="left">
                    <?php
                    $rsc = 0.0;
                    $rsa = 0.0;
                    $totrsa = 0.0;
                    $noCourses = 0.0;
                    $gpoint=0.0;
                    $totcredit=0;
                    $totgpoint=0.0;
                    $gcredit=0;
                    $b=0.0;
                    $a=0;
                    foreach ($records as $row){
                        for($i=1;$i<3;$i++){
                            $query=  Models\AcademicRecordsModel::where("indexno",$sql)->where("year",$row->year)->where("sem",$i)->where(\DB::raw("CONCAT(year,',',sem)"),'!=', $resultb)->orderby("code")->orderby("resit")->get()->toArray();


                            if(count($query)>0){

                                echo "<div class='uk-text-bold' align='left' style='margin-left:18px'>YEAR : ".$row->year."    ";
                                echo ", LEVEL :  " .$row->level;
                                echo ", SEMESTER : ".$i." <hr/></div>";






                                ?>

                                <div class="uk-overflow-container">
                                <table style="margin-left:18px"  border="0" style="" width='826px'  class="uk-table uk-table-striped">
                                    <thead >
                                    <tr class="uk-text-bold" style="background-color:#1A337E;color:white;">
                                        <td  width="86">CODE</td>
                                        <td  width="418">COURSE</td>
                                        <td align='center' width="50">CR</td>
                                            <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td align='center' width="50">GD</td>
                                            <?php
                                                }
                                            ?>
                                        <td align='center' width="50">MK</td>
                                            <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td align='center' width="57">GP</td>
                                            <?php
                                                }
                                            ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    foreach ($query as $rs){

                                    if($rs['grade']!="IC" and $rs['grade']!="E" and $rs['grade']!="NC"){

                                    ?>
                                    <tr>
                                        <td <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>> <?php $object=$sys->getCourseByCodeProgramObject($rs['code'],$program); $noCourses++; echo @$object[0]->COURSE_CODE; ?></td>
                                        <td <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>> <?php
                                            if($rs['resit']=="yes"){
                                                echo "<span style='color:red'>* </span>".@$object[0]->COURSE_NAME."<span style='color:red'> *</span>";}else{echo @$object[0]->COURSE_NAME;}?> </td>

                                        <td align='center' <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>><?php  @$gcredit+=@$rs['credits'];   $totcredit+=@$rs['credits'];@$a+=$totcredit; if($rs['credits']){ echo $rs['credits'];} else{echo "##";};?></td>
                                         <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td align='center' <?php // if($rs['grade']=="E" || $rs['grade']=="F"){ echo "style='display:none'";}?>><?php  if($rs['grade']){ echo @$rs['grade'];} else{echo "##";}?></td>
                                            <?php
                                                }
                                            ?>
                                        <td align='center' <?php // if($rs['grade']=="E" || $rs['grade']=="F"){ echo "style='display:none'";}?>><?php  @$rsc+=@$rs['total']; if($rs['total']){ echo @$rs['total'];} else{echo "IC";}?></td>

                                            <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td align='center' <?php // if($rs['grade']=="E"|| $rs['grade']=="F"){ echo "style='display:none'";}?>>
                                            <?php   @$gpoint+=@$rs['gpoint']; @$totgpoint+=@$rs['gpoint'];@$b+=@$totgpoint;if($rs['gpoint']){ echo $rs['gpoint'];} else{echo "0";}  ?></td>



                                        <?php
                                    }
                                        }
                                        }?>
                                    </tr>
                                    <tr>

                                        <td>&nbsp</td>

                                        <?php if ($rsaProgram == 'RSA') 
                                        {
                                            
                                         ?>
                                         <td class="uk-text-bold"><span>CPA</span>
                                         <?php
                                            $rsa = @($rsc/$noCourses); @$totrsa+=@$rsa; echo  number_format(@($totrsa/$totcredit), 3, '.', ','); ?>
                                            &nbsp; </td>
                                         <?php
                                        }
                                        else
                                        {
                                        ?>

                                        <td class="uk-text-bold"><span>GPA</span> <?php echo  number_format(@($gpoint/$gcredit), 3, '.', ',');?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
                                        <?php
                                        }
                                        ?>


                                        <td class="uk-text-bold" align='center'><?php echo $gcredit; ?></td>
                                        <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td >&nbsp;</td>
                                        <?php
                                                }
                                            
                                            ?>
                                        <td class="uk-text-bold" align='center'><?php if ($rsaProgram == 'RSA') { echo $rsc;  }?>&nbsp;</td>
                                        <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td class="uk-text-bold" align='center'><?php echo $gpoint; ?>&nbsp;</td>
                                         <?php
                                                }
                                            
                                            ?>
                                    </tr>
                                    <tr>

                                        <td>&nbsp</td>

                                        <?php
                                                if ($rsaProgram == 'RSA') {
                                        ?>    
                                            
                                        <td class="uk-text-bold"> <?php 
                                        if (@($totrsa/$totcredit) > 3.994) {echo 'Competent with Distinction';}
                                        elseif(@($totrsa/$totcredit) > 2.994) {echo 'Competent with Merit';}
                                        elseif(@($totrsa/$totcredit) > 1.994) {echo 'Competent';}
                                        
                                        else {echo 'Not yet Competent';}?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
                                        
                                    <?php
                                    }
                                    else
                                    {
                                        ?>

                                        <td class="uk-text-bold"><span>CGPA</span> <?php echo  number_format(@($totgpoint/$totcredit), 3, '.', ',');
                                        if ($totgpoint/$totcredit > 3.994) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;First Class';}
                                        elseif($totgpoint/$totcredit > 2.994) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;Second Upper';}
                                        elseif($totgpoint/$totcredit > 1.994) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;Second lower';}
                                        elseif($totgpoint/$totcredit >1.494) {echo ' &nbsp;&nbsp;&nbsp;&nbsp;Pass';}
                                        else {echo '&nbsp;&nbsp;&nbsp;&nbsp;Fail';}?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
                                     <?php   
                                    }
                                    ?>
                                        <td class="uk-text-bold" align='center'><?php echo   $totcredit; ?></td>
                                        <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td >&nbsp;</td>
                                        <?php
                                                }
                                            
                                            ?>
                                            <td class="uk-text-bold" align='center'><?php if ($rsaProgram == 'RSA') { echo  number_format(@($rsc/$noCourses), 3, '.', ','); }?>&nbsp;</td>
                                            <?php
                                                if ($rsaProgram != 'RSA') {
                                            
                                            ?>
                                        <td class="uk-text-bold" align='center'><?php echo $totgpoint;   $b="";$a=""; ?>&nbsp;</td>
                                        <?php
                                                }
                                            
                                            ?>
                                    </tr>

                                    </tbody>

                                    <?php
                                    $gpoint=0.0;
                                    $gcredit=0;
                                    $rsc=0;
                                    $noCourses=0;
                                    ?>
                                </table>
                            <?php }else{
                                echo "<p class='uk-text-danger'>No results to display</p>";
                                ?><?php }?>
                            <p>&nbsp;</p>
                            </div><?php }  }

                    ?>


            </tr>

        </table>
        </td>
        </tr>

        </table>
        </div></div>

    <?php }
    public function transcriptHeader($student, SystemController $sys) {
        ?>
        <div class="md-card" style="overflow-x: auto;" >

        <div   class="uk-grid" data-uk-grid-margin>

        <table  border="0" valign="top" cellspacing="0" align="left">
        <tr>
        <td>
        <table width="826px" style="margin-left:18px" height="133">
            <tr>
                <td class="uk-text-danger uk-text-left" colspan="3"><blinks>Contact TPConnect Office for any issue regarding this results. </blinks></td>
            </tr>
            <tr>
                <td colspan="3" align='left'> <img src="<?php echo url('public/assets/img/academic.jpg')?>" style='width: 826px;height: auto;margin-bottom: 10px;'/></td>

            </tr>
            <tr>
                <td class="uk-text-bold"style="padding-right: px;">INDEX NUMBER</td>
                <td style=""><?php echo $student->INDEXNO;?></td>
                <td rowspan="5" width="145">&nbsp;
                    <img   style="width:130px;height: auto;margin-left: 8px"
                        <?php
                        $pic = $student->INDEXNO;
                        ?>
                           src='<?php echo url("https://www.ttuportal.com/srms/public/albums/students/$pic.JPG")?>' onerror="this.onerror=function my(){return this.src='<?php echo url("https://www.ttuportal.com/srms/public/albums/students/USER.JPG")?>';};this.src='<?php echo url("https://www.ttuportal.com/srms/public/albums/students/$pic.jpg")?>';"   />
                </td>
            </tr>
            <tr>
                <td class="uk-text-bold" style="">NAME</td> <td style=""><?php echo strtoupper($student->TITLE .' '.  $student->NAME)?></td>
            </tr>
            <tr>
                <td class="uk-text-bold"style="">GENDER</td> <td style=""><?php echo strtoupper($student->SEX)?></td>
            </tr>
            <tr>
                <td class="uk-text-bold">PROGRAMME</td> <td style=""><?php echo strtoupper($student->programme->PROGRAMME)?></td>
            </tr>

            <tr>
                <td class="uk-text-bold" style="">DATE OF BIRTH</td> <td style=""><?PHP echo  $student->DATEOFBIRTH ; ?></td>
            </tr>
            <tr>
                <td class="uk-text-left" colspan="3">&nbsp;<br/>For HND only. &nbsp;&nbsp;Grade &nbsp;= &nbsp;Value, &nbsp;&nbsp;&nbsp;A+ &nbsp;= &nbsp;5.0, &nbsp;&nbsp;&nbsp;A &nbsp;= &nbsp;4.5, &nbsp;&nbsp;&nbsp;B+ &nbsp;= &nbsp;4.0, &nbsp;&nbsp;&nbsp;B &nbsp;= &nbsp;3.5, &nbsp;&nbsp;&nbsp;C+ &nbsp;= &nbsp;3, &nbsp;&nbsp;&nbsp;C &nbsp;= &nbsp;2.5, &nbsp;&nbsp;&nbsp;D+ &nbsp;= &nbsp;2, &nbsp;&nbsp;&nbsp;D &nbsp;= &nbsp;1.5, &nbsp;&nbsp;&nbsp;F &nbsp;= &nbsp;0, &nbsp;&nbsp;&nbsp;red asterisk means resit
                </td>
            </tr>
            <tr>
                <td class="uk-text-left" colspan="3">&nbsp;
                </td>
            </tr>
        </table><!-- end basic infos -->



        <?php

    }

    public function show(Request $request) {

    }


    public function register(Request $request, SystemController $sys)
    {

        $student=@\Auth::user()->username;
        $array=$sys->getSemYear();
        $sem=$array[0]->SEMESTER;
        $year=$array[0]->YEAR;

        $status=$array[0]->STATUS;

        if($status==1) {
            // check requisits for registrations
            $userModel = User::query()->where('username', $student)->where('active', '1')->first();
            $studentUpdate = $userModel->biodata_update;
            if ($studentUpdate == 0) {
                return redirect('/biodataUpdate');
            }


            if ($request->isMethod("get")) {
                $studentRecords = @Models\StudentModel::where("INDEXNO", $student)->where("ALLOW_REGISTER", "1")->first();
                $qa = @$studentRecords->QUALITY_ASSURANCE;

                if ($sem == 1) {
                if ($studentRecords->LEVEL == '200H' || $studentRecords->LEVEL == '300H' || $studentRecords->LEVEL == "200BTT") {
                    if ($qa == 0) {
                        return redirect('lecturer/assessment');
                    }
                }
            }
            elseif($sem == 2) {
                if ($studentRecords->LEVEL == '100H'|| $studentRecords->LEVEL == '200H' || $studentRecords->LEVEL == '300H' || $studentRecords->LEVEL == "200BTT" || $studentRecords->LEVEL == '100BTT') {
                    if ($qa == 0) {
                        return redirect('lecturer/assessment');
                    }
                }
            }
                
                if (!empty($studentRecords)) {
                    $owing = @$studentRecords->BILL_OWING;
                    $bill = @$studentRecords->BILLS;
                    $register = @$studentRecords->ALLOW_REGISTER;
                    $studentStatus = @$studentRecords->STATUS;
                    $qa = @$studentRecords->QUALITY_ASSURANCE;


                    $studentDetail = @$studentRecords;

                    $courseCore = @Models\MountedCourseModel::query()->where('COURSE_SEMESTER', $sem)->where('COURSE_LEVEL', $studentDetail->LEVEL)->where('PROGRAMME', $studentDetail->PROGRAMMECODE)->where('COURSE_YEAR', $year)->where('COURSE_TYPE', 'Core')->groupBy('COURSE_CODE')->paginate(100);

                    $courseElective = @Models\MountedCourseModel::query()->where('COURSE_SEMESTER', $sem)->where('COURSE_LEVEL', $studentDetail->LEVEL)->where('PROGRAMME', $studentDetail->PROGRAMMECODE)->where('COURSE_YEAR', $year)->where('COURSE_TYPE', 'Elective')->groupBy('COURSE_CODE')->paginate(100);
                    $courseResit = @Models\MountedCourseModel::query()->where('COURSE_SEMESTER', $sem)->where('COURSE_LEVEL', $studentDetail->LEVEL)->where('PROGRAMME', $studentDetail->PROGRAMMECODE)->where('COURSE_YEAR', $year)->where('COURSE_TYPE', 'Resit')->paginate(100);
                    $paid = Models\FeePaymentModel::where("INDEXNO", $studentRecords->INDEXNO)
                        ->where("YEAR", $year)
                        //->where("SEMESTER", $sem)
                        ->sum("AMOUNT");
                    $balance = $bill - $paid;

                    if ($paid >= 0.99*$bill or $studentRecords->PROTOCOL == '1' or $studentRecords->vw == '1') {
                        return view('courses.register')
                            ->with('year', $year)
                            ->with('sem', $sem)
                            ->with('data', $studentDetail)
                            ->with('course', $courseCore)
                            ->with('courseElective', $courseElective)
                            ->with('courseResit', $courseResit)
                            ->with('owing', $owing)
                            ->with('bill', $bill)
                            ->with('paid', $paid)
                            ->with('register', $register)
                            ->with('qa', $qa)
                            ->with('studentStatus', $studentStatus);
                    } else {
                        dd("You owe school fees contact finance. Amount is GHS" . $balance);
                    }


                } else {
                    // abort(434, "{!!<b>Course Registration has not been open yet contact ICT Officer</b>!!}");
                    return redirect("/registered_courses")->with("error", "Registration has not begun.");

                }
            } elseif ($request->isMethod("post")) {
                /*do post to database here
                 * @request type Post
                 */
                \DB::beginTransaction();
                try {

                    $core = $request->input('core');
                    $resit = $request->input('resit');

                    $elective = $request->input('elective');
                    //dd($elective);
                    $level = $request->input('level');
                    $credit = $request->input('credit');
                    $totalHours = $request->input('hours');
                    $yeargroup = $request->input('yearGroup');
                    $studentID = $sys->getStudentIDfromIndexno($student);

                    @Models\AcademicRecordsModel::query()->where('student', $studentID)
                        ->where('year', $year)
                        ->where('sem', $sem)
                        ->delete();

                    if ($core) {
                        //  dd(count($core));

                        for ($i = 0; $i < count($core); $i++) {


                            $queryModel = new Models\AcademicRecordsModel();
                            $queryModel->course = $core[$i];
                            $queryModel->code = $sys->getCourseCodeByID($core[$i]);
                            $queryModel->credits = $credit[$i];
                            $queryModel->student = $studentID;
                            $queryModel->indexno = $student;
                            $queryModel->yrgp = $yeargroup;
                            $queryModel->year = $year;
                            $queryModel->sem = $sem;
                            $queryModel->level = $level;
                            $queryModel->lecturer = $sys->getLecturer($core[$i]);
                            $queryModel->dateRegistered = \date('Y-m-d H:i:s');

                            if ($queryModel->save()) {
                                // let increase the credit hours done by the student


                            }

                        }
                        $oldHours = Models\StudentModel::where("INDEXNO", $student)->where('STATUS', '=', 'In school')->orWhere('STATUS', '=', 'Admitted')->first();
                        $durationCredit = $sys->getProgrammeMinCredit(@$oldHours->PROGRAMMECODE);

                        $newHours = @$oldHours->TOTAL_CREDIT_DONE + $totalHours;

                        $leftHours = $durationCredit - $newHours;

                        Models\StudentModel::where('INDEXNO', $student)->update(array('TOTAL_CREDIT_DONE' => $newHours, 'STATUS' => 'In school', 'CREDIT_LEFT_COMPLETE' => $leftHours, 'REGISTERED' => '1'));
                        \DB::commit();

                    }
                    if ($resit) {

                        for ($i = 0; $i < count($resit); $i++) {
                            $queryResit = new Models\AcademicRecordsModel();
                            $queryResit->course = $resit[$i];

                            $queryResit->credits = $sys->getCreditHours($resit[$i]);
                            $queryResit->student = $studentID;
                            $queryResit->indexno = $student;
                            $queryResit->yrgp = $yeargroup;
                            $queryResit->year = $year;
                            $queryResit->sem = $sem;
                            $queryResit->level = $level;
                            $queryResit->dateRegistered = \date('Y-m-d H:i:s');
                            $queryResit->lecturer = $sys->getLecturer($resit[$i]);
                            if ($queryResit->save()) {

                            }
                        }
                        /* $oldHours = Models\StudentModel::where("INDEXNO", $student)->where('STATUS', '=', 'In school')->first();
                         $durationCredit = $sys->getProgrammeMinCredit(@$oldHours->PROGRAMMECODE);

                         $newHours = @$oldHours->TOTAL_CREDIT_DONE + $totalHours;

                         $leftHours = $durationCredit - $newHours;

                         Models\StudentModel::where('INDEXNO', $student)->update(array('TOTAL_CREDIT_DONE' => $newHours, 'CREDIT_LEFT_COMPLETE' => $leftHours, 'REGISTERED' => '1'));

                         */
                        \DB::commit();
                    }
                    if ($elective) {
                        //  dd($elective);


                        $queryElective = new Models\AcademicRecordsModel();
                        $queryElective->course = $elective;
                        $queryElective->code = $sys->getCourseCodeByID($elective);
                        // dd($sys->getCreditHours($elective));
                        $queryElective->credits = $sys->getCreditHours($elective);
                        $queryElective->student = $studentID;
                        $queryElective->yrgp = $yeargroup;
                        $queryElective->year = $year;
                        $queryElective->indexno = $student;
                        $queryElective->sem = $sem;
                        $queryElective->level = $level;
                        $queryElective->dateRegistered = \date('Y-m-d H:i:s');
                        $queryElective->lecturer = $sys->getLecturer($elective);
                        if ($queryElective->save()) {
                            $oldHours = Models\StudentModel::where("INDEXNO", $student)->where('STATUS', '=', 'In school')->first();
                            $durationCredit = $sys->getProgrammeMinCredit(@$oldHours->PROGRAMMECODE);

                            $newHours = @$oldHours->TOTAL_CREDIT_DONE + $totalHours;

                            $leftHours = $durationCredit - $newHours;

                            Models\StudentModel::where('INDEXNO', $student)->update(array('TOTAL_CREDIT_DONE' => $newHours, 'CREDIT_LEFT_COMPLETE' => $leftHours, 'REGISTERED' => '1'));

                            \DB::commit();


                        }
                    }

                    $url = url("printOut/" . trim($student));

                    $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";

                    $request->session()->flash("success", "Course registered successfully   $print_window");
                    return redirect("/dashboard");

                } catch (\Exception $e) {
                    \DB::rollback();

                }

            }

        }
        else{
            return redirect("/dashboard")->with("error", "Course registration closed. Contact 0246091283 / 0505284060");

        }
    }
    /*
     * Printing registration form after registering
     */

    public function printRegistration(Request $request, $student, SystemController $sys) {

        $studentIndexNo=$sys->getStudentIDfromIndexno(@\Auth::user()->username);
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $studentDetail = Models\StudentModel::query()->where('STATUS', 'In School')->where('INDEXNO', $student)

            ->first();

        $query = Models\AcademicRecordsModel::where("student",  $studentIndexNo)->where("year", $year
        )
            ->where('sem',$sem)
            ->paginate(100);


        if (empty($query)) {
            abort(434, " No registration with this student  $student ");
        }


        return view("students.print_registration")->with("student", $studentDetail)->with('course', $query)->with('year', $year)->with('sem',$sem);

    }
    public function registeredCourses(Request $request,   SystemController $sys) {
        $studentIndexNo=$sys->getStudentIDfromIndexno(@\Auth::user()->username);
        $student=@\Auth::user()->username;
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $studentDetail = Models\StudentModel::query()->where('STATUS', 'In school')->where('INDEXNO', $student)

            ->first();

        $query = Models\AcademicRecordsModel::where("student",  $studentIndexNo)->where("year", $year
        )
            ->where('sem',$sem)
            ->paginate(100);


        if (empty($query)) {
            abort(434, " No registration with this student  $student ");
        }


        return view("students.print_registration")->with("student", $studentDetail)->with('course', $query)->with('year', $year)->with('sem',$sem);

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
    public function generateIndexNumber(Request $request, SystemController $sys){

    }
}
