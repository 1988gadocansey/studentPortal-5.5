<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeModel;
use App\Models\FeePaymentModel;
use App\Models\StudentModel; 
use App\Models\ReceiptModel; 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 
class FeeController extends Controller
{
    
      public function log_query() {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }
    /**
     * Create a new controller instance.
     *
     
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

         
    }
    public function getTotalPayment($student,$term,$yearr) {
        $sys=new SystemController();
              $array=$sys->getSemYear();
              if($term=="" && $yearr==""){
              $term=$array[0]->SEMESTER;
              $yearr=$array[0]->YEAR;
              }
               
        $fee=  FeePaymentModel::query()->where('YEAR', '=',$yearr)->where('SEMESTER',$term)->where('INDEXNO',$student)->sum('AMOUNT');
      return $fee;
        
             
      }
    
    public function masterLedger(Request $request) {
        $sys=new SystemController();
              $array=$sys->getSemYear();
              $sem=$array[0]->SEMESTER;
              $year=$array[0]->YEAR;
        $fee = FeePaymentModel::query()->where('YEAR', '=', $year);
         
        if ($request->has('level') && trim($request->input('level')) != "") {
            $fee->where("LEVEL", $request->input("level", ""));
        }
         
        if ($request->has('indexno') && trim($request->input('indexno')) != "") {
            $fee->where("INDEXNO", '=',$request->input("indexno", ""));
        }
        if ($request->has('year') && trim($request->input('year')) != "") {
            $fee->where("YEAR", "=", $request->input("year", ""));
        }
         
        if ($request->has('program') && trim($request->input('program'))) {
            $fee->where("PROGRAMME", "=", $request->input('program'));
        }
         
          
        if ($request->has('type') && trim($request->input('type'))) {
            $fee->where("PAYMENTTYPE", "=", $request->input('type'));
        }
        $data = $fee->groupBy('INDEXNO')->orderBy('TRANSDATE', 'DESC')->paginate(100);
        
        $request->flashExcept("_token");
         \Session::put('students', $data);
         
          foreach ($data as $key => $row) {
                  $a[]=$row->AMOUNT;
                    //$data[$key]->TOTALS = array_sum($a);
                    
                    $t[]=$this->getTotalPayment($row->INDEXNO, $row->SEMESTER, $row->YEAR);
                     $data[$key]->TOTALS=@array_sum($t);
                }
                
                $totals=@$sys->formatMoney($data[$key]->TOTALS);
        return view('finance.fees.masterLedger')->with("data", $data)
                        ->with('program', $sys->getProgramList())
                        ->with('year',$this->years())
                        ->with('bank',$this->banks())
                        ->with('total',$totals);
                       
    }
    public function feeSummary(Request $request) {
        $sys = new SystemController();
         
      
        if ($request->isMethod("get")) {

            return view('finance.fees.fee_summary')
                            ->with('program', $sys->getProgramByIDList())
                            ->with('year', $this->years());
             
        } 
    else {
             
            $fee = FeeModel::query()->where('STATUS','approved');

            if ($request->has('level') && trim($request->input('level')) != "") {
                 $fee->where("LEVEL", $request->input("level", ""));
             }
            if ($request->has('program') && trim($request->input('program'))) {
                 $fee->where("PROGRAMME", "=", $request->input('program'));
             }
             if ($request->has('year') && trim($request->input('year')) != "") {
                 $fee->where("YEAR", "=", $request->input("year", ""));
             }
              $data = $fee->orderBy('PROGRAMME')->orderBy('YEAR')->paginate(100);
              $data->setPath(url("fee_summary"));
              $programm=$sys->getProgramByID($request->input('program'));
              $yearr=$request->input('year');
              $request->flashExcept("_token");
               foreach ($data as $key => $row) {
                   $total[]=$row->AMOUNT;
                    $data[$key]->TOTALS =  array_sum($total);
            
                }
              return view('finance.fees.fee_summary')->with('data',$data)
                                 ->with('program', $sys->getProgramByIDList())
                                 ->with('year', $this->years())
                                 ->with('academicYear', $yearr)
                                 ->with('programme', $programm)
                                 ->with('level', $request->input("level", "")); 
                                  

        }
    }
    /*
     * this controller method handles everything about students 
     * who are owing and those who have paid
     */
    public function owingAndPaid(Request $request) {
         $student= StudentModel::query() ;
         if ($request->has('search') && trim($request->input('search')) != "") {
            // dd($request);
            $student->where($request->input('by'), "LIKE", "%" . $request->input("search", "") . "%");
        }
        if ($request->has('program') && trim($request->input('program')) != "") {
            $student->where("PROGRAMMECODE", $request->input("program", ""));
        }
        if ($request->has('level') && trim($request->input('level')) != "") {
            $student->where("YEAR", $request->input("level", ""));
        }
        if ($request->has('season') && trim($request->input('season')) != "") {
            $student->where("TYPE", "=", $request->input("season", ""));
        }
         if ($request->has('indexno') && trim($request->input('indexno')) != "") {
            $student->where("INDEXNO", "=", $request->input("indexno", ""));
        }
        if ($request->has('type') && trim($request->input('type')) == "owing") {
            $student->where("BILL_OWING", ">", "0");
        }
        if ($request->has('filter') && trim($request->input('filter')) != "" && $request->input('amount') != "") {
            $filter = $request->input('filter');
            $amount = $request->input('amount');
            if ($filter == '=') {
                $student->where("BILL_OWING", $amount);
            }
            else{
                 $student->where("BILL_OWING", "$filter", $amount);
                // dd($request);
            }
        }
        $sys = new SystemController();
        $data = $student->paginate(100);
        $data->setPath(url("owing_paid"));
        $request->flashExcept("_token");
         foreach ($data as $key => $row) {
                   $total[]=$row->BILL_OWING;
                    $data[$key]->TOTALS =  array_sum($total);
                  $data[$key]->TOTALS =  @$sys->formatMoney($data[$key]->TOTALS);
                }
         \Session::put('students', $data);
        return view('finance.fees.owing')->with("data", $data)
                        ->with('program', $sys->getProgramList());
                
    }
     public function sendFeeSMS(Request $request){
         $message=$request->input("message", "");
        $query=\Session::get('students');
        $sms= new SystemController();
         \DB::beginTransaction();
          try {
                 
        foreach($query as $rtmt=> $member) {
           
             
             if ($sms->firesms($message,$member->TELEPHONENO,$member->INDEXNO)) {

                \Session::forget('students');
                 return redirect('/owing_paid')->with('success',array('Message sent to students succesfully'));
         
            } else {
                return redirect('/owing_paid')->withErrors("SMS could not be sent.. please verify if you have sms data and internet access.");
            }
        }
        } catch (\Exception $e) {
                \DB::rollback();
                
            }
    }
    public function new_receiptno(){
        $receiptno_query = Models\Receiptno::first();
		$receiptno_query->increment("receiptno", 1);
        $receiptno = str_pad($receiptno_query->receiptno, 12, "0", STR_PAD_LEFT);
		
        return $receiptno;
        
    }
    
