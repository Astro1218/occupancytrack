@extends('layouts.app')

@section('additional_css')

@endsection

@section('contents')

@php
    use App\model\Reports;
@endphp

<div class="row py-5" style="margin-bottom: 50px;">
    <div class="col-md-3" style="padding-top: 10px;">
        <h3 class="landingtitle">Report Summary For</h3>
    </div>
    <div class="col-md-3" style="padding-top: 5px;">
        <select class="form-control selectpicker" id="view_community" data-size="7" data-live-search="true">
            <option value="">Select Community</option>

            @foreach ($community_data as $item)
                @if($item->id == $community_id)
                    <option value="{{ $item->id }}" selected="true">{{ $item->name }}</option>
                @else
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endif
            @endforeach
    </select>
    </div>
    <div class="col-md-1" style="padding-top: 10px;">
        <h3 class="landingtitle">From </h3>
    </div>
    <div class="col-md-3" style="padding-top: 5px;">
        <select class="form-control selectpicker" id="report_date" data-size="5" data-live-search="true">
            @foreach ($periodItems as $period)
                @if($period->id == $period_data->id)
                    <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                @else
                    <option value="{{ $period->id }}">{{ $period->caption }}</option>
                @endif
            @endforeach
        </select>

        {{-- <input type="button" class="span2 btn-rounded" id="report_date" data-id="{{ $period_data->id }}" data-start="{{ $period_data->starting }}" data-caption="{{ $period_data->caption }}" value="{{ $period_data->caption }}" /> --}}
    </div>
</div>

@if(empty($report_data))
    <div style="height: 520px;">
        <div class="alert alert-custom alert-notice alert-light-danger alert-dismissible fade show mb-5" role='alert'>
            <button type="button" class="close" data-dismiss="alert">×</button>
            No Report Data ! You can not see the data in this date and community. You must add the data in this date and community. &nbsp;&nbsp;&nbsp;
            <a style="color: blue; font-size: 16px; font-weight: 700;" href="{{ route('community_view_add', ['date' => $period_data->starting, 'community_id' => $community_id]) }}">Go to Add <span><i class="far fa-arrow-alt-circle-right"></i></span></a>
        </div>
    </div>
