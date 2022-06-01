@extends('layouts.app')

@section('additional_css')

@endsection

@section('contents')
@if ($errors->any())
@foreach ($errors->all() as $error)
<div class="alert alert-custom alert-notice alert-light-danger alert-dismissible fade show mb-5" role='alert'>
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{ $error }}
</div>
@endforeach
@endif

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
        <!-- <input type="button" class="span2 btn-rounded" id="report_date" data-id="{{ $period_data->id }}"
            data-start="{{ $period_data->starting }}" data-caption="{{ $period_data->caption }}"
            value="{{ $period_data->caption }}" /> -->
        <select class="form-control selectpicker" id="report_date" data-size="5" data-live-search="true">
            @foreach ($periodItems as $period)
            @if($period->id == $period_data->id)
            <option value="{{ $period->id }}" selected="true">{{ $period->caption }}</option>
            @else
            <option value="{{ $period->id }}">{{ $period->caption }}</option>
            @endif
            @endforeach
        </select>
    </div>
</div>

@if(empty($report_data))
<div style="height: 520px;">
    <div class="alert alert-custom alert-notice alert-light-danger alert-dismissible fade show mb-5" role='alert'>
        <button type="button" class="close" data-dismiss="alert">×</button>
        No Report Data ! You can not edit the data in this date and community. You must add the data in this date and
        community. &nbsp;&nbsp;&nbsp;
        <a style="color: blue; font-size: 16px; font-weight: 700;"
            href="{{ route('community_view_add', ['date' => $period_data->starting, 'community_id' => $community_id]) }}">Go
            to Add <span><i class="far fa-arrow-alt-circle-right"></i></span></a>
    </div>