    public function pad_receiptno($receiptno){
       return str_pad($receiptno, 12, "0", STR_PAD_LEFT);
       }
    /**
     * Display a list of proposed fees.
     *
     * @param  Request  $request
     * @return Response
     */
     public function getIndex(Request $request)
    {
        $sys=new SystemController();
              $array=$sys->getSemYear();
              $sem=$array[0]->SEMESTER;
              $year=$array[0]->YEAR;
        $fee = FeeModel::query();
         
        if ($request->has('level') && trim($request->input('level')) != "") {
            $fee->where("LEVEL", $request->input("level", ""));
        }
       
        if ($request->has('year') && trim($request->input('year')) != "") {
            $fee->where("YEAR", "=", $request->input("year", ""));
        }
        
        if ($request->has('program') && trim($request->input('program'))) {
            $fee->where("PROGRAMME", "=", $request->input('program'));
        }
         
        
        
        $data = $fee->orderBy('LEVEL', 'ASC')->paginate(100);
         
        $request->flashExcept("_token");
         
          foreach ($data as $key => $row) {
                   $total[]=$row->AMOUNT;
                    $data[$key]->TOTALS =  array_sum($total);
                     $data[$key]->TOTALSTUDENTS=@$sys->getTotalFeeByProrammeLevel($row->PROGRAMME, $row->LEVEL);
                    $data[$key]->TOTALAMOUNT=@$sys->getTotalFeeByProrammeLevel($row->PROGRAMME, $row->LEVEL)*$row->AMOUNT;
                    $total_proposed[]= $data[$key]->TOTALAMOUNT;
                  $data[$key]->TotalProposed=  array_sum($total_proposed);
                    }
                    
            $totalProposed=@$sys->formatMoney($data[$key]->TotalProposed);
        return view('finance.fees.index')->with("data", $data)
                        ->with('program', $sys->getProgramByIDList())
                        ->with('year',$this->years())
                          ->with('bank',$this->banks())
                          ->with('totalProposed',$totalProposed);
        
    }
     public function anyData(Request $request)
    {
         
          
        $fees = FeeModel::join('tpoly_programme', 'tpoly_fees.PROGRAMME', '=', 'tpoly_programme.ID')
           ->select(['tpoly_fees.ID', 'tpoly_fees.NAME','tpoly_fees.DESCRIPTION', 'tpoly_fees.AMOUNT','tpoly_fees.FEE_TYPE','tpoly_fees.SEASON_TYPE','tpoly_programme.PROGRAMME','tpoly_fees.LEVEL','tpoly_fees.SEMESTER','tpoly_fees.YEAR','tpoly_fees.STATUS','tpoly_fees.NATIONALITY']);
         


        return Datatables::of($fees)
                      
            ->addColumn('action', function ($fee) {
                if($fee->STATUS=='approved'){
                    return "<span class='uk-text-success'>Approved ready</span>";
                      } 
                else{
                    return  
                           \Form::open(['action' => ['FeeController@destroy', 'id'=>$fee->ID], 'method' => 'DELETE','name'=>'myform' ,'style' => 'display: inline;'])  
             
                   ." <button type=\"button\" class=\"md-btn  md-btn-danger md-btn-small   md-btn-wave-light waves-effect waves-button waves-light\" onclick=\"UIkit.modal.confirm('Are you sure you want to delete this fee?', function(){ document.forms[0].submit(); });\"><i  class=\"sidebar-menu-icon material-icons md-18\">delete</i></button>
                         <input type='hidden' name='fee' value='$fee->ID'/>  
                      ". \Form::close()."

                    <button title='click to approve fees' type=\"button\" class=\"md-btn  md-btn-primary md-btn-small   md-btn-wave-light waves-effect waves-button waves-light\" onclick=\"UIkit.modal.confirm('Are you sure you want to bill student with this fee item?', function(){   return window.location.href='run_bill/$fee->ID/id'     ; });\"><i  class=\"sidebar-menu-icon material-icons md-18\">done</i></button> 
                       
                   ";
                    
                          
                }
                            
                                         
            })
               ->editColumn('id', '{!! $ID!!}')
            
              
            
            ->setRowId('id')
            ->setRowClass(function ($fee) {
               // return $fee->ID % 2 == 0 ? 'uk-text-success uk-text-bold' : 'uk-text-warning uk-text-bold';
            })
            ->setRowData([
                'id' => 'test',
            ])
            ->setRowAttr([
                'color' => 'red',
            ])
                  
            ->make(true);
             
            //flash the request so it can still be available to the view or search form and the search parameters shown on the form 
      //$request->flash();
    }
    
