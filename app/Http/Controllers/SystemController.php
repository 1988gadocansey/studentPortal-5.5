<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\MessagesModel; 

use App\Models;

use App\Http\Requests;

use App\Http\Controllers\Controller;

use PhpParser\Node\Expr\AssignOp\Mod;





class SystemController extends Controller

{

     

    /**

     * Create a new controller instance.

     *

     * @param  TaskRepository  $tasks

     * @return void

     */

    public function __construct()

    {

       // $this->middleware('auth');



        

    }

    public function getClass($cgpa){

        $class = \DB::table('tpoly_classes')->where('lowerBoundary','<=',$cgpa)
            ->where("upperBoundary",">=",$cgpa)
            ->first();

        return @$class->class;

    }

    public function getCGPA($indexno) {

        $totalCR=Models\AcademicRecordsModel::where("indexno",$indexno)->where("grade","!=","E")->sum("credits");
        $totalGP=Models\AcademicRecordsModel::where("indexno",$indexno)->where("grade","!=","E")->sum("gpoint");


        if($totalCR<=0 ||  $totalGP<=0){
            return 0;
        }
        return round( $totalGP/$totalCR,3);
    }

    public function getCourseByCodeProgramObject($id,$program) {



        $courseObject = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->where("PROGRAMME",$program)->select

        ("COURSE")->get();

        //dd( $courseObject);

        if(@count($courseObject)>=1){

            @$courseMount=$courseObject[0]->COURSE;

            @$course = \DB::table('tpoly_courses')->where('ID',$courseMount)->get();

            if(@count($course)>=1){

                return @$course;

                }

                else{

        //$course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->where("PROGRAMME",$program)->get();

          @$course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();  

          return @$course;

        }

        }

        else{

        //$course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)->where("PROGRAMME",$program)->get();

          $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();  

          return @$course;

        }

    }

     // return course array based on code

    public function getCourseByCodeObject($id) {

         

         $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();

          

                 

        return @$course;

    }

       public function age($birthdate, $pattern = 'eu')

        {

            $patterns = array(

                'eu'    => 'd/m/Y',

                'mysql' => 'Y-m-d',

                'us'    => 'm/d/Y',

                'gh'    => 'd-m-Y',

            );



            $now      = new \DateTime();

            $in       = \DateTime::createFromFormat($patterns[$pattern], $birthdate);

            $interval = $now->diff($in);

            return $interval->y;

        }

     public function getReligion() {

         $religion = \DB::table('tbl_religion')

                ->pluck('religion', 'religion');

        return $religion;

    }

    public function getCountry() {

         $country = \DB::table('tbl_country')

                ->pluck('Name', 'Name');

        return $country;

    }

    public function getHalls() {

         $hall = \DB::table('tpoly_hall')

                ->pluck('HALL_NAME', 'HALL_NAME');

        return $hall;

    }

    public function getZones() {

        $zone = \DB::table('liaison_zones')

            ->pluck('sub_zone', 'id');

        return $zone;

    }

    public function getAddress() {

        $address = \DB::table('liaison_address')

            ->pluck('addresses', 'id');

        return $address;

    }

     public function getRegions() {

         $region = \DB::table('tbl_regions')

                ->pluck('Name', 'Name');

        return $region;

    }

    public function getProgramByIDList() {

         $program = \DB::table('tpoly_programme')

                ->pluck('PROGRAMME', 'ID');

         return $program;

       

         

    }

    // this is purposely for select box 

    public function getProgramList() {

         $program = \DB::table('tpoly_programme')

                ->pluck('PROGRAMME', 'PROGRAMMECODE');

         return $program;

       

         

    }

        public function getProgramResult($code) {

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE', $code)->get();

        return @$programme[0]->RESULT;
    }

    public function graduatingGroup($indexNo) {

  //       $level= substr($indexNo, 2,2);
//
  //       $group="20".$level;
//
    //     $group_=($group + 3)."/".($group + 4);

         

      //   return $group_;

               

    }

     public function years() {

            

             for ($i = 2008; $i <= 2030; $i++) {

             $year = $i - 1 . "/" . $i;

             $years[$year]= $year;

             }

             return $years;

        }

