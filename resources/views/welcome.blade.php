@extends('layouts.app')

@section('additional_css')

@endsection

@section('contents')

<input type="hidden" id="alert" value="{{ Session::get('alert') }}">

<div class="row">
    <div class="col-md-12 TitleHeaderBar">
        <h3 class="landingtitle">Community Reports</h3>
    </div>
</div>
<div class="row mb-8">
    <div class="col-md-4 d-flex align-items-center">
        <label class="mr-3 mb-0 d-none d-md-block" style="width: 20%;"><strong>View</strong></label>

        <select class="form-control selectpicker" id="view_community" data-size="5" data-live-search="true">
            <option value="">Select Community</option>

            @foreach ($viewitems as $item)
                @if($item->id == $userData->community_id || ($item->id == 1 && $userData->community_id == 10))
                    <option value="{{ $item->id }}" selected="true">{{ $item->name }}</option>
                @else
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endif
            @endforeach
        </select>
    </div>

    <div class="col-md-3 d-flex align-items-center">
        <label class="mr-3 mb-0 d-none d-md-block vewCompany">
            Date:&nbsp;
        </label>

        <select class="form-control selectpicker" id="community_view_from_btn" data-size="5" data-live-search="true">
            @foreach ($periodItems as $period)
                @if($period->id == $oneItem->id)
                    <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                @else
                    <option value="{{ $period->id }}">{{ $period->caption }}</option>
                @endif
            @endforeach
        </select>

        {{-- <input type="button" class="span2 btn-rounded" id="community_view_from_btn" data-id="{{ $oneItem->id }}" data-start="{{ $oneItem->starting }}" data-caption="{{ $oneItem->caption }}" value="{{ $oneItem->caption }}" /> --}}
    </div>

    <div class="col-md-3"></div>
    <div class="col-md-1">
        @if($add_flag == 0 && $report_id != "")
            @if($userData->levelreport > 0)
                <a href="{{ route('community_view_goto', ['report_id' => $report_id]) }}" class="ViewReportsOne  btn-rounded" id="community_view_go" type="button">
                    GO
                </a>
            @else
                <a href="javascript:void(0)" disabled="true" class="ViewReportsOne  btn-rounded btn-disabled" id="community_view_go" type="button">
                    GO
                </a>
            @endif
        @else
            <a href="javascript:void(0)" class="ViewReportsOne  btn-rounded btn-disabled" id="community_view_go" type="button" disabled="true">
                GO
            </a>
        @endif
    </div>

    <div class="col-md-1">
        @if($add_flag == 0 && $report_id != "")
            @if($userData->leveledit > 0)
                <a href="{{ route('community_view_edit', ['report_id' => $report_id]) }}" class="ViewReportsOne btn-rounded" type="button" id="editBtn">
                    Edit
                </a>
            @else
                <a href="javascript:void(0)" disabled="true" class="ViewReportsOne btn-rounded btn-disabled" type="button" id="editBtn">
                    Edit
                </a>
            @endif
            <a style="display:none;" href="javascript:void(0)" class="ViewReportsOne btn-rounded" type="button" id="addBtn" >
                Add
            </a>
        @else
            @if($userData->leveladd > 0)
            <a href="{{ route('community_view_add', ['period_id' => $oneItem->id, 'community_id' => $userData->community_id]) }}" class="ViewReportsOne btn-rounded" type="button" id="addBtn" >
                Add
            </a>
            @else
            <a href="javascript:void(0)" disabled="true" class="ViewReportsOne btn-rounded btn-disabled" type="button" id="addBtn" >
                Add
            </a>
            @endif

            <a style="display:none;" href="javascript:void(0)" class="ViewReportsOne btn-rounded" type="button" id="editBtn">
                Edit
            </a>
        @endif
    </div>
</div>