</div>
@else
@if($user_data->company_id == 1)
<form method="POST" id="report_form" name="report_form" action="{{ route('update_report') }}" style="width: 100%;">
    @csrf

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Census and Capacity</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody class="cencaps_body">
                    <tr>
                        <th>Building</th>
                        <th>Census</th>
                        <th>Capacity</th>
                    </tr>
                    @foreach($cencaps_data as $item)
                    <tr id="cencaps_record_{{ $loop->index+1 }}">
                        <td class="w-30">
                            <select class="form-control selectpicker" onchange="whatedit('1')"
                                name="building_name_{{ $loop->index+1 }}" id="building_name_{{ $loop->index+1 }}"
                                data-live-search="true" tabindex="null">
                                @foreach ($building_data as $bitem)
                                @if($bitem->id == $item->building_id)
                                <option value="{{ $bitem->id }}" selected>
                                    {{ $bitem->name }}
                                </option>
                                @else
                                <option value="{{ $bitem->id }}">
                                    {{ $bitem->name }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="w-30">
                            <input type="text" onchange="whateditFunc('1')" value="{{ $item->census }}"
                                name="census_{{ $loop->index+1 }}" class="form-control form-control-solid census"
                                required>
                        </td>
                        <td class="w-30">
                            <input type="text" onchange="whateditFunc('1')" value="{{ $item->capacity }}"
                                name="capacity_{{ $loop->index+1 }}" class="form-control form-control-solid capacity"
                                required>
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_cencaps_btn_{{ $loop->index+1 }}"
                                href="javascript:;" onclick="del_cencaps({{ $loop->index+1 }})" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row jcc hs">
                <span class="svg-icon svg-icon-primary svg-icon-2x" id="add_cencaps_btn">
                    <i class="fas fa-plus-circle"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Inquiries</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody class="inquiry_body">
                    <tr>
                        <th>Type</th>
                        <th>Count</th>
                    </tr>
                    @foreach($inquiry_data as $item)
                    <tr id="inquiry_record_{{ $loop->index+1 }}">
                        <td class="w-45">
                            <input type="text" onchange="whateditFunc('2')" id="inquiry_type_{{ $loop->index+1 }}"
                                name="inquiry_type_{{ $loop->index+1 }}" value="{{$item->description}}"
                                class="form-control form-control-solid" required>
                        </td>
                        <td class="w-45">
                            <input type="text" onchange="whateditFunc('2')" name="inquiry_{{ $loop->index+1 }}"
                                value="{{$item->number}}" class="form-control form-control-solid" required>
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_inquriy_btn_{{ $loop->index+1 }}"
                                onclick="del_inquiry({{ $loop->index+1 }})" href="javascript:;" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row jcc hs">
                <span class="svg-icon svg-icon-primary svg-icon-2x" id="add_inquiry_btn">
                    <i class="fas fa-plus-circle"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Moveouts</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody class="moveout_body">
                    <tr>
                        <th>Reason</th>
                        <th>Count</th>
                    </tr>
                    @foreach($moveout_data as $item)
                    <tr id="moveout_record_{{ $loop->index+1 }}">
                        <td class="w-45">
                            <input type="text" onchange="whateditFunc('3')" id="move_type_{{ $loop->index+1 }}"
                                name="move_type_{{ $loop->index+1 }}" value="{{ $item->description }}"
                                class="form-control form-control-solid">
                        </td>
                        <td class="w-45">
                            <input type="text" onchange="whateditFunc('3')" name="move_{{ $loop->index+1 }}"
                                value="{{ $item->number }}" class="form-control form-control-solid">
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_moveout_btn_{{ $loop->index+1 }}"
                                type="moveouts" onclick="del_moveout({{ $loop->index+1 }})" href="javascript:;"
                                title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row jcc hs">
                <span class="svg-icon svg-icon-primary svg-icon-2x" id="add_moveout_btn">
                    <i class="fas fa-plus-circle"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Statistics</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody>
                    <tr></tr>
                    <tr>
                        <th class="w-50">Unqualified</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('4')"
                                value="{{ $report_data->unqualified }}" name="unqualified"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">Tours</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('4')"
                                value="{{ $report_data->tours }}" name="tours" class="form-control form-control-solid"
                                required></th>
                    </tr>
                    <tr>
                        <th class="w-50">Deposits</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('4')"
                                value="{{ $report_data->deposits }}" name="deposits"
                                class="form-control form-control-solid" required></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Move In/Out</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody>
                    <tr></tr>
                    <tr>
                        <th class="w-50">WTD Move-Ins</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->wtd_movein }}" name="wtd_movein"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">WTD Move-Outs</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->wtd_moveout }}" name="wtd_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">YTD Move-Ins</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->ytd_movein }}" name="ytd_movein"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">YTD Move-Outs</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->ytd_moveout }}" name="ytd_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">TOTAL MOVE OUT</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->total_moveout }}" name="total_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">OTHER</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody>
                    <tr></tr>
                    <tr>
                        <th class="w-50">PRIOR YE OCC</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('6')"
                                value="{{ $report_data->prior_ye_occ }}" name="prior_ye_occ" id="prior_ye_occ"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <!-- <tr>
                                    <th class="w-50">Y.T.D UNITS</th>
                                    <th class="w-50"><input type="text" value="" name="ytd_unit" id="ytd_unit" class="form-control form-control-solid" required readonly></th>
                                </tr>
                                <tr>
                                    <th class="w-50">CHNAGES %</th>
                                    <th class="w-50"><input type="text" value="" name="change_percent" id="change_percent" class="form-control form-control-solid" required readonly></th>
                                </tr> -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="row jcc">
        <div class="col-md-6 tar">
            <button class="btn-rounded" id="save_btn" style="color: white !important;" type="button">
                Save
            </button>
        </div>
        <div class="col-md-6">
            <a href="{{ route('home') }}" type="button" class="btn-rounded" style="color: white !important;">
                Cancel
            </a>
        </div>
    </div>
    <input type="hidden" id="report_id" name="report_id" value="{{ $report_id }}">
    <input type="hidden" id="whatedit" name="whatedit" value="{{$whatedit}}">
    <input type="hidden" id="community_id" name="community_id" value="{{ $community_id }}">
    <input type="hidden" id="period_id" name="period_id" value="{{ $period_id }}">
    <input type="hidden" id="moveouts_num" name="moveouts_num" value="{{ $moveout_data->count() }}">
    <input type="hidden" id="inquiries_num" name="inquiries_num" value="{{ $inquiry_data->count() }}">
    <input type="hidden" id="cencaps_num" name="cencaps_num" value="{{ $cencaps_data->count() }}">