    // this is purposely for select box 

    public function getCourseList() {

         $course = \DB::table('tpoly_courses')

                ->pluck('COURSE_NAME', 'ID');

         return $course;

       

         

    }

    // this is purposely for select box 

    public function getLectureList() {

         $lecturer = \DB::table('tpoly_workers')->where('designation','Lecturer')

                ->pluck('fullName', 'id');

         return $lecturer;

       

         

    }

    public function getLectureListQA($program,$level) {

        //$levels=["100H"=>"200H","200H"=>"300H","100BTT"=>"200BT","100NT"=>"200NT","500MT"=>"600MT"];
        $levels=["100H"=>"200H","200H"=>"300H","100BTT"=>"200BTT","100NT"=>"200NT","500MT"=>"600MT","100BT"=>"200BT"];

        $level=array_search($level,$levels);


            $lecturer=  \DB::table('tpoly_mounted_courses')
                ->join('tpoly_workers', function($join)
                {
                    $join->on('tpoly_mounted_courses.LECTURER','=','tpoly_workers.staffID');
                })
                ->where('tpoly_mounted_courses.COURSE_LEVEL',$level)
                ->where('PROGRAMME',$program)

            ->select('tpoly_workers.fullName', 'tpoly_workers.staffID')->distinct()->get();
        return $lecturer;


    }

    public function getLevelList() {





        $level = \DB::table('tpoly_levels')

            ->select('slug', 'name')->get();

        return $level;





    }

     // this is purposely for select box 

    public function getUsers() {

         $user= \DB::table('users')

                ->pluck('name', 'id');

         return $user;

       

         

    }

    public function department() {

         $department= \DB::table('tpoly_department')

                ->pluck('DEPARTMENT', 'DEPTCODE');

         return $department;

       

         

    }

     public function getLecturer($course) {

         $lecturer= \DB::table('tpoly_mounted_courses')->where("ID",$course)->get();

                 

         return $lecturer[0]->LECTURER;

       

         

    }

    public function totalRegistered($sem,$year,$course,$level) {

        $query=Models\AcademicRecordsModel::where('sem',$sem)

                ->where('year',$year)

                ->where('level',$level)

                ->where('course',$course)

                ->count('student');

        return $query;

    }

    

     public function firesms($message,$phone,$receipient){

          

         

        

        //print_r($contacts);

        if (!empty($phone)&& !empty($message)&& !empty($receipient)) {

             \DB::beginTransaction();

            try {



                 

                //$key = "83f76e13c92d33e27895";

                $message = urlencode($message);

                $phone=$phone; // because most of the numbers came from excel upload

                 

                 $phone="+233".\substr($phone,1,9);

            $url = 'http://txtconnect.co/api/send/'; 

            $fields = array( 

            'token' => \urlencode('a166902c2f552bfd59de3914bd9864088cd7ac77'), 

            'msg' => \urlencode($message), 

            'from' => \urlencode("TPOLY"), 

            'to' => \urlencode($phone), 

            );

            $fields_string = ""; 

                    foreach ($fields as $key => $value) { 

                    $fields_string .= $key . '=' . $value . '&'; 

                    } 

                    \rtrim($fields_string, '&'); 

                    $ch = \curl_init(); 

                    \curl_setopt($ch, \CURLOPT_URL, $url); 

                    \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true); 

                    \curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true); 

                    \curl_setopt($ch, \CURLOPT_POST, count($fields)); 

                    \curl_setopt($ch, \CURLOPT_POSTFIELDS, $fields_string); 

                    \curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, 0); 

                    $result2 = \curl_exec($ch); 

                    \curl_close($ch); 

                    $data = \json_decode($result2); 

                    $output=@$data->error;

                    if ($output == "0") {

                   $result="Message was successfully sent"; 

                   

                    }else{ 

                    $result="Message failed to send. Error: " .  $output; 

                     

                    } 

                     

                

                $array=  $this->getSemYear();

                $sem=$array[0]->SEMESTER;