<div class="row mb-8">
    <div class="col-md-4">
        <div class="d-flex align-items-center">
            <label class="mr-3 mb-0 d-none d-md-block" style="width: 20%;"><strong>Trend</strong></label>
            <select class="form-control selectpicker" id="trend_community" data-size="5" data-live-search="true">
                <option value="">Select Community</option>
                    @foreach ($viewitems as $item)
                        @if($item->id == $userData->community_id || ($item->id == 1 && $userData->community_id == 10))
                            <option value="{{ $item->id }}" selected="true">{{ $item->name }}</option>
                        @else
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endif
                    @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="mr-3 mb-0 d-none d-md-block vewCompany">
                From:&nbsp;
            </label>
            <select class="form-control selectpicker" id="community_trend_from_btn" data-size="5" data-live-search="true">
                @foreach ($periodItems as $period)
                    @if($period->id == $oneItem->id)
                        <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                    @else
                        <option value="{{ $period->id }}">{{ $period->caption }}</option>
                    @endif
                @endforeach
            </select>
            {{-- <input type="button" class="span2 btn-rounded" id="community_trend_from_btn" data-id="{{ $oneItem->id }}" data-start="{{ $oneItem->starting }}" data-caption="{{ $oneItem->caption }}" value="{{ $oneItem->caption }}" /> --}}
        </div>
    </div>

    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="mr-3 mb-0 d-none d-md-block vewCompany">
                To:&nbsp;
            </label>
            <select class="form-control selectpicker" id="community_trend_to_btn" data-size="5" data-live-search="true">
                @foreach ($periodItems as $period)
                    @if($period->id == $oneItem->id)
                        <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                    @else
                        <option value="{{ $period->id }}">{{ $period->caption }}</option>
                    @endif
                @endforeach
            </select>
            {{-- <input type="button" class="span2 btn-rounded" id="community_trend_to_btn" data-id="{{ $oneItem->id }}" data-start="{{ $oneItem->starting }}" data-caption="{{ $oneItem->caption }}" value="{{ $oneItem->caption }}" /> --}}
        </div>
    </div>
    <div class="col-md-1">
        @if($add_flag == 0 && $report_id != "")
            @if($userData->levelreport > 0)
            <a href="{{ route('community_trend_goto', ['community_id' => $userData->community_id, 'from' => $oneItem->id, 'to' => $oneItem->id]) }}" class="ViewReportsOne  btn-rounded" id="community_trend_go" type="button">GO</a>
            @else
            <a href="javascript:void(0)" disabled="type" class="ViewReportsOne  btn-rounded btn-disabled" id="community_trend_go" type="button">GO</a>
            @endif
        @else
            <a href="javascript:void(0)" class="ViewReportsOne  btn-rounded btn-disabled" id="community_trend_go" type="button" disabled="true">GO</a>
        @endif
    </div>
</div>


@if($userData->levelcompany > 0)
<div class="row mb-8">
    <div class="col-md-12 TitleHeaderBar">
        <h3 class="landingtitle">Company Reports</h3>
    </div>
</div>
<div class="row mb-8">
    <div class="col-md-4 d-flex align-items-center">
        <label class="mr-3 mb-0 d-none d-md-block vewCompany">
            <strong>View Company Summary for Period:</strong>&nbsp;
        </label>
    </div>
    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="mr-3 mb-0 d-none d-md-block vewCompany">
                Date:&nbsp;
            </label>
            <select class="form-control selectpicker" id="company_view_from_btn" data-size="5" data-live-search="true">
                @foreach ($periodItems as $period)
                    @if($period->id == $oneItem->id)
                        <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                    @else
                        <option value="{{ $period->id }}">{{ $period->caption }}</option>
                    @endif
                @endforeach
            </select>
            {{-- <input type="button" class="span2 btn-rounded" id="company_view_from_btn" data-id="{{ $oneItem->id }}" data-start="{{ $oneItem->starting }}" data-caption="{{ $oneItem->caption }}" value="{{ $oneItem->caption }}" /> --}}
        </div>
    </div>
    <div class="col-md-3"></div>
    <div class="col-md-1">
        @if($add_flag == 0 && $report_id != "")
            <a href="{{ route('company_view_goto', ['community_id' => $userData->community_id, 'date' => $oneItem->id]) }}" class="ViewReportsOne  btn-rounded" id="company_view_go" type="button">GO</a>
        @else
            <a href="javascript:void(0)" class="ViewReportsOne  btn-rounded btn-disabled" id="company_view_go" type="button" disabled="true">GO</a>
        @endif
    </div>