@else
    @if($user_data->company_id == 1)
        <div class="row reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Census</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">Building</h4></div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Census</h4>
                    </div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Capacity</h4>
                    </div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">(%)</h4>
                    </div>
                </div>
                @php
                    $total_census = 0;
                    $total_capacity = 0;
                @endphp

                @foreach($report_data->get_Cencaps as $item)
                    @php
                        $total_census += $item->census;
                        $total_capacity += $item->capacity;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-3 text-align-center">{{ $item->get_Building->name }}</div>
                        <div class="col-md-3 text-align-center">{{ $item->census }}</div>
                        <div class="col-md-3 text-align-center">{{ $item->capacity }}</div>
                        <div class="col-md-3 text-align-center">
                            @if($item->capacity != 0)
                            {{ number_format((100*($item->census/$item->capacity)), '2', '.', '')}}(%)
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="row mb-4">
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">Total</h4></div>
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">{{ $total_census }}</h4></div>
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">{{ $total_capacity }}</h4></div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">
                        @if($total_capacity != 0)
                        {{ number_format((100*($total_census/$total_capacity)), '2', '.', '')}}(%)
                        @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Move-Outs</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Type</h4>
                    </div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Count</h4>
                    </div>
                </div>
                @php
                    $total_count = 0;
                @endphp

                @foreach($report_data->get_Moveouts as $item)
                    @php
                        $total_count += $item->number;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-3 text-align-center">{{ $item->description }}</div>
                        <div class="col-md-3 text-align-center">{{ $item->number }}</div>
                    </div>
                @endforeach

                <div class="row mb-4">
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">Total</h4></div>
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">{{ $total_count }}</h4></div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Statistics</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Unqualified</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->unqualified}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Tour</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->tours}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Deposites</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->deposits}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Inquiries to Tours</div>
                    <div class="col-md-3 text-align-center">
                        @php
                            $total_inquiries = 0;
                        @endphp

                        @foreach($report_data->get_Inquiries as $item)
                            @php
                                $total_inquiries += $item->number;
                            @endphp
                        @endforeach

                        @if($report_data->tours != 0)

                        {{ number_format((($report_data->tours/$report_data->inquiry)*100), '2', '.', '')}}(%)
                        @endif
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Tours to Deposits</div>
                    <div class="col-md-3 text-align-center">
                        @if($report_data->deposits != 0)
                            @php
                                
                                
                            @endphp
                            @if($report_data->deposits > 0)
                                
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Move In/Out</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">WTD Move-Ins</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->wtd_movein}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">WTD Move-Outs</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->wtd_moveout}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">WTD Net Residents</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->wtd_movein - $report_data->wtd_moveout }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">YTD Move-Ins</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->ytd_movein}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">YTD Move-Outs</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->ytd_moveout}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">YTD Net Residents</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->ytd_movein - $report_data->ytd_moveout }}</div>
                </div>
            </div>
        </div>
    @else
        <div class="row reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Census</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-2 text-align-center"><h4 class="sub-header">Building</h4></div>
                    <div class="col-md-2 text-align-center">
                        <h4 class="sub-header">OCC</h4>
                    </div>
                    <div class="col-md-2 text-align-center">
                        <h4 class="sub-header">TOTAL UNIT</h4>
                    </div>
                    <div class="col-md-2 text-align-center">
                        <h4 class="sub-header">(%)</h4>
                    </div>
                    <div class="col-md-2 text-align-center">
                        <h4 class="sub-header">OPEN UNIT</h4>
                    </div>
                    <div class="col-md-2 text-align-center">
                        <h4 class="sub-header">TOTAL RESIDENT</h4>
                    </div>
                </div>

                <div class="row mb-4">
                    @php
                        $item = $report_data->get_Cencaps()->first();
                    @endphp
                    <div class="col-md-2 text-align-center">{{ $item->get_Building->name }}</div>
                    <div class="col-md-2 text-align-center">{{ $item->census }}</div>
                    <div class="col-md-2 text-align-center">{{ $item->capacity }}</div>
                    <div class="col-md-2 text-align-center">
                        @if($item->capacity != 0)
                            {{ number_format((100*($item->census/$item->capacity)), '2', '.', '')}}(%)
                        @endif
                    </div>
                    <div class="col-md-2 text-align-center">{{ $item->capacity - $item->census }}</div>
                    <div class="col-md-2 text-align-center">{{ $item->total_resident }}</div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Inquiries</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Type</h4>
                    </div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Count</h4>
                    </div>
                </div>
                @php
                    $total_count = 0;
                @endphp

                @foreach($report_data->get_Inquiries as $item)
                    @php
                        $total_count += $item->number;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-3 text-align-center">{{ $item->description }}</div>
                        <div class="col-md-3 text-align-center">{{ $item->number }}</div>
                    </div>
                @endforeach

                <div class="row mb-4">
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">Total</h4></div>
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">{{ $total_count }}</h4></div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Deposits</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Type</h4>
                    </div>
                    <div class="col-md-3 text-align-center">
                        <h4 class="sub-header">Count</h4>
                    </div>
                </div>
                @php
                    $total_count = 0;
                @endphp

                @foreach($report_data->get_Moveouts as $item)
                    @php
                        $total_count += $item->number;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-3 text-align-center">{{ $item->description }}</div>
                        <div class="col-md-3 text-align-center">{{ $item->number }}</div>
                    </div>
                @endforeach

                <div class="row mb-4">
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">Total</h4></div>
                    <div class="col-md-3 text-align-center"><h4 class="sub-header">{{ $total_count }}</h4></div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Move In/Out</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">INCR/DECR</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->wtd_movein - $report_data->wtd_moveout}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">MOVE IN</div>
                    <div class="col-md-3 text-align-center">{{ $report_data->wtd_movein}}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">MOVE OUT</div>
                    <div class="col-md-3 text-align-center">
                        {{ $report_data->wtd_moveout}}
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">N/0 NOTICE</div>
                    <div class="col-md-3 text-align-center">
                    @php
                        $mo_motice = 0;
                    @endphp
                    @foreach($report_data->get_Moveouts as $item)
                        @if($item->description == 'M/0 NOTICE')
                            @php
                                $mo_notice += $item->number
                            @endphp
                        @endif
                    @endforeach
                    {{ $mo_motice }}
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">WEEK AT 100%</div>
                    <div class="col-md-3 text-align-center">
                        @php
                            $count = 0;

                            for($i=0; $i<count($week_data); $i++) {
                                $cencaps_data = $week_data[$i]->get_Cencaps->first();
                                $census = $cencaps_data->census;
                                $capacity = $cencaps_data->capacity;

                                if($census == $capacity) {
                                    $count++;
                                }
                                else {
                                    break;
                                }
                            }
                        @endphp

                        {{ $count }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-5 reports-data">
            <div class="col-md-4">
                <h3 class="landingtitle">Other</h3>
            </div>
            <div class="col-md-8">
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Prior YE OCC </div>
                    <div class="col-md-3 text-align-center">{{ $report_data->prior_ye_occ }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">YTD Units</div>
                    <div class="col-md-3 text-align-center">
                        @php
                            $cencaps_data = $report_data->get_Cencaps->first();
                            $census = $cencaps_data->census;
                            $total_capacity = $cencaps_data->capacity;

                            $ytd_unit = $census - $report_data->prior_ye_occ

                        @endphp

                        {{ $ytd_unit }}
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 text-align-center">Change</div>
                    <div class="col-md-3 text-align-center">
                        @if($total_capacity != 0)
                        {{ number_format((100*($ytd_unit/$total_capacity)), '2', '.', '')}}(%)
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@endsection

@section('additional_js')
<script>
    var base_url = "{{ url('/') }}";
</script>
<script src="{{ asset('assets/js/custom/go.js') }}"></script>
@endsection