    // approve bill here
    public function approve(Request $request,$id){
         
                $sys = new SystemController();
        $array = $sys->getSemYear();
        $sem1 = $array[0]->SEMESTER;
        $year1 = $array[0]->YEAR;
        /*
         * make sure only bills for the current semester are charged againts
         * students 
         * get current semester and year
         */

        //get the current user in session
        $user = \Auth::user()->id;
        //  dd($user);
        //get the bill item 
        $query = FeeModel::where('ID', $id)->get()->toArray();

        $programme = $sys->getProgramCodeByID($query[0]['PROGRAMME']);

        $amount = $query[0]['AMOUNT'];
        $level = $query[0]['LEVEL'];
        $year = $query[0]['YEAR'];
        $name = $query[0]['NAME'];
        // if the fee is actually for the current academic year
        if ($year1 == $year) {
            \DB::beginTransaction();
            try {

                // get students details
                $balance = StudentModel::where("PROGRAMMECODE", $programme)->where('YEAR', $level)->where('STATUS', '=', 'In school')->limit(1)->get();

                $bill = @$balance[0]->BILLS + $amount;
                $billOwing = @$balance[0]->BILL_OWING + $amount;

                $sql = StudentModel::where("PROGRAMMECODE", $programme)->where('YEAR', $level)->where('STATUS', '=', 'In school')->update(array("BILLS" => "", 'BILL_OWING' => $billOwing));

                if (!$sql) {

                    return redirect("/view_fees")->with("error", array("Error in billing:<span style='font-weight:bold;font-size:13px;'> $name with amount GHC$amount for level $level $programme $year  academic year could not be applied!</span>"));
                } else {
                    $sql = FeeModel::where("ID", $id)->update(array("APPROVED_BY" => $user, 'STATUS' => 'approved'));

                    if ($sql) {
                         \DB::commit();
                        return redirect("/view_fees")->with("success", array("Following bill:<span style='font-weight:bold;font-size:13px;'>  $name with amount GHC$amount for level $level $programme $year  academic year successfully applied!</span> "));
                    }
                }
            } catch (\Exception $e) {
                \DB::rollback();
            }
        } else {
            return redirect("/view_fees")->with("error", array("Error in billing:<span style='font-weight:bold;font-size:13px;'> $name with amount GHC$amount for level $level $programme $year  is not meant for the current academic year <br/> and therefore could not be applied!</span>"));
        }
    }
    public function showPayform(){
         return view('finance.fees.payfee');
    }
    public function showStudent(Request $request)
    {
        $student=  explode(',',$request->input('q'));
        $student=$student[0];
        
        $sql= StudentModel::where("INDEXNO",$student)->get();
        //dd($sql);
         if(count($sql)==0){
      
          return redirect("/pay_fees")->with("error","<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
          }
          else{
              $sys=new SystemController();
              $array=$sys->getSemYear();
              $sem=$array[0]->SEMESTER;
              $year=$array[0]->YEAR;
               return view("finance.fees.processPayment")->with( 'data',$sql)->with('year',$year)->with('sem',$sem)->with('banks', $this->banks())->with('receipt', $this->getReceipt());
      
          }
    }
    // this handle late fee payment ie penalty
    public function showStudentPenalty(Request $request)
    {
        $student=  explode(',',$request->input('q'));
        $student=$student[0];
        
        $sql= StudentModel::where("INDEXNO",$student)->get();
        //dd($sql);
         if(count($sql)==0){
      
          return redirect("/pay_fees")->with("error","<span style='font-weight:bold;font-size:13px;'> $request->input('q') does not exist!</span>");
          }
          else{
              $sys=new SystemController();
              $array=$sys->getSemYear();
              $sem=$array[0]->SEMESTER;
              $year=$array[0]->YEAR;
               return view("finance.fees.process_penalty")->with( 'data',$sql)->with('year',$year)->with('sem',$sem)->with('banks', $this->banks())->with('receipt', $this->getReceipt());
      
          }
    }
    public function processPayment(Request $request){
        
        $sys=new SystemController();
        $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $phone = $request->input('phone');
        $user = \Auth::user()->id;
        $feetype = "School Fees";
        if ($request->has('type') && $request->input('type') == "Late Registration Penalty") {
            \DB::beginTransaction();
            try {
                \Session::put('type', 'late');

                $amount = $request->input('amount');
                $receipt = $request->input('receipt');
                 
                $indexno = $request->input('student');
                
                $program = $request->input('programme');
                $level = $request->input('level');
                $feeLedger = new FeePaymentModel();
                $feeLedger->INDEXNO = $indexno;
                $feeLedger->PROGRAMME = $program;
                $feeLedger->AMOUNT = $amount;
                $feeLedger->PAYMENTTYPE = "Late Registration Fee";

                $feeLedger->PROGRAMME = $program;
                $feeLedger->LEVEL = $level;
                $feeLedger->RECIEPIENT = $user;

                $feeLedger->RECEIPTNO = $receipt;
                $feeLedger->YEAR = $year;
                $feeLedger->FEE_TYPE = "Late Registration Fee";
                $feeLedger->SEMESTER = $sem;
                if ($feeLedger->save()) {
                    \DB::commit();

                    $message = "Hi $indexno you have just paid GHC$amount as late registration fee";
                    if ($sys->firesms($message, $phone, $indexno)) {
                        
                    }
                    $this->updateReceipt();
                    $url = url("printreceiptLate/" . trim($receipt));
                    $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                    $request->session()->flash("success", "Payment successfully   $print_window");
                    return redirect("/pay_fees");
                }
            } catch (\Exception $e) {
                \DB::rollback();
                redirect()->back()->with('error', 'Error processing payment');
            }
        } else {
            \DB::beginTransaction();
            try {
                $amount = $request->input('amount');
                $receipt = $request->input('receipt');
                $indexno = $request->input('student');
                $owing = $request->input('bill') - $amount;
                $program = $request->input('programme');
                $level = $request->input('level');
                $bank = $request->input('bank');
 
                $bank_date = $request->input('bank_date');

                $details = $request->input('payment_detail');

                $transactionID = $request->input('transaction');
                if ($owing > $amount) {
                    $paymenttype = "Part payment";
                } else {
                    $paymenttype = "Full payment";
                }



                $feeLedger = new FeePaymentModel();
                $feeLedger->INDEXNO = $indexno;
                $feeLedger->PROGRAMME = $program;
                $feeLedger->AMOUNT = $amount;
                $feeLedger->PAYMENTTYPE = $paymenttype;
                $feeLedger->PAYMENTDETAILS = $details;
                $feeLedger->BANK_DATE = $bank_date;
                $feeLedger->PROGRAMME = $program;
                $feeLedger->LEVEL = $level;
                $feeLedger->RECIEPIENT = $user;
                $feeLedger->BANK = $bank;
                $feeLedger->TRANSACTION_ID = $transactionID;
                $feeLedger->RECEIPTNO = $receipt;
                $feeLedger->YEAR = $year;
                $feeLedger->FEE_TYPE = $feetype;
                $feeLedger->SEMESTER = $sem;

                if ($feeLedger->save()) {
                    $this->updateReceipt();
                    $newyear = substr($level, 0, 1);


                    $balance = StudentModel::where("INDEXNO", $indexno)->where('STATUS', '=', 'In school')->get();


                    $billOwing = @$balance[0]->BILL_OWING - $amount;

                    StudentModel::where('INDEXNO', $indexno)->update(array('BILL_OWING' => $billOwing, 'TELEPHONENO' => $phone, 'LEVEL' => $level, 'YEAR' => $newyear));
                    $message = "Hi $indexno you have just paid GHC$amount as $feetype remaining GHC$owing";
                    \DB::commit();
                    if ($sys->firesms($message, $phone, $indexno)) {
                        
                    }

                    $url = url("printreceipt/" . trim($receipt));
                    $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                    $request->session()->flash("success", "Payment successfully   $print_window");
                    return redirect("/pay_fees");
                }
            } catch (\Exception $e) {
                \DB::rollback();
                redirect()->back()->with('error', 'Error processing payment');
            }
        }
    }
     public function banks() {

        $banks = \DB::table('tpoly_banks')
                ->lists('NAME', 'ID');
        return $banks;
    }
    public function programmes() {

        $program = \DB::table('tpoly_programme')->get();
                
         foreach($program as $p=>$value){
             $programs[]=$value->PROGRAMMECODE;
         }
         return $programs;
    }
    public function getReceipt(){
         \DB::beginTransaction();
        try {
        $receiptno_query =  ReceiptModel::first();
		$receiptno =date('Y').str_pad($receiptno_query->no, 5, "0", STR_PAD_LEFT);
                \DB::commit();
                return $receiptno;
	} catch (\Exception $e) {
            \DB::rollback();
        }
    }
    public function updateReceipt(){
        \DB::beginTransaction();
        try {
            $query = ReceiptModel::first();

            $result= $query->increment("no");
            if($result){
                 \DB::commit();
            }
           
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }
    public function printreceiptLate(Request $request, $receiptno) {

		// $this->show_query();

		$transaction = FeePaymentModel::where("RECEIPTNO", $receiptno)->with("student", "bank"
                )->first();
        
        if (empty($transaction)) {
            abort(434, "No Fee payment   with this receipt <span class='uk-text-bold uk-text-large'>{{$receiptno}}</span>");
        }

        $words= $this->convert($transaction->AMOUNT);

              
               return view("finance.fees.late_receipt")->with("transaction", $transaction)->with('words',$words);
     
         
        
        }
        public function processReceipt(Request $request) {
            $receipt= $request->input('q');
             
            $url = url("printreceipt/" . trim($receipt));
                    $print_window = "<script >window.open('$url','','location=1,status=1,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=500')</script>";
                    $request->session()->flash("success", "Receipt printed out successfully   $print_window");
                    return redirect("/dashboard");
        
        }
        public function printreceipt(Request $request, $receiptno) {

		// $this->show_query();
                if(@\Auth::user()->level=='100H' || @\Auth::user()->level=='100BTT' || @\Auth::user()->level=='100BT'){
             
            $data=  StudentModel::where("STNO",@\Auth::user()->username)->first();
                     
                    
                }
                else{
                     $data=  StudentModel::where("INDEXNO",@\Auth::user()->username)->first();
                    
                   
                }
		$transaction = FeePaymentModel::where("RECEIPTNO", $receiptno)->with("student", "bank"
                )->first();
        
        if (empty($transaction)) {
            abort(434, "No Fee payment   with this receipt <span class='uk-text-bold uk-text-large'>{{$receiptno}}</span>");
        }

        $words= $this->convert($transaction->AMOUNT);

         
      

        return view("finance.fees.receipt")
                ->with("student",$data)
                ->with("transaction", $transaction)->with('words',$words);
        
        
        }
    public function showUpload() {
         return view("finance.fees.upload");
    }
    public function showReceiptForm(SystemController $sys) {
         $array = $sys->getSemYear();
        $sem = $array[0]->SEMESTER;
        $year = $array[0]->YEAR;
        $data=FeePaymentModel::where("INDEXNO",@\Auth::user()->username)
                ->where("SEMESTER",$sem)->where("YEAR",$year)
                ->get();
        return view("finance.fees.printReceipt")->with("data",$data);
    }
    public function storeUpload(Request $request) {
        //get the current user in session
        \DB::beginTransaction();
        try {

            $user = \Auth::user()->id;
            $valid_exts = array('csv'); // valid extensions
            $file = $request->file('file');
            $name = time() . '-' . $file->getClientOriginalName();
            if (!empty($file)) {

                $ext = strtolower($file->getClientOriginalExtension());
                $destination = public_path() . '\uploads\fees';
                if (in_array($ext, $valid_exts)) {
                    // Moves file to folder on server
                    // $file->move($destination, $name);
                    if (@$file->move($destination, $name)) {



                        $handle = fopen($destination . "/" . $name, "r");
                        //  print_r($handle);
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                            $num = count($data);

                            for ($c = 0; $c < $num; $c++) {
                                $col[$c] = $data[$c];
                            }


                            $name = $col[0];
                            $description = $col[1];
                            $amount = $col[2];
                            $type = $col[3];
                            $season = $col[4];
                            $programme = $col[5];
                            $level = $col[6];
                            $sem = $col[7];
                            $year = $col[8];
                            $nationality = $col[9];
                            $programs = $this->programmes(); // check if the programmes in the file tally wat is in the db
                            if (array_search($programme, $programs)) {


                                $fee = new FeeModel();
                                $fee->NAME = $name;

                                $fee->DESCRIPTION = $description;
                                $fee->AMOUNT = $amount;
                                $fee->FEE_TYPE = $type;
                                $fee->SEASON_TYPE = $season;
                                $fee->PROGRAMME = $programme;
                                $fee->LEVEL = $level;
                                $fee->SEMESTER = $sem;
                                $fee->YEAR = $year;
                                $fee->NATIONALITY = $nationality;
                                $fee->CREATED_BY = $user;
                                if ($fee->save()) {
                                    \DB::commit();
                                    return redirect('/view_fees')->with("success", array(" <span style='font-weight:bold;font-size:13px;'>Fees  successfully uploaded!</span> "));
                                } else {
                                    return redirect('/view_fees')->back()->withErrors("Fee could not be uploaded");
                                }
                            } else {
                                echo "<script>alert('Please your files contain programme(s) that are not in the system')</script>";
                            }
                        }
                        fclose($handle);
                    }
                } else {
                    echo "<script>alert('Please upload only csv files')</script>";
                }
            } else {
                echo "<script>alert('Please upload a csv file')</script>";
            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }
    public function convert_number($number) {

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

	public function convert($amt) {
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
         public function countries() {

                 $country=['Ghanaian'=>'Ghanaian','Foriegn'=>'Foriegn'];
                 return $country;
            }

        public function createform(){
           $program = \DB::table('tpoly_programme')
                ->lists('PROGRAMME', 'ID');
         return view('finance.fees.create')->with('program',$program)->with('year',$this->years())->with('country', $this->countries());
        
        }
        
        public function years() {
            
             for ($i = 2008; $i <= 2030; $i++) {
             $year = $i - 1 . "/" . $i;
             $years[$year]= $year;
             }
             return $years;
        }
        public function store(Request $request) {
            \DB::beginTransaction();
        try {
            $user = \Auth::user()->id;
            $this->validate($request, ['name' => 'required', 'amount' => 'required', 'programme' => 'required', 'level' => 'required', 'year' => 'required', 'stype' => 'required']);
            if ($request->input('programme') == 'All' && $request->input('level') == 'All') {
                $program = \DB::table('tpoly_programme')->get();

                // dd($size)   ;          

                foreach ($program as $programs) {
                    $fee = new FeeModel();
                    $fee->NAME = $request->input('name');

                    $fee->DESCRIPTION = $request->input('description');
                    $fee->AMOUNT = $request->input('amount');
                    $fee->FEE_TYPE = $request->input('type');
                    $fee->SEASON_TYPE = $request->input('stype');
                    $fee->PROGRAMME = $programs->ID;
                    $fee->LEVEL = $request->input('level');
                    $fee->SEMESTER = $request->input('semester');
                    $fee->YEAR = $request->input('year');
                    $fee->NATIONALITY = $request->input('country');
                    $name = $request->input('name');
                    $fee->CREATED_BY = $user;
                    $fee->save();
                }
            } elseif ($request->input('programme') == 'All') {
                $program = \DB::table('tpoly_programme')->get();
                foreach ($program as $programs) {
                    $fee = new FeeModel();
                    $fee->NAME = $request->input('name');

                    $fee->DESCRIPTION = $request->input('description');
                    $fee->AMOUNT = $request->input('amount');
                    $fee->FEE_TYPE = $request->input('type');
                    $fee->SEASON_TYPE = $request->input('stype');
                    $fee->PROGRAMME = $programs->ID;
                    $fee->LEVEL = $request->input('level');
                    $fee->SEMESTER = $request->input('semester');
                    $fee->YEAR = $request->input('year');
                    $fee->NATIONALITY = $request->input('country');
                    $name = $request->input('name');
                    $fee->CREATED_BY = $user;
                    $fee->save();
                }
            }

            $fee = new FeeModel();
            $fee->NAME = $request->input('name');

            $fee->DESCRIPTION = $request->input('description');
            $fee->AMOUNT = $request->input('amount');
            $fee->FEE_TYPE = $request->input('type');
            $fee->SEASON_TYPE = $request->input('stype');
            $fee->PROGRAMME = $request->input('programme');
            $fee->LEVEL = $request->input('level');
            $fee->SEMESTER = $request->input('semester');
            $fee->YEAR = $request->input('year');
            $fee->NATIONALITY = $request->input('country');
            $name = $request->input('name');
            $fee->CREATED_BY = $user;

            if ($fee->save()) {
                \DB::commit();
                return redirect()->back()->with("success", array(" <span style='font-weight:bold;font-size:13px;'> $name fee  successfully added!</span> "));
            } else {
                return redirect()->back()->withErrors("Fee could not be added");
            }
        } catch (\Exception $e) {
            \DB::rollback();
        }
    }

    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request)
    {
         
         $query = FeeModel::where('ID',$request->input("id"))->delete();
         
         if ($query) {
             \Session::flash("success", "<span style='font-weight:bold;font-size:13px;'> Fee  </span>successfully deleted!");

             return redirect()->route("view_fees");
        }
    }
}