</div>
<div class="row mb-8">
    <div class="col-md-4 d-flex align-items-center">
        <label class="mr-3 mb-0 d-none d-md-block vewCompany">
            <strong>View Company Trend Report </strong>&nbsp;&nbsp;&nbsp;&nbsp;
        </label>
    </div>
    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="mr-3 mb-0 d-none d-md-block vewCompany">
                From:&nbsp;
            </label>
            <select class="form-control selectpicker" id="company_trend_from_btn" data-size="5" data-live-search="true">
                @foreach ($periodItems as $period)
                    @if($period->id == $oneItem->id)
                        <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                    @else
                        <option value="{{ $period->id }}">{{ $period->caption }}</option>
                    @endif
                @endforeach
            </select>
            {{-- <input type="button" class="span2 btn-rounded" id="company_trend_from_btn" data-id="{{ $oneItem->id }}" data-start="{{ $oneItem->starting }}" data-caption="{{ $oneItem->caption }}" value="{{ $oneItem->caption }}" /> --}}
        </div>
    </div>

    <div class="col-md-3">
        <div class="d-flex align-items-center">
            <label class="mr-3 mb-0 d-none d-md-block vewCompany">
                To:&nbsp;
            </label>
            <select class="form-control selectpicker" id="company_trend_to_btn" data-size="5" data-live-search="true">
                @foreach ($periodItems as $period)
                    @if($period->id == $oneItem->id)
                        <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
                    @else
                        <option value="{{ $period->id }}">{{ $period->caption }}</option>
                    @endif
                @endforeach
            </select>
            {{-- <input type="button" class="span2 btn-rounded" id="company_trend_to_btn" data-id="{{ $oneItem->id }}" data-start="{{ $oneItem->starting }}" data-caption="{{ $oneItem->caption }}" value="{{ $oneItem->caption }}" /> --}}
        </div>
    </div>
    <div class="col-md-1">
        @if($add_flag == 0 && $report_id != "")
            <a href="{{ route('company_trend_goto', ['community_id' => $userData->community_id, 'from' => $oneItem->id, 'to' => $oneItem->id]) }}" class="ViewReportsOne  btn-rounded" id="company_trend_go" type="button">GO</a>
        @else
            <a href="javascript:void(0)" class="ViewReportsOne  btn-rounded btn-disabled" id="company_trend_go" type="button" disabled="true">GO</a>
        @endif
    </div>
</div>
@endif

<hr />
<div class="row mb-8">
    <div class="col-xl-12 TitleHeaderBar">
        <h3 class="landingtitle">Chart Reports Data</h3>
    </div>
    <div class="col-xl-6 di">
        <h3> <span style="color: #8950FC !important;">Current</span> <span style="color:#1BC5BD !important;">Census</span></h3>
        <div id="chart_12" class="d-flex justify-content-center"></div>
    </div>
    <div class="col-xl-6 di">
        <h3> <span style="color: #1BC5BD !important;">Census</span> vs <span style="color:#8950FC !important;">Capacity</span></h3>
        <div id="chart_3"></div>
    </div>
</div>

<hr />

<div class="row mb-8">
    <div class="col-xl-12 TitleHeaderBar">
        <h3 class="landingtitle">Table Reports Data</h3>
    </div>
    <div class="col-xl-12">
        <div class="dashboard-btn-group">
            <button class="span2 btn-rounded active" id="default_btn">DEFAULT</button>
            <button class="span2 btn-rounded" id="full_percent_btn">COMMUNITIES At 100%</button>
            <button class="span2 btn-rounded" id="pre_btn">COMMUNITIES NOT AT 100%</button>
            <button class="span2 btn-rounded" id="less_year_btn">COMMUNITIES THAT HAVE INCREASE</button>
            <button class="span2 btn-rounded" id="more_year_btn">COMMUNITIES THAT HAVE DECREASE</button>
            <button class="span2 btn-rounded" id="westen_btn">COMMUNITIES WITH OPEN UNIT</button>
        </div>
    </div>
    <div class="col-xl-12">
        <div class="table-responsive" id="table-wrapper" style="overflow: hidden">
        </div>
    </div>
</div>
@endsection

@section('additional_js')
    <script>
        var base_url = "{{ url('/') }}";
    </script>
    <script src="{{ asset('assets/js/custom/home.js') }}"></script>
@endsection
