@extends('layouts.app')

@section('additional_css')

@endsection

@section('contents')

<!-- Button trigger modal-->
<button type="button" class="btn btn-primary dn uploadBtn" data-toggle="modal"
    data-target="#exampleModalCustomScrollable">
    Launch demo modal
</button>

<!-- Modal-->
<div class="modal fade" id="exampleModalCustomScrollable" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">File Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div data-scroll="true" data-height="300">
                    <div class="container mt-5">
                        <form action="{{route('fileUpload')}}" method="post" enctype="multipart/form-data">
                            <h3 class="text-center mb-5">Please upload only "csv" file</h3>
                            @csrf
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <strong>{{ $message }}</strong>
                            </div>
                            @endif

                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="chooseFile">
                                <label class="custom-file-label" for="chooseFile">Select file</label>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                                Upload File
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom" style="margin-top: 30px;">
    @if (Session::get('doesntmatch') != null)
    <h4>{{ Session::get('doesntmatch') }}</h4>
    @endif
    <div class="card-header flex-wrap border-0 pt-6 pb-0">
        <div class="card-title" style="width: 50%;">
            <div class="col-md-6 manageheader">
                <h3 class="card-label">
                    <a href="usermanage">User Management</a>
                </h3>
            </div>
            <div class="col-md-6 manageheader">
                <h3 class="card-label">
                    <a href="reportmanage" class="bb">Report Management</a>
                </h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!--begin: Datatable-->
        <div class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded"
            id="kt_datatable">
            <table class="datatable-table  table-sm table-hover" id="kt_datatable2" style="display: block;">
                <thead class="datatable-head">
                    <tr class="datatable-row sortEmotic" SDType="reportmanageSD" style="left: 0px;">
                        <th data-field="missing" style="width: 5%;" class="datatable-cell-left datatable-cell">
                            #
                        </th>
                        <th data-field="location" style="width: 25%; cursor:pointer" class="datatable-cell">
                            Location
                        </th>
                        <th data-field="date" style="width: 20%; cursor:pointer" class="datatable-cell">
                            Date
                        </th>
                        <th data-field="user" style="width: 15%; cursor:pointer" class="datatable-cell">
                            User
                        </th>
                        <th data-field="missing" style="width: 5%;" class="datatable-cell">
                            Status
                        </th>
                        <th data-field="edit_time" style="width: 15%; cursor:pointer" class="datatable-cell">
                            Edit Time<i class="fas fa-caret-down" style="float:right"></i>
                        </th>
                        <th data-field="what_edit" style="width: 15%; cursor:pointer" class="datatable-cell">
                            What Was Edit
                        </th>
                    </tr>
                </thead>
                <tbody class="datatable-body" id="report_datatable">
                    @foreach ($data as $key => $item)
                        <tr data-row="0" class="datatable-row" style="left: 0px;">
                            <td class="datatable-cell-sorted datatable-cell-left datatable-cell" style="width: 5%;"
                                data-field="RecordID" aria-label="1">
                                <span>
                                    <span class="font-weight-bolder">{{ $item->report_id }}</span>
                                </span>
                            </td>
                            <td data-field="Name" style="width: 25%;" class="datatable-cell">
                                <span>
                                    <div class="font-weight-bolder font-size-lg mb-0">{{ $item->name }}</div>
                                </span>
                            </td>
                            <td data-field="Caption" style="width: 20%;" class="datatable-cell">
                                <span>
                                    <div class="font-weight-bolder text-primary mb-0">{{ $item->caption }}</div>
                                </span>
                            </td>
                            <td data-field="UserName" style="width: 15%;" class="datatable-cell">
                                <span>
                                    <div class="font-weight-bold ">
                                        {{ $item->username }}
                                    </div>
                                </span>
                            </td>
                            <td data-field="Actions" style="width: 5%;" class="datatable-cell">
                                @if(Auth::user()->leveledit > 0)
                                <form action="editaction" method="POST" class="dn">
                                    @csrf
                                    <input type="text" name="community_id" value="{{ $item->community_id }}">
                                    <input type="text" name="period_id" value="{{ $item->period_id }}">
                                    <input type="submit" class="cpeBtn">
                                </form>
                                <span style="overflow: visible; position: relative;display:inline-block;"
                                    data-toggle="tooltip" data-theme="dark" title="Edit">
                                    <a href="{{ route('community_view_edit', ['report_id' => $item->report_id]) }}"
                                        class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2 changeReportBtn"
                                        title="Edit details">
                                        <span class="svg-icon svg-icon-md">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                    <path
                                                        d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                        fill="#000000" fill-rule="nonzero"
                                                        transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                    </path>
                                                    <path
                                                        d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                        fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                </g>
                                            </svg>
                                        </span>
                                    </a>
                                </span>
                                @endif
                            </td>
                            <td data-field="EditTime" style="width: 15%;" class="datatable-cell">
                                <span>
                                    <div class="font-weight-bold ">
                                        {{ $item->edit_time }}
                                    </div>
                                </span>
                            </td>
                            <td data-field="WhatEdit" style="width: 15%;" class="datatable-cell">
                                <span>
                                    <div class="font-weight-bold ">
                                        {{ $item->what_edit }}
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!--end: Datatable-->
    </div>
    <!--end::Body-->
</div>


<script language="javascript">
var header = "edit_time";
var direction = "desc";

