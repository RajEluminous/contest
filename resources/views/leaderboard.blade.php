@extends('layouts.master')

@section('content')
<header class="text-center p-3">
    <img src="{{ asset('img/limitless-factor-logo.png') }}">
</header>
@if (count($contest_show)>0)
<section class="hero">
    @php
      $i = 0;
    @endphp
    @foreach ($arrPrizeData as $keyPD => $valPD)
    @php
    $arr_prizes = "";
    if(isset($valPD['contest_type'])){

        if($valPD['contest_type']=='BOTH'){
            $arr_prizes = array_merge($valPD['SALE'],$valPD['LEAD']);
            $array1 = $valPD['LEAD'];
            $array2 = $valPD['SALE'];
        } else {
            $arr_prizes = $valPD[$valPD['contest_type']];
            list($array1, $array2) = array_chunk($arr_prizes, ceil(count($arr_prizes) / 2));
        }
    }
    @endphp
    <div class="container clsprize @if($i>0) d-none @endif" id="div_{{ $keyPD }}">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <h1 id="contest_name{{$i}}">Sales Contest</h1>
            </div>
        </div>

        <div class="row justify-content-center">
            @if (isset($valPD['contest_type']) && count($arr_prizes)>0)
                @if (isset($valPD['column']) && $valPD['column']==1)
                <div class="col-12 col-md-6 text-center">
                    <div class="text-center">
                        <h3 class="mb-4 mb-md-12">{{ $valPD['column_label_1'] }}</h3>
                        @foreach ($arr_prizes as $keyTop => $valTop)
                        <div class="d-flex align-items-center justify-content-center">
                            <p class="mb-0">{{ $valTop['short_desc'] }}</p>
                            <div class="sales-line"></div>
                            <p class="sales-prize mb-0">
                                <span>$</span>{{ number_format((float)$valTop['amount'], 0, '', ',') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @if (isset($valPD['column']) && $valPD['column']==2)
                    @if(empty($valPD['column_label_2']))
                    <h3 class="col-12 mb-md-12 text-center">{{ $valPD['column_label_1'] }}</h3>
                        <div class="col-6 col-md-6 text-center">
                            <div class="text-center">

                                @foreach ($array1 as $keyTop => $valTop)
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="mb-0">{{ $valTop['short_desc'] }}</p>
                                    <div class="sales-line"></div>
                                    <p class="sales-prize mb-0">
                                        <span>$</span>{{ number_format((float)$valTop['amount'], 0, '', ',') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-6 col-md-6 text-center">
                            <div class="text-center">
                                @foreach ($array2 as $keyTop => $valTop)
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="mb-0">{{ $valTop['short_desc'] }}</p>
                                    <div class="sales-line"></div>
                                    <p class="sales-prize mb-0">
                                        <span>$</span>{{ number_format((float)$valTop['amount'], 0, '', ',') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-6 col-md-6 text-center">
                            <div class="text-center">
                                <h3 class="mb-4 mb-md-12">{{ $valPD['column_label_1'] }}</h3>
                                @foreach ($array1 as $keyTop => $valTop)
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="mb-0">{{ $valTop['short_desc'] }}</p>
                                    <div class="sales-line"></div>
                                    <p class="sales-prize mb-0">
                                        <span>$</span>{{ number_format((float)$valTop['amount'], 0, '', ',') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-6 col-md-6 text-center">
                            <div class="text-center">
                                <h3 class="mb-4 mb-md-12">{{ $valPD['column_label_2'] }}</h3>
                                @foreach ($array2 as $keyTop => $valTop)
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="mb-0">{{ $valTop['short_desc'] }}</p>
                                    <div class="sales-line"></div>
                                    <p class="sales-prize mb-0">
                                        <span>$</span>{{ number_format((float)$valTop['amount'], 0, '', ',') }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endif
        </div>
        <div class="row justify-content-center mb-0 mt-5 mb-md-5">
            <div class="col-11 col-md-6 text-center">
                <div class="text-center mb-3">
                    @if (isset($valPD['aff_tools_link']))
                    <a href="{{ $valPD['aff_tools_link'] }}" class="btn btn-primary w-100" target="_blank">Grab Your Swipes Here</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @php
        $i++;
    @endphp
    @endforeach
</section>

<section class="bg-contest">
    <div class=" container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <div role="tabpanel">
                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        @foreach ($contest_show as $nKey => $item)
                          <li role="presentation" class="">
                            <a class="nav-item nav-link @if($nKey==0) active @endif" id="tbcontest{{$nKey}}" href="#home{{ $item->id }}" onclick="showPrize({{ $item->id }}, {{$nKey}})" aria-controls="home" role="tab" data-toggle="tab">{{ $item->name }}</a>
                          </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                     @foreach ($contest_show as $cKey => $item)
                          <div role="tabpanel" class="tab-pane  @if($cKey==0) active @endif" id="home{{ $item->id }}" class="active">
                            <input type="hidden" id="actionaftercontestexpire{{$cKey}}" value="{{ $item->action_after_countdown_expire }}">
                            <input type="hidden" id="actionaftercontestexpirevalue{{$cKey}}" value="{{ $item->action_after_countdown_expire_value }}">
                            <input type="hidden" id="expiredcontestid{{$cKey}}" value="{{ $item->id }}">
                            <input type="hidden" id="contestname{{ $cKey }}" value="{{ $item->name }}">
                            <input type="hidden" id="contest_status{{ $cKey }}" value="{{ $item->status }}">

                            <div class="row justify-content-center mb-0 mt-5 mb-md-5 @if($item->is_display_counter_timer==0) d-none @endif" id="divClock{{$cKey}}">
                                <div class="col-11 col-md-6 text-center">
                                    @if($date_params[$cKey]['date_flag'] =='start')
                                    <div class="text-center">
                                        <h5 class="">Contest Starts on {{ $date_params[$cKey]['date_show_value'] }} (Eastern Time)</h5>
                                        <div id="clock-c{{$cKey}}-start" class="countdown py-4"></div>
                                    </div>
                                    @endif
                                    @if($date_params[$cKey]['date_flag'] =='end')
                                    <div class="text-center">
                                        <h5 class="">Contest Ends on {{ $date_params[$cKey]['date_show_value'] }} (Eastern Time)</h5>
                                        <div id="clock-c{{$cKey}}-end" class="countdown py-4"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <h3 class="my-3 d-none" id="divFinalMessage{{$cKey}}"></h3>
                            @php
                                $cnt = 1;
                            @endphp
                            <!-- tbl class -->
                            <div class="table-responsive mt-5">
                                <!-- Leads Records ------------------>
                                @if ($item->display_leads_view==1)
                                @if(count($arrLeadsData[$item->id])>0)
                                <h3 class="text-left">Top Leads</h3>

                                <table class="table table-striped">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th scope="col">Position</th>
                                            <th scope="col">Affiliate ID</th>
                                            @if ($item->display_leads_result==1)
                                            <th scope="col">No. of Count</th>
                                            @endif
                                            @if ($item->display_prizes==1)
                                            <th scope="col">Prize</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cnt = 1;
                                            $flag_showprize = false;
                                            $arr_prizes = [];

                                            if($arrPrizeData[$item->id]['contest_type']=='BOTH'){
                                                if(empty($arrPrizeData[$item->id]['column_label_2'])) {
                                                    $arr_prizes = array_merge($arrPrizeData[$item->id]['SALE'], $arrPrizeData[$item->id]['LEAD']);
                                                } else {
                                                    $arr_prizes = $arrPrizeData[$item->id]['LEAD'];
                                                 }
                                                $flag_showprize = true;
                                            } else {
                                                if(isset($arrPrizeData[$item->id]['LEAD']) && count($arrPrizeData[$item->id]['LEAD'])>0)
                                                {
                                                    $flag_showprize = true;
                                                    $arr_prizes = $arrPrizeData[$item->id]['LEAD'];
                                                }
                                            }

                                        @endphp
                                        @foreach ($arrLeadsData[$item->id] as $vKey => $vVal)
                                        @php

										if($cnt <= $item->contest_result_places) {
                                            $affiliateName = strtolower($vVal['name'] );
                                            if(isset($aff_partner_list[$affiliateName])){
                                                $affiliateName = $aff_partner_list[$affiliateName];
                                            }
                                            $affiliateName = strtoupper($affiliateName);
                                            $affiliate_profile_img = $vVal['affiliate_profile_img'];
                                        @endphp
                                            <tr>
                                                <td scope="row">{{$cnt}}</td>
                                                <td>
                                                    <div class="col-7 mx-auto">
                                                        <div>
                                                        @if ($item->display_affiliate_image==1)
                                                            <img src="{{ $affiliate_profile_img }}" class="rounded-circle float-left" width="66">
                                                        @endif
                                                        </div>
                                                        <div class="pt-3">
                                                            {{ $affiliateName }}
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($item->display_leads_result==1)
                                                <td> {{ $vVal['lead_count'] }}</td>
                                                @endif
                                                @if ($item->display_prizes==1)
                                                <td>
                                                    @if ($flag_showprize && isset($arr_prizes[$vKey]['prize_type']) && $arr_prizes[$vKey]['amount']>0)
                                                     {{ '$'.$arr_prizes[$vKey]['amount'] }}
                                                    @endif
                                                </td>
                                                @endif
                                            </tr>
                                        @php
										}
                                            $cnt++
                                        @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                                @endif
                                <!-- End: Leads Table ------------->
                                <!-- Sales Records -->
                                @if ($item->display_sales_view==1 && count($arrAffiliateData[$item->id])>0)
                                <table class="table table-striped">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th scope="col">Position</th>
                                            <th scope="col">Affiliate ID</th>
                                            @if ($item->display_sales_result==1)
                                            <th scope="col">{{ $arrContestTypeLabel[$item->contest_type] }}</th>
                                            @endif
                                            @if ($item->display_prizes==1)
                                            <th scope="col">Prize</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cnt = 1;
                                            $arr_prizes = [];

                                            if($arrPrizeData[$item->id]['contest_type']=='BOTH'){
                                                 if(empty($arrPrizeData[$item->id]['column_label_2'])) {
                                                    $arr_prizes = array_merge($arrPrizeData[$item->id]['SALE'], $arrPrizeData[$item->id]['LEAD']);
                                                 } else {
                                                    $arr_prizes = $arrPrizeData[$item->id]['SALE'];
                                                 }
                                                 $flag_showprize = true;
                                            } else {
                                                if(isset($arrPrizeData[$item->id]['SALE']) && count($arrPrizeData[$item->id]['SALE'])>0) {
                                                    $flag_showprize = true;
                                                    $arr_prizes = $arrPrizeData[$item->id]['SALE'];
                                                }
                                            }

                                        @endphp
                                        @foreach ($arrAffiliateData[$item->id] as $affKey => $affVal)
                                        @php
                                         if($cnt <= $item->contest_result_places) {
                                            $display_srevenue_scount = '';
                                            $sub_part = '';
                                            if($item->contest_type == 'top_revenue'){
                                                if($item->is_display_total_sale == 1){
                                                    $sub_part = ' ('.$affVal['sale_count'].' sales)';
                                                }
                                                $display_srevenue_scount = '$'.$affVal['sale_amount'].$sub_part;
                                            }

                                            if($item->contest_type == 'top_sales_count'){
                                                if($item->is_display_revenue == 1){
                                                    $sub_part= ' ($'.$affVal['sale_amount'].')';
                                                }
                                                $display_srevenue_scount = $affVal['sale_count'].' sales'.$sub_part;
                                            }
                                            $aff_partner_list;
                                            $affiliateName = strtolower($affVal['affiliate']);
                                            if(isset($aff_partner_list[$affiliateName])){
                                                $affiliateName = $aff_partner_list[$affiliateName];
                                            }
                                            $affiliateName = strtoupper($affiliateName);
                                            $affiliate_profile_img = $affVal['affiliate_profile_img'];

                                         @endphp
                                            <tr>
                                                <td scope="row">{{$cnt}}</td>
                                                <td>
                                                    <div class="col-7 mx-auto">
                                                        <div>
                                                        @if ($item->display_affiliate_image==1)
                                                            <img src="{{ $affiliate_profile_img }}" class="rounded-circle float-left" width="66">
                                                        @endif
                                                        </div>
                                                        <div class="pt-3">
                                                            {{ $affiliateName }}
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($item->display_sales_result==1)
                                                 <td> {{$display_srevenue_scount}}</td>
                                                @endif
                                                @if ($item->display_prizes==1)
                                                <td>
                                                    @if ($flag_showprize && isset($arr_prizes[$affKey]['prize_type']) && $arr_prizes[$affKey]['amount']>0)
                                                     {{ '$'.$arr_prizes[$affKey]['amount'] }}
                                                    @endif
                                                </td>
                                                @endif
                                            </tr>
                                        @php
                                        }
                                            $cnt++
                                        @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                                <!-- End: Sales Records -->

                            </div>
                            <!-- tbl class -->
                          </div>
                     @endforeach
                    </div>
                  </div>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md-7 text-center">
                <div class="alert alert-warning" role="alert">
                    <span class="fs-xs font-italic">*T&C: Please reach out to us within 30 days after the contest with your PayPal details to be eligible for the "Prizes for All" payout.</span>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section style="margin:0; padding:0; width:100vw; height:100vh; display:flex; justify-content:center; align-items:center; font-size:50pt;">
    <span>Coming soon</span>
</section>
@endif
@push('script')
<style>

</style>
<script src="{{ asset('vendors/moment/moment.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment-with-locales.min.js"></script>
<script src="//momentjs.com/downloads/moment-timezone-with-data.min.js"></script>

<script src="{{ asset('vendors/jquery.countdown/jquery.countdown.min.js') }}"></script>
<script>
    // Grab the timezone of client machine
    var clienttimezone = moment.tz.guess();

    //console.log(moment.tz.guess());
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $('#tbcontest0').trigger( "click" );
    // To Show hide prizes
    function showPrize(cid,nkey){
        $('.clsprize').addClass('d-none');
        $('#div_'+cid).removeClass('d-none');
        var contestname = '';
        contestname = $('#contestname'+nkey).val();
        //console.log(contestname);
        $('#contest_name'+nkey).text(contestname);
    }
   // console.log(clienttimezone);

    $(function () {

        @foreach ($contest_show as $cKey => $item)

            var dateValue{{$cKey}} = "{{ $date_params[$cKey]['date_value'] }}";
            var est = moment.tz("{{ $date_params[$cKey]['date_value'] }}", "America/New_York");

            // countdown for start
            $('#clock-c{{$cKey}}-start').countdown(est.toDate(), function(event) {
                var $this = $(this).html(event.strftime(''
                    + '<span class="h1 font-weight-bold">%D</span> Days'
                    + '<span class="h1 font-weight-bold">%H</span> Hours'
                    + '<span class="h1 font-weight-bold">%M</span> Minutes'
                    + '<span class="h1 font-weight-bold">%S</span> Seconds'));
                }).on('update.countdown', function(event) {
                    // If Event is going on
                    // actionAfterContestComplete({{$cKey}});

                }).on('finish.countdown', function(event) {
                    // If Event Expired
                    window.location.reload();
            });

            // countdown for end
            $('#clock-c{{$cKey}}-end').countdown(est.toDate(), function(event) {
                if (event.type === "finish") {
                    actionAfterContestComplete({{$cKey}});
                }

                var $this = $(this).html(event.strftime(''
                    + '<span class="h1 font-weight-bold">%D</span> Days'
                    + '<span class="h1 font-weight-bold">%H</span> Hours'
                    + '<span class="h1 font-weight-bold">%M</span> Minutes'
                    + '<span class="h1 font-weight-bold">%S</span> Seconds'));
                }).on('update.countdown', function(event) {
                    // If Event is going on
                    //
                    contest_status = $('#contest_status'+{{$cKey}}).val();
                    if(contest_status=='ENDED') {
                        actionAfterContestComplete({{$cKey}});
                    }
                }).on('finish.countdown', function(event) {
                    // If Event Expired
                    actionAfterContestComplete({{$cKey}});
            });

        @endforeach

    });

    // Processing of data after contest completion
    function actionAfterContestComplete(cKey){

        actionaftercontestexpire = $('#actionaftercontestexpire'+cKey).val();
        actionaftercontestexpirevalue = $('#actionaftercontestexpirevalue'+cKey).val();

        if(actionaftercontestexpire=='redirect'){

        }
        else if(actionaftercontestexpire=='display_text'){
            $('#divFinalMessage'+cKey).html(actionaftercontestexpirevalue);
            $('#divFinalMessage'+cKey).removeClass('d-none');
            $('#divClock'+cKey).addClass('d-none');
        }
        else {

        }
        updateContestStatus(cKey);
    }

    function updateContestStatus(cKey) {
        expiredcontestid = $('#expiredcontestid'+cKey).val();

        $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': CSRF_TOKEN
                  }
              });

        $.ajax({
            url: '{{ route("leaderboard.ajaxupdateconteststatus") }}',
            type:'post',
            data:{"_token": "{{ csrf_token() }}",'expiredcontestid':expiredcontestid},
            success:function(){

            }
        })
    }

</script>
@endpush
@endsection