</form>
@else
<form method="POST" id="report_form" name="report_form" action="{{ route('update_report') }}" style="width: 100%;">
    @csrf

    <input type="text" class="dn whateditC" name="whatedit" value="">
    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Census and Capacity</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody class="cencaps_body">
                    <tr>
                        <th>Building</th>
                        <th>OCC</th>
                        <th>Total Unit</th>
                        <th>Total Resident</th>
                    </tr>
                    @foreach($cencaps_data as $item)
                    <tr id="cencaps_record_{{ $loop->index+1 }}">
                        <td class="w-30">
                            <select class="form-control selectpicker" onchange="whateditFunc('1')"
                                name="building_name_{{ $loop->index+1 }}" id="building_name_{{ $loop->index+1 }}"
                                data-live-search="true" tabindex="null">
                                @foreach ($building_data as $bitem)
                                @if($bitem->id == $item->building_id)
                                <option value="{{ $bitem->id }}" selected>
                                    {{ $bitem->name }}
                                </option>
                                @else
                                <option value="{{ $bitem->id }}">
                                    {{ $bitem->name }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="w-20">
                            <input type="text" onchange="whateditFunc('1')" value="{{ $item->census }}"
                                name="census_{{ $loop->index+1 }}" class="form-control form-control-solid census"
                                required>
                        </td>
                        <td class="w-20">
                            <input type="text" onchange="whateditFunc('1')" value="{{ $item->capacity }}"
                                name="capacity_{{ $loop->index+1 }}" class="form-control form-control-solid capacity"
                                required>
                        </td>
                        <td class="w-20">
                            <input type="text" onchange="whateditFunc('1')" value="{{ $item->total_resident }}"
                                name="total_resident_{{ $loop->index+1 }}"
                                class="form-control form-control-solid total_resident" required>
                        </td>
                        <td class="w-10">

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Inquiries</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody class="inquiry_body">
                    <tr>
                        <th>Type</th>
                        <th>Count</th>
                    </tr>
                    @foreach($inquiry_data as $item)
                    <tr id="inquiry_record_{{ $loop->index+1 }}">
                        <td class="w-45">
                            <select class="form-control selectpicker" onchange="whateditFunc('2')"
                                id="inquiry_type_{{ $loop->index+1 }}" name="inquiry_type_{{ $loop->index+1 }}"
                                data-live-search="true" tabindex="null">
                                @if($item->description == 'DI/DEP CALLS')
                                <option value="DI/DEP CALLS" selected>DI/DEP CALLS</option>
                                @else
                                <option value="DI/DEP CALLS">DI/DEP CALLS</option>
                                @endif

                                @if($item->description == 'COMM VISITS')
                                <option value="COMM VISITS" selected>COMM VISITS</option>
                                @else
                                <option value="COMM VISITS">COMM VISITS</option>
                                @endif

                            </select>
                        </td>
                        <td class="w-45">
                            <input type="text" onchange="whateditFunc('2')" name="inquiry_{{ $loop->index+1 }}"
                                value="{{ $item->number }}" class="form-control form-control-solid" required>
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_inquriy_btn_{{ $loop->index+1 }}"
                                type="inquiries" onclick="del_inquiry({{ $loop->index+1 }})" href="javascript:;"
                                title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row jcc hs">
                <span class="svg-icon svg-icon-primary svg-icon-2x" id="add_inquiry_btn">
                    <i class="fas fa-plus-circle"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Deposits</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody class="moveout_body">
                    <tr>
                        <th>Reason</th>
                        <th>Count</th>
                    </tr>
                    @foreach($moveout_data as $item)
                    <tr id="moveout_record_{{ $loop->index+1 }}">
                        <td class="w-45">
                            <select class="form-control selectpicker" onchange="whateditFunc('3')"
                                id="move_type_{{ $loop->index+1 }}" name="move_type_{{ $loop->index+1 }}"
                                data-live-search="true" tabindex="null">
                                @if($item->description == 'SPEC DEP')
                                <option value="SPEC DEP" selected>SPEC DEP</option>
                                @else
                                <option value="SPEC DEP">SPEC DEP</option>
                                @endif

                                @if($item->description == 'GEN DEP')
                                <option value="GEN DEP" selected>GEN DEP</option>
                                @else
                                <option value="GEN DEP">GEN DEP</option>
                                @endif

                            </select>
                        </td>
                        <td class="w-45">
                            <input type="text" onchange="whateditFunc('3')" name="move_{{ $loop->index+1 }}"
                                value="{{ $item->number }}" class="form-control form-control-solid">
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_moveout_btn_{{ $loop->index+1 }}"
                                type="moveouts" onclick="del_moveout({{ $loop->index+1 }})" href="javascript:;"
                                title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row jcc hs">
                <span class="svg-icon svg-icon-primary svg-icon-2x" id="add_moveout_btn">
                    <i class="fas fa-plus-circle"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">Move In/Out</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody>
                    <tr></tr>
                    <tr>
                        <th class="w-50">MOVE IN</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->wtd_movein }}" name="wtd_movein"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">MOVE OUT</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')"
                                value="{{ $report_data->wtd_moveout }}" name="wtd_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">M/O NOTICE</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('5')" value="{{ $mo_notice }}"
                                name="mo_notice" class="form-control form-control-solid" required></th>
                    </tr>
                    <!-- <tr>
                                    <th class="w-50">WEEK AT 100%</th>
                                    <th class="w-50"><input type="text" value="" name="ytd_moveout" class="form-control form-control-solid" required></th>
                                </tr> -->
                    <tr>
                        <th class="w-50">TOTAL MOVE OUT</th>
                        <th class="w-50"><input type="text" value="{{ $report_data->total_moveout }}"
                                name="total_moveout" class="form-control form-control-solid" required></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-custom cb" style="width: 100%;">
        <div class="card-header">
            <h3 class="card-title">OTHER</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless viewtable table-sm table-hover">
                <tbody>
                    <tr></tr>
                    <tr>
                        <th class="w-50">PRIOR YE OCC</th>
                        <th class="w-50"><input type="text" onchange="whateditFunc('6')"
                                value="{{ $report_data->prior_ye_occ }}" name="prior_ye_occ" id="prior_ye_occ"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <!-- <tr>
                                    <th class="w-50">Y.T.D UNITS</th>
                                    <th class="w-50"><input type="text" value="" name="ytd_unit" id="ytd_unit" class="form-control form-control-solid" required readonly></th>
                                </tr>
                                <tr>
                                    <th class="w-50">CHNAGES %</th>
                                    <th class="w-50"><input type="text" value="" name="change_percent" id="change_percent" class="form-control form-control-solid" required readonly></th>
                                </tr> -->
                </tbody>
            </table>
        </div>
    </div>

    <div class="row jcc">
        <div class="col-md-6 tar">
            <button id="save_btn" class="btn-rounded" style="color: white !important;" type="button">
                Save
            </button>
        </div>
        <div class="col-md-6">
            <a href="{{ route('home') }}" class="btn-rounded" type="button" style="color: white !important;">
                Cancel
            </a>
        </div>
    </div>
    <input type="hidden" id="report_id" name="report_id" value="{{ $report_id }}">
    <input type="hidden" id="whatedit" name="whatedit" value="{{$whatedit}}">
    <input type="hidden" id="community_id" name="community_id" value="{{ $community_id }}">
    <input type="hidden" id="period_id" name="period_id" value="{{ $period_id }}">
    <input type="hidden" id="moveouts_num" name="moveouts_num" value="{{ $moveout_data->count() }}">
    <input type="hidden" id="inquiries_num" name="inquiries_num" value="{{ $inquiry_data->count() }}">
    <input type="hidden" id="cencaps_num" name="cencaps_num" value="{{ $cencaps_data->count() }}">
</form>
@endif
@endif

@endsection

@section('additional_js')
<script>
var buildingsData = <?php echo $building_data ?>;

var base_url = "{{ url('/') }}";
var company_id = "{{ $user_data->company_id }}";
</script>
<script src="{{ asset('assets/js/custom/editaction.js') }}"></script>
@endsection