$(document).ready(function(){
    $('body').on('click', '#kt_datatable2 th', function () {
        if($(this).attr('data-field') !="missing"){

            header = $(this).attr('data-field');

            var data = $(this).children().first();
            if(data.length == 0){
                $(this).html($(this).text() + ' ' + '<i class="fas " style="float:right"></i>');
                data = $(this).children().first();
            }
            if(!data.hasClass('fa-caret-up') && !data.hasClass('fa-caret-down'))
            {
                $("#kt_datatable2 .fa-caret-up, #kt_datatable2 .fa-caret-down").each(function(){
                    $(this).removeClass('fa-caret-up');
                    $(this).removeClass('fa-caret-down');
                });
                data.addClass('fa-caret-down');
                direction = 'DESC';
            }else{
                if(data.hasClass('fa-caret-up'))
                {
                    $("#kt_datatable2 .fa-caret-up, #kt_datatable2 .fa-caret-down").each(function(){
                        $(this).removeClass('fa-caret-up');
                        $(this).removeClass('fa-caret-down');
                    });

                    data.removeClass('fa-caret-up');
                    data.addClass('fa-caret-down');
                    direction = 'DESC';

                }
                else
                {
                    $("#kt_datatable2 .fa-caret-up, #kt_datatable2 .fa-caret-down").each(function(){
                        $(this).removeClass('fa-caret-up');
                        $(this).removeClass('fa-caret-down');
                    });

                    data.removeClass('fa-caret-down');
                    data.addClass('fa-caret-up');
                    direction = 'ASC';
                }
            }

        }
        $.get("",
            {
                header : header,
                direction : direction,
                row_count : 0
            },
            function(data){
                data = JSON.parse(data);
                if(data != null)
                appendTable(data, 'sort');
            }
        );
    });

    $("#report_datatable").scroll(function(){
        var element = document.getElementById("report_datatable");
        var element = event.target;
        if (element.scrollHeight - element.scrollTop === element.clientHeight)
        {
            var row_count = $("#report_datatable").children().length;
            console.log(row_count);

            $.get("",
                {
                    header : header,
                    direction : direction,
                    row_count : row_count
                },
                function(data){
                    data = JSON.parse(data);
                    if(data != null)
                    appendTable(data, 'scroll');
                }
            );
        }
    });
});
function appendTable(data, type) {
    var content = "";
    for(var i=0; i < data.length; i++){
        content +=`<tr data-row="0" class="datatable-row" style="left: 0px;">
                        <td class="datatable-cell-sorted datatable-cell-left datatable-cell" style="width: 5%;"
                            data-field="RecordID" aria-label="1">
                            <span>
                                <span class="font-weight-bolder">`+ data[i]['report_id'] +`</span>
                            </span>
                        </td>
                        <td data-field="Name" style="width: 25%;" class="datatable-cell">
                            <span>
                                <div class="font-weight-bolder font-size-lg mb-0">`+ data[i]['name'] +`</div>
                            </span>
                        </td>
                        <td data-field="Caption" style="width: 20%;" class="datatable-cell">
                            <span>
                                <div class="font-weight-bolder text-primary mb-0">`+ data[i]['caption'] +`</div>
                            </span>
                        </td>
                        <td data-field="UserName" style="width: 15%;" class="datatable-cell">
                            <span>
                                <div class="font-weight-bold ">
                                    `+ data[i]['username'] +`
                                </div>
                            </span>
                        </td>
                        <td data-field="Actions" style="width: 5%;" class="datatable-cell">
                            @if(Auth::user()->leveledit > 0)
                            <form action="editaction" method="POST" class="dn">
                                @csrf
                                <input type="text" name="community_id" value="`+ data[i]['community_id'] +`">
                                <input type="text" name="period_id" value="`+ data[i]['period_id'] +`">
                                <input type="submit" class="cpeBtn">
                            </form>
                            <span style="overflow: visible; position: relative;display:inline-block;"
                                data-toggle="tooltip" data-theme="dark" title="Edit">
                                <a href="{{ route('community_view_edit')}}?report_id=`+ data[i]['report_id'] +`"
                                    class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2 changeReportBtn"
                                    title="Edit details">
                                    <span class="svg-icon svg-icon-md">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path
                                                    d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                    fill="#000000" fill-rule="nonzero"
                                                    transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) ">
                                                </path>
                                                <path
                                                    d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                    fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                            </g>
                                        </svg>
                                    </span>
                                </a>
                            </span>
                            @endif
                        </td>
                        <td data-field="EditTime" style="width: 15%;" class="datatable-cell">
                            <span>
                                <div class="font-weight-bold ">
                                    `+ data[i]['edit_time'] +`
                                </div>
                            </span>
                        </td>
                        <td data-field="WhatEdit" style="width: 15%;" class="datatable-cell">
                            <span>
                                <div class="font-weight-bold ">
                                    `+ (!!data[i]['what_edit'] == false ? '':data[i]['what_edit']) +`
                                </div>
                            </span>
                        </td>
                    </tr>`;
    }
    if(type == 'scroll'){
        $("#report_datatable").append(content);
    }
    else if(type == 'sort'){
        $("#report_datatable").html(content);
    }
}
</script>
<form method="POST" action="/reportmanage" class="dn">
    @csrf
    <input name="type" id="sortType">
    <input name="sortTypeagain" id="sortTypeagain" value="null">
    <input type="submit" class="clickMeforReload" />
</form>

@endsection

@section('additional_js')
        <script src="{{ asset('assets/js/myEvent.js') }}"></script>
@endsection


