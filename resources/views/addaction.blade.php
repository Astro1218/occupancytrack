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

@if(!empty($report_data))
<div style="height: 520px;">
    <div class="alert alert-custom alert-notice alert-light-danger alert-dismissible fade show mb-5" role='alert'>
        <button type="button" class="close" data-dismiss="alert">×</button>
        Already added data in this date and community! You can not add the data in this date and community
        again.&nbsp;&nbsp;&nbsp;
        <a style="color: blue; font-size: 16px; font-weight: 700;"
            href="{{ route('community_view_edit', ['date' => $period_data->starting, 'community_id' => $community_id]) }}">Go
            to Edit <span><i class="far fa-arrow-alt-circle-right"></i></span></a>
    </div>
</div>
@else
@if($user_data->company_id == 1)
<form method="POST" id="report_form" name="report_form" action="{{ route('add_report') }}" style="width: 100%;">
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
                    <tr id="cencaps_record_1">
                        <td class="w-30">
                            <select class="form-control selectpicker" name="building_name_1" id="building_name_1"
                                data-live-search="true" tabindex="null">
                                @foreach ($building_data as $bitem)
                                <option value="{{ $bitem->id }}">
                                    {{ $bitem->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-30">
                            <input type="text" value="" name="census_1" class="form-control form-control-solid census"
                                required>
                        </td>
                        <td class="w-30">
                            <input type="text" value="" name="capacity_1"
                                class="form-control form-control-solid capacity" required>
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_cencaps_btn_1" href="javascript:;"
                                onclick="del_cencaps(1)" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
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
                    <tr id="inquiry_record_1">
                        <td class="w-45">
                            <input type="text" id="inquiry_type_1" name="inquiry_type_1" value=""
                                class="form-control form-control-solid" required>
                        </td>
                        <td class="w-45">
                            <input type="text" name="inquiry_1" value="" class="form-control form-control-solid"
                                required>
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_inquriy_btn_1" onclick="del_inquiry(1)"
                                href="javascript:;" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
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
                    <tr id="moveout_record_1">
                        <td class="w-45">
                            <input type="text" id="move_type_1" name="move_type_1" value=""
                                class="form-control form-control-solid">
                        </td>
                        <td class="w-45">
                            <input type="text" name="move_1" value="" class="form-control form-control-solid">
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_moveout_btn_1" type="moveouts"
                                onclick="del_moveout(1)" href="javascript:;" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
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
                        <th class="w-50"><input type="text" value="" name="unqualified"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">Tours</th>
                        <th class="w-50"><input type="text" value="" name="tours"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">Deposits</th>
                        <th class="w-50"><input type="text" value="" name="deposits"
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
                        <th class="w-50"><input type="text" value="" name="wtd_movein"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">WTD Move-Outs</th>
                        <th class="w-50"><input type="text" value="" name="wtd_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">YTD Move-Ins</th>
                        <th class="w-50"><input type="text" value="" name="ytd_movein"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">YTD Move-Outs</th>
                        <th class="w-50"><input type="text" value="" name="ytd_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">TOTAL MOVE OUT</th>
                        <th class="w-50"><input type="text" value="" name="total_moveout"
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
                        <th class="w-50"><input type="text" value="" name="prior_ye_occ" id="prior_ye_occ"
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
    <input type="hidden" id="community_id" name="community_id" value="{{ $community_id }}">
    <input type="hidden" id="period_id" name="period_id" value="{{ $period_id }}">
    <input type="hidden" id="moveouts_num" name="moveouts_num" value="1">
    <input type="hidden" id="inquiries_num" name="inquiries_num" value="1">
    <input type="hidden" id="cencaps_num" name="cencaps_num" value="1">
</form>
@else
<form method="POST" id="report_form" name="report_form" action="{{ route('add_report') }}" style="width: 100%;">
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
                        <th>OCC</th>
                        <th>Total Unit</th>
                        <th>Total Resident</th>
                    </tr>
                    <tr id="cencaps_record_1">
                        <td class="w-30">
                            <select class="form-control selectpicker" name="building_name_1" id="building_name_1"
                                data-live-search="true" tabindex="null">
                                @foreach ($building_data as $bitem)
                                <option value="{{ $bitem->id }}">
                                    {{ $bitem->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-20">
                            <input type="text" value="" name="census_1" class="form-control form-control-solid census"
                                required>
                        </td>
                        <td class="w-20">
                            <input type="text" value="" name="capacity_1"
                                class="form-control form-control-solid capacity" required>
                        </td>
                        <td class="w-20">
                            <input type="text" value="" name="total_resident_1"
                                class="form-control form-control-solid total_resident" required>
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_cencaps_btn_1" href="javascript:;"
                                onclick="del_cencaps(1)" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
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
                    <tr id="inquiry_record_1">
                        <td class="w-45">
                            <select class="form-control selectpicker" id="inquiry_type_1" name="inquiry_type_1"
                                data-live-search="true" tabindex="null">
                                <option value="DI/DEP CALLS">DI/DEP CALLS</option>
                                <option value="COMM VISITS">COMM VISITS</option>
                            </select>
                        </td>
                        <td class="w-45">
                            <input type="text" name="inquiry_1" value="" class="form-control form-control-solid"
                                required>
                        </td>
                        <td class="w-10">

                        </td>
                    </tr>
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
                    <tr id="moveout_record_1">
                        <td class="w-45">
                            <select class="form-control selectpicker" id="move_type_1" name="move_type_1"
                                data-live-search="true" tabindex="null">
                                <option value="SPEC DEP">SPEC DEP</option>
                                <option value="GEN DEP">GEN DEP</option>
                            </select>
                        </td>
                        <td class="w-45">
                            <input type="text" name="move_1" value="" class="form-control form-control-solid">
                        </td>
                        <td class="w-10">
                            <a class="btn btn-sm btn-clean btn-icon" id="del_moveout_btn_1" type="moveouts"
                                onclick="del_moveout(1)" href="javascript:;" title="Delete">
                                <span><i class="far fa-times-circle"></i></span>
                            </a>
                        </td>
                    </tr>
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
                        <th class="w-50"><input type="text" value="" name="wtd_movein"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">MOVE OUT</th>
                        <th class="w-50"><input type="text" value="" name="wtd_moveout"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <tr>
                        <th class="w-50">M/O NOTICE</th>
                        <th class="w-50"><input type="text" value="" name="mo_notice"
                                class="form-control form-control-solid" required></th>
                    </tr>
                    <!-- <tr>
                                    <th class="w-50">WEEK AT 100%</th>
                                    <th class="w-50"><input type="text" value="" name="ytd_moveout" class="form-control form-control-solid" required></th>
                                </tr> -->
                    <tr>
                        <th class="w-50">TOTAL MOVE OUT</th>
                        <th class="w-50"><input type="text" value="" name="total_moveout"
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
                        <th class="w-50"><input type="text" value="" name="prior_ye_occ" id="prior_ye_occ"
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
    <input type="hidden" id="community_id" name="community_id" value="{{ $community_id }}">
    <input type="hidden" id="period_id" name="period_id" value="{{ $period_id }}">
    <input type="hidden" id="moveouts_num" name="moveouts_num" value="1">
    <input type="hidden" id="inquiries_num" name="inquiries_num" value="1">
    <input type="hidden" id="cencaps_num" name="cencaps_num" value="1">
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
<script src="{{ asset('assets/js/custom/addaction.js') }}"></script>
@endsection