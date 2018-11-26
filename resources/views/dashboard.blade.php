@extends('layouts.app')


@section('style')
    <style>
        body {
            background-color: #fafafa;
        }
    </style>
    <!-- additional styles for plugins -->
    <!-- weather icons -->
    <link rel="stylesheet" href="public/assets/plugins/weather-icons/css/weather-icons.min.css" media="all">
    <!-- metrics graphics (charts) -->
    <link rel="stylesheet" href="public/assets/plugins/metrics-graphics/dist/metricsgraphics.css">
    <!-- chartist -->
    <link rel="stylesheet" href="public/assets/plugins/chartist/dist/chartist.min.css">


@endsection
@section('content')

    @inject('sys', 'App\Http\Controllers\SystemController')


    <div class="md-card-content">
        @if(Session::has('success'))
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                {!! Session::get('success') !!}
            </div>
        @endif

        @if(Session::has('error'))
            <div style="text-align: center" class="uk-alert uk-alert-danger" data-uk-alert="">
                {!! Session::get('error') !!}
            </div>
        @endif


    </div>



    <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium uk-sortable ">
        <div>
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i
                                    class="sidebar-menu-icon material-icons md-36">access_time</i></span></div>
                    <div style="margin-top: -7px; margin-bottom: -5px">

                    <table>
                        <tbody>
                        <tr>
                            <td align="left"><span class="uk-text-muted uk-text-small">Last Visit</span></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td align="left"><span class="uk-text-muted uk-text-small">Level</span></td>
                        </tr>
                        <tr>
                            <td align="left"> <h3 class="uk-margin-remove"><span class="countUpMe">{{$lastVisit}}</h3></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td align="left"> <h3 class="uk-margin-remove"><span class="countUpMe">{{$level}}</h3></td>
                        </tr>
                        
                    </tbody>
                    </table>
                    </div>

                    
                </div>
            </div>
        </div>
        <div>
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i
                                    class="sidebar-menu-icon material-icons md-36">reorder</i></span></div>
                    <div style="margin-top: -7px; margin-bottom: -5px">
                    <table>
                        <tbody>
                        <tr>
                            <td align="left"><span class="uk-text-muted uk-text-small">Class</span></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td align="left"><span class="uk-text-muted uk-text-small">CGPA</span></td>
                        </tr>
                        <tr>
                            <td align="left"> <h3 class="uk-margin-remove"><span class="countUpMe">{{$class}}</h3></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td align="left"> <h3 class="uk-margin-remove"><span class="countUpMe">{{$cgpa}}</h3></td>
                        </tr>
                        
                    </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=" "><i
                                    class="sidebar-menu-icon material-icons md-36">account_balance</i></span></div>

                        <span class="uk-text-muted uk-text-small">Registered: </span><span class="uk-text-small {{$registerS}} uk-text-bold">{{$register1}}
                        </span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="uk-text-muted uk-text-small">Profile updated: </span><span class="uk-text-small {{$studentUpdateS}} uk-text-bold">{{$studentUpdate1}}
                        </span><br/>
                        <span class="uk-text-muted uk-text-small">Lecturer assesment: </span><span class="uk-text-small {{$assesS}} uk-text-bold">{{$asses1}}
                        </span>
                    
                </div>
            </div>
        </div>
        <div>
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i
                                    class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                    <div style="margin-top: -7px; margin-bottom: -5px">

                    <table>
                        <tbody>
                        <tr>
                            <td align="left"><span class="uk-text-muted uk-text-small">CH Done</span></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td align="left"><span class="uk-text-muted uk-text-small">STATUS</span></td>
                        </tr>
                        <tr>
                            <td align="left"> <h3 class="uk-margin-remove"><span class="countUpMe">{{$credit}}</h3></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td align="left"> <h3 class="uk-margin-remove"><span class="countUpMe">{{$status}}</h3></td>
                        </tr>
                        
                    </tbody>
                    </table>
                    </div>
                    
                                        
                </div>
            </div>
        </div>
        
    </div>





    <div class="uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-5 uk-text-center"
         data-uk-grid-margin>
        <div>
            
                <div class="md-card-content">
                     <table  class="uk-table">
                        <tbody>
                        <tr style="background-color: #e0e7f5;">
                            <td align="right"> Opening b/c: </td><td align="left"> GHc {{$balance}} </td>
                        </tr>
                        <tr>
                            <td align="right"> Fees ({{$year}}): </td><td align="left"> GHc {{$currentFees}} </td>
                        </tr>
                        <tr style="background-color: #e0e7f5;">
                            <td align="right"> Total bill: </td><td align="left"> GHc {{$bill}} </td>
                        </tr>
                        <tr>
                            <td align="right"> Paid: </td><td align="left"> GHc {{$paid}} </td>
                        </tr>
                        <tr style="background-color: #e0e7f5;">
                            <td align="right"> Outstanding b/c: </td><td align="left"> GHc {{$totalowe}} </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                
            
        </div>

        <div>
            <a href="{!!url('/result/transcript/provisonal')!!}">
            <div class="md-card md-card-hover md-card-overlay">
                <div class="md-card-content">
                     <img
                                src="{{url('public/dashboard/transcript.png')}}"/>
                </div>
                <div class="md-card-overlay-content">
                    <div class="uk-clearfix md-card-overlay-header">
                        <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                        <h3 class="uk-text-center uk-text-upper">
                            RESULT CHECKER
                        </h3>
                    </div>
                    click to view your provisional results here
                </div>
            </div>
            </a>
        </div>
        <div>
            <a href="{{url('/course_registration')}}">
            <div class="md-card md-card-hover md-card-overlay">
                <div class="md-card-content">
                     <img src="{{url('public/dashboard/uploadnotes.png')}}"/>
                </div>
                <div class="md-card-overlay-content uk-badge-success">
                    <div class="uk-clearfix md-card-overlay-header">
                        <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                        <h3 class="uk-text-center uk-text-upper uk-text-red">
                            COURSE REGISTRATION
                        </h3>
                    </div>
                    Click register this semester courses
                </div>
            </div>
            </a>
        </div>
        <?php

         $studentProgramme =  substr(Auth::user()->programme,0,1) ;

         ?>
        @if($studentProgramme=="H")
        <div>
            <a href="{{url('/liaison/form/attachment')}}">
            <div class="md-card md-card-hover md-card-overlay">
                <div class="md-card-content">
                     <img src="{{url('public/dashboard/results.png')}}"/>
                </div>
                <div class="md-card-overlay-content">
                    <div class="uk-clearfix md-card-overlay-header">
                        <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                        <h3 class="uk-text-center uk-text-upper">
                          ATTACHMENT LETTER
                        </h3>
                    </div>
                    <p>Click to print attachment form</p>
                    <a href="{{url('/liaison/form/attachment')}}" class="md-btn md-btn-primary">Click to go</a>
                </div>
            </div>
            </a>
        </div>
        @endif

        <div>
            <a href="{{url('/liaison/form/assumption')}}">
            <div class="md-card md-card-hover md-card-overlay">
                <div class="md-card-content">
                     <img src="{{url('public/dashboard/results.png')}}"/>
                </div>
                <div class="md-card-overlay-content">
                    <div class="uk-clearfix md-card-overlay-header">
                        <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                        <h3 class="uk-text-center uk-text-upper">
                            ASSUMPTION OF DUTY 
                        </h3>
                    </div>
                    <p>Click to print assumption of duty form</p>
                    <a href="{{url('/liaison/form/assumption')}}" class="md-btn md-btn-primary">Click to go</a>
                </div>
            </div>
            </a>
        </div>
        <?php

            $programmes=["HID","HIDE","HDT","HDTE","HCE","HCEE","BTH","BTSMGTS","BTIAG-ANIM","BTIAG-MULT","BTX - DP","BTT","BTST","BTBE","BTCE","BTEE","BTIAG-ADVT","BTIAG-ANIM","BTIAG-MULT","BTIAG-PRINT","BTX - FY","BTX - G","BTX - W","BTP","BTSMGTS"];
        $studentLevel =  substr(Auth::user()->level,0,1) ;
        $studentProgramme =  substr(Auth::user()->programme,0,1) ;

        if($studentLevel==1){
            $studentLevel="Year 1";
        }
        elseif($studentLevel==2){
            $studentLevel="Year 2";
        }
        elseif($studentLevel==3){
            $studentLevel="Year 3";
        }
        elseif($studentLevel==4){
            $studentLevel="Year 4";
        }
        ?>

        @if(in_array(Auth::user()->programme,$programmes)  && $studentLevel=="Year 2")
        <div>
            <a href="{{url('/liaison/form/semester/out/fill')}}">
                <div class="md-card md-card-hover md-card-overlay">
                    <div class="md-card-content">
                        <img src="{{url('public/dashboard/results.png')}}"/>
                    </div>
                    <div class="md-card-overlay-content">
                        <div class="uk-clearfix md-card-overlay-header">
                            <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                            <h3 class="uk-text-center uk-text-upper">
                                PRINT SEMESTER OUT LETTER
                            </h3>
                        </div>
                        <p>Click to print semester out letter</p>
                        <a href="{{url('/liaison/form/semester/out/fill')}}" class="md-btn md-btn-primary">Click to go</a>
                    </div>
                </div>
            </a>
        </div>

        @endif




 <div>
            <a href="{{url('/lecturer/assessment')}}">
            <div class="md-card md-card-hover md-card-overlay">
                <div class="md-card-content">
                     <img src="{{url('public/dashboard/results.png')}}"/>
                </div>
                <div class="md-card-overlay-content">
                    <div class="uk-clearfix md-card-overlay-header">
                        <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                        <h3 class="uk-text-center uk-text-upper">
                            LECTURER ASSESSMENT
                        </h3>
                    </div>
                    <p>Click to print lecturer assessment form</p>
                    <a href="{{url('/lecturer/assessment')}}" class="md-btn md-btn-primary">Click to go</a>
                </div>
            </div>
            </a>
        </div>



    </div>




    <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
        <div class="uk-width-medium-1-2">
            @if($register1==0)
                <div class="md-card">

                    <div class="md-card-toolbar">

                        <h3 class="md-card-toolbar-heading-text">
                            <div class="">Courses for {{$year }} Academic year {{$sem}}
                                semester <!--  click <a href='{{url("/course_registration")}}'> here to register</a> --></div>

                        </h3>
                    </div>

                    <div class="md-card-content">
                        <div class="uk-overflow-container">
                            <center><span class="uk-text-success uk-text-bold">{!! $course->count()!!} Records</span>
                            </center>
                            <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair"
                                   id="ts_pager_filter">
                                <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>COURSE</th>

                                    <th style="text-align:center">CREDIT</th>


                                    <th style="text-align:center">TYPE</th>
                                    <th style="text-align:center">LECTURER</th>

                                </tr>
                                </thead>
                                <tbody>
                        <?php $m=0;?>
                                @foreach($course as $courseindex=> $rows)

                        <?php $m++?>


                                    <tr align="">
                                        <td> {{  $m }} </td>
                                        <td> {{ @$rows->course->COURSE_NAME }}</td>

                                        <td> {{ @$rows->COURSE_CREDIT    }}</td>
                                        @if(@$rows->COURSE_TYPE=='Core')
                                            <td>
                                                <span class="uk-badge uk-badge-success"> {{ @$rows->COURSE_TYPE }}</span>
                                            </td>
                                        @else
                                            <td>
                                                <span class="uk-badge uk-badge-warning"> {{ @$rows->COURSE_TYPE }}</span>
                                            </td>
                                        @endif
                                        <td> {{ strtoupper(@$rows->lecturer->fullName) }}</td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
            @else
                <div class="md-card">

                    <div class="md-card-toolbar">

                        <h3 class="md-card-toolbar-heading-text">
                            <div class="">Registered Courses for {{$year }} Academic year {{$sem}} semester click <a
                                        href='{{url("/registeredCourses")}}'> here to print</a></div>

                        </h3>
                    </div>

                    <div class="md-card-content">
                        <div class="uk-overflow-container">
                            <center><span class="uk-text-success uk-text-bold">{!! $registerQuery->count()!!}
                                    Records</span></center>
                            <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair"
                                   id="ts_pager_filter">
                                <thead>
                                <tr>
                                    <th class="filter-false remove sorter-false">NO</th>
                                    <th>COURSE</th>
                                    <th style="text-align:center">CODE</th>

                                    <th style="text-align:center">CREDIT</th>


                                </tr>
                                </thead>
                                <tbody>
                                <?php $n = 0;?>

                                @foreach($registerQuery as $courseIndexs=> $row)




                                    <tr align="">
                                        <td> <?php $n++;echo $n;?> </td>
                                        <td> {{ strtoupper(@$row->courseMount->course->COURSE_NAME) }}</td>
                                        <td> {{ @$row->courseMount->course->COURSE_CODE  }}</td>

                                        <td> {{ @$row->credits   }}</td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="uk-width-medium-1-2">
            @if(count($resitQuery)>0)
                <div class="md-card">

                    <div class="md-card-toolbar">

                        <h3 class="md-card-toolbar-heading-text">
                            <div class="">Academic issues (resit)<!--  click <a href='{{url("/course_registration")}}'> here to register</a> --></div>

                        </h3>
                    </div>

                    <div class="md-card-content">
                        <div class="uk-overflow-container">
                            
                            <table border="0" class="uk-table uk-table-striped">
                                    <thead >
                                    <tr class="uk-text-bold" style="background-color:#1A337E;color:white;">
                                        <td>CODE</td>
                                        <td>COURSE</td>
                                        <td>GD</td>    
                                        <td>MK</td>

                                   
                                </tr>
                                </thead>
                                <tbody>
                        <?php $m=0;?>

                                @foreach($resitQuery as $rs)

                        <?php $m++?>


                                    <tr>
                                        <td><?php $object=$sys->getCourseByCodeProgramObject($rs['code'],$program); echo @$object[0]->COURSE_CODE; ?> 
                                        </td>
                                        <td><?php
                                            echo @$object[0]->COURSE_NAME;?></td>

                                        <td><?php if($rs['grade']){ echo @$rs['grade'];} else{echo "IC";}?></td>
                                        
                                        <td><?php if($rs['total']){ echo @$rs['total'];} else{echo "IC";}?></td>

                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
            @else
            <div class="md-card">
                <div class="md-card-content">
                    <h3 class="heading_a uk-margin-bottom">Progress Statistics</h3>
                    <div id="ct-chart" class="chartist"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <br/>


@endsection
@section('js')
    <!-- d3 -->
    <script src="public/assets/plugins/d3/d3.min.js"></script>
    <!-- metrics graphics (charts) -->
    <script src="public/assets/plugins/metrics-graphics/dist/metricsgraphics.min.js"></script>
    <!-- chartist (charts) -->
    <script src="public/assets/plugins/chartist/dist/chartist.min.js"></script>
    <!-- maplace (google maps) -->

    <!-- peity (small charts) -->
    <script src="public/assets/plugins/peity/jquery.peity.min.js"></script>
    <!-- easy-pie-chart (circular statistics) -->
    <script src="public/assets/plugins/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
    <!-- countUp -->
    <script src="public/assets/plugins/countUp.js/dist/countUp.min.js"></script>


    <!--  dashbord functions -->
    <script src="public/assets/js/pages/dashboard.min.js"></script>

@endsection