                $year=$array[0]->YEAR;

                  $user = \Auth::user()->id;

                  $sms=new MessagesModel();

                    $sms->dates=\DB::raw("NOW()");

                    $sms->message=$message;

                    $sms->phone=$phone;

                    $sms->status=$result;

                    $sms->type="Fees reminder";

                    $sms->sender=$user;

                    $sms->term=$sem;

                    $sms->year=$year;

                    $sms->receipient=$receipient;

                     

                   $sms->save();

                   \DB::commit();

               } catch (\Exception $e) {

                \DB::rollback();

            }

            }

        

    }

    /**

     * Get current sem and year

     *

     * @param  Request  $request

     * @return Response

     */

    public function getSemYear()

    {

        $sql =\DB::table('tpoly_academic_settings')->where('ID', \DB::raw("(select max(`ID`) from tpoly_academic_settings)"))->get();

        return $sql;

    }

     

    public function getProgram($code){

        

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$code)->get();

                 

        return @$programme[0]->PROGRAMME;

     

    }

     public function getCreditHours($course){

        $array=  $this->getSemYear();

        $sem=$array[0]->SEMESTER;

        $year=$array[0]->YEAR;

        $hours = \DB::table('tpoly_mounted_courses')->where('ID',$course)

                ->where('COURSE_SEMESTER',$sem)

                ->where('COURSE_YEAR',$year)

                ->get();

            

        return @$hours[0]->COURSE_CREDIT;

     

    }

    public function getProgrammeMinCredit($program) {

          $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->get();

                 

        return @$programme[0]->MINCREDITS;

    }

    public function getProgramCode($id){

        

        $programme = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$id)->get();

                 

        return @$programme[0]->PROGRAMMECODE;

     

    }

    

    

    public function getProgramCodeByID($id){

        

        $programme = \DB::table('tpoly_programme')->where('ID',$id)->get();

                 

        return @$programme[0]->PROGRAMMECODE;

     

    }

      public function getProgramStudent($student){        

        $program = \DB::table('tpoly_students')->where('INDEXNO',$student)->get();                 

        return @$program[0]->PROGRAMMECODE;     

    }
    

      public function getProgramDepartment($program){

        

        $department = \DB::table('tpoly_programme')->where('PROGRAMMECODE',$program)->get();

                 

        return @$department[0]->DEPTCODE;

     

    }

    public function getDepartmentName($deptCode){

        

        $department = \DB::table('tpoly_department')->where('DEPTCODE',$deptCode)->get();

                 

        return @$department[0]->DEPARTMENT;

     

    }

    

    

    public function getSchoolCode($dept){

        

        $school = \DB::table('tpoly_department')->where('DEPTCODE',$dept)->get();

                 

        return @$school[0]->FACCODE;

     

    }

    

     public function getSchoolName($dept){

        

        $faculty = \DB::table('tpoly_faculty')->where('FACCODE',$dept)->get();

                 

        return @$faculty[0]->FACULTY;

     

    }

    

    

    public function getProgramByID($id) {

         $programme = \DB::table('tpoly_programme')->where('ID',$id)->get();

                 

        return @$programme[0]->PROGRAMME;

    }

    public function getGrade($mark,$type){

        

        $grade = \DB::table('tpoly_grade_system')->where('lower','<=',$mark)

                ->where('lower','<=',$mark)

                ->where('upper','>=',$mark)

                ->where('type',$type)

                ->get();

                 

        return $grade;

     

    }

    public function getStudentByID($id){

        

        $student = \DB::table('tpoly_students')->where('ID',$id)->get();

                 

        return @$student[0]->INDEXNO;

     

    }

    public function getStudentIDfromIndexno($indexno) {

        $student = \DB::table('tpoly_students')->where('INDEXNO',$indexno)->get();

                 

        return @$student[0]->ID;

    }

    public function getStudentNameByID($id){

        

        $student = \DB::table('tpoly_students')->where('ID',$id)->get();

                 

        return @$student[0]->NAME;

     

    }

     public function getCourseCodeByID($id){

        

                  

       

     $course =   \DB::table('tpoly_mounted_courses')

    ->join('tpoly_courses', function($join)

    {

        $join->on('tpoly_mounted_courses.COURSE','=','tpoly_courses.ID');

    })

    ->where('tpoly_mounted_courses.ID',$id)

    ->get();

      return @$course[0]->COURSE_CODE;

    }

     public function getCourseByID($id){

        

        $course = \DB::table('tpoly_courses')->where('COURSE_CODE',$id)->get();

                 

        return @$course[0]->COURSE_NAME;

     

    }

    public function getCourseByIDAQ($id,$program){



        $course = \DB::table('tpoly_mounted_courses')->where('COURSE_CODE',$id)

            ->where('PROGRAMME',$program)



            ->get();



        return @$course[0]->ID;



    }
    public function getStudentAppnofromIndexno($indexno) {
        $student = \DB::table('tpoly_students')->where('INDEXNO',$indexno)->get();

        return  @$student[0]->STNO;
    }



    public function getTotalPayment($student, $yearr)
    {
        //$sys = new SystemController();
        $array = $this->getSemYear();
        
            $yearr = $array[0]->YEAR;
        
        $id = $this->getStudentIDfromIndexno($student);

        $appNo = $this->getStudentAppnofromIndexno($student);

        $fee = \DB::table('tpoly_feedetails')->where('YEAR', '=', $yearr)->where('STUDENT', $id)->sum('AMOUNT');

        //$fee2 = \DB::table('tpoly_feedetails')->where('YEAR', '=', $yearr)->where('INDEXNO', $appNo)->sum('AMOUNT');
        //$fee = $fee1+$fee2;
        return $fee;


    }

    public function getStudentFee($student,$level){
        $array = $this->getSemYear();
        
            $yearr = $array[0]->YEAR;
       $program=  $this->getProgramStudent($student);

        $fee = \DB::table('tpoly_bills')->where('PROGRAMME',$program)->where('LEVEL',$level)->where('YEAR',$yearr)->get();

                 

        return @$fee[0]->AMOUNT;

                // dd($total);

        //return @$total;

    }

     public function getTotalFeeByProrammeLevel($program,$level){

       $program=  $this->getProgramCodeByID($program);

        $total = \DB::table('tpoly_students')->where('PROGRAMMECODE',$program)->where('YEAR',$level)->where('STATUS','=','In school')->COUNT('*');

                // dd($total);

        return @$total;

    }

   public function picture($path,$target){

                if(file_exists($path)){

                        $mypic = getimagesize($path);



                 $width=$mypic[0];

                        $height=$mypic[1];



                if ($width > $height) {

                $percentage = ($target / $width);

                } else {

                $percentage = ($target / $height);

                }



                //gets the new value and applies the percentage, then rounds the value

                 $width = round($width * $percentage);

                $height = round($height * $percentage);



               return "width=\"$width\" height=\"$height\"";







            }else{}

        

       

        }

        

        

	public function pictureid($stuid) {



        return str_replace('/', '', $stuid);

    }

     

    function formatMoney($number, $fractional=false) { 

    if ($fractional) { 

        $number = sprintf('%.2f', $number); 

    } 

    while (true) { 

        $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number); 

        if ($replaced != $number) { 

            $number = $replaced; 

        } else { 

            break; 

        } 

    } 

    return $number; 

    }

    public function formatCurrency($amount) {

       return number_format($amount,3);

            

    }

    

    public function checkRegistrationRequisits($student) {

        

        

         

    }

    public function getProgrammeType($program){

        $sql=Models\ProgrammeModel::where("PROGRAMMECODE",$program)->first();

        return $sql->TYPE;

    }

    public function assignIndex($programme){

        $type=$this->getProgrammeType($programme);

        $quote=Models\IndexNumberModel::where("programme",$programme)->where("year",date("Y"))->first();



        if($type=="NON TERTIARY") {

            $index=$quote->code+1;

            Models\IndexNumberModel::where("programme", $programme)->where("year", date("Y"))

                ->update(array("code" => $index));

            return $index;

        }

        else{



            $index=$quote->code+1;



            Models\IndexNumberModel::where("programme", $programme)->where("year", date("Y"))

                ->update(array("code" =>"0".$index));

            return "0".$index;

        }



   }

    public function generateIndexNumbers()

    {

       $sql=Models\StudentModel::select("INDEXNO","PROGRAMMECODE")->groupBy("PROGRAMMECODE")->get();

       foreach ($sql as $row){

            $index=new Models\IndexNumberModel();

            $index->programme=$row->PROGRAMMECODE;



           $program=$row->PROGRAMMECODE;



           if (strpos($program, "H") == 0) {

               $index->code="07".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";

           } elseif (strpos($program, "D") == 0 || strpos($program, "C") == 0|| strpos($program, "E") == 0) {

               $index->code="7".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";

           } elseif (strpos($program, "A") == 0) {

               $index->code="7".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";

           }

           elseif (strpos($program, "B") == 0) {



               $index->code="075".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";

           }



           elseif($program=="MTECHT"||$program=="MTECHP"||$program=="MTECHG"){

               //$index->code="07".substr(date("Y"),2,2);

           }

           else {

               $index->code="7".substr(date("Y"),2,2).substr($row->INDEXNO,4,1)."000";

           }









            $index->year=date("Y");

            $index->save();

       }

    }

    function get_letter_code($programme, $year)

    {



        if ($programme == 'HDT' && $year == '2')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 04";   //hnd Fashion design and Technology

        }



        else if ($programme == 'HCE' && $year == '2')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 01";  //hnd civil engineering

        }

        else if ($programme == 'HCBT' && $year == '2')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 02";  //hnd construction engineering and management

        }

        else if ($programme == 'BTCE' && $year == '2')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 09";  //BTECH Civil engineering and management

        }

        else if ($programme == 'BTBE' && $year == '1')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 13";  //BTECH building technology

        }

        else if ($programme == 'BTP')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 10";  //btech Procurement

        }



        else if ($programme == 'BTT')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 06";  //btech Tourism

        }



        else if ($programme == 'BTX')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 08";  //btech TEXTILES

        }

        else if ($programme == 'BTH')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 05";  //btech hospitality

        }

        else if ($programme == 'BTG')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 07"; //btech Industrial art printing option

        }

        else if ($programme == 'HID')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 12"; //btech Industrial art printing option

        }

        else if ($programme == 'BTIAG-ANIM')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 13"; //BACHELOR OF TECHNOLOGY IN INDUSTRIAL ARTS (GRAPHICS - ANIMATION OPTION)

        }

        else if ($programme == 'BTIAG-MULT')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 14"; //BACHELOR OF TECHNOLOGY IN INDUSTRIAL ARTS (GRAPHICS - MULTIMEDIA OPTION)

        }

        else if ($programme == 'BTIAG-PRINT')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 15"; //BACHELOR OF TECHNOLOGY IN INDUSTRIAL ARTS (GRAPHICS - PRINT PRESS OPTION)

        }

        else if ($programme == 'BTIAG-ADVT')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 16"; //BACHELOR OF TECHNOLOGY IN INDUSTRIAL ARTS (GRAPHICS - ADVERTISING OPTION)

        }

        else if ($programme == 'BTSMGTS')

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 17"; //BACHELOR OF TECHNOLOGY IN SECRETARYSHIP AND MANAGEMENT STUDIES

        }

        else

        {

            $reference_no= "TTU / ILCDO / IAP / VOL. 2 / 11";

        }



        return $reference_no;

    }

    function getlevel($yr)

    {

        if ($yr == 1)

        {

            $yr_show ="I";

        }

        else if ($yr == 2)

        {

            $yr_show ="II";

        }

        else if ($yr == 3)

        {

            $yr_show ="III";

        }

        return $yr_show;

    }

//beginning of another function

    function showSalutation($yr)

    {

        if ($yr == 1)

        {

            $yr_show ="I";

        }

        else if ($yr == 2)

        {

            $yr_show ="II";

        }

        else if ($yr == 3)

        {

            $yr_show ="III";

        }

        return $yr_show;

    }

}

