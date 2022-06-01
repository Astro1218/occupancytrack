function disableWeekends($this) {
    var $days = $this.find('.datepicker-days tr').each(function () {
        var $days = $(this).find('.day');
        // disable days
        for (var i = 0; i < 2; i++) {
            if (i == 5) continue;
            $days.eq(i).addClass('old').click(false);
        }
    });
}

function whateditFunc(val) {
    var whatedit = $("#whatedit").val();

    if (whatedit.length == 0) {
        whatedit += val;
    } else if (whatedit.indexOf(val) == -1) {
        whatedit += ',' + val;
    }
    $("#whatedit").attr('value', whatedit);
    $("#whatedit").val(whatedit);
}

function del_cencaps(row_id) {
    var cencaps_count = $("#cencaps_num").val();
    cencaps_count = parseInt(cencaps_count) - 1;

    $("#cencaps_num").val(cencaps_count);

    $("#cencaps_record_" + row_id).remove();
}

function del_inquiry(row_id) {
    var inquiry_count = $("#inquiries_num").val();
    inquiry_count = parseInt(inquiry_count) - 1;

    $("#inquiries_num").val(inquiry_count);

    $("#inquiry_record_" + row_id).remove();
}

function del_moveout(row_id) {
    var moveout_count = $("#moveouts_num").val();
    moveout_count = parseInt(moveout_count) - 1;

    $("#moveouts_num").val(moveout_count);

    $("#moveout_record_" + row_id).remove();
}

function errorAlert(data) {
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr.error(data);
}

$(document).ready(function () {
    // $('#report_date').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var report_date_datepicker = $('#report_date').data('datepicker');

    // disableWeekends(report_date_datepicker.picker);

    // var _fill = report_date_datepicker.fill;
    // report_date_datepicker.fill = function () {
    //     _fill.call(this);
    //     disableWeekends(this.picker);
    // };

    $('body').on('mouseenter', '.table-condensed td:nth-child(6)', function () {
        $(this).next().attr('style', 'background: #eee !important; color: black;');
        for (var i = 0; i < 4; i++) {
            $($(this).parent().next().children()[i]).attr('style', 'background: #eee !important; color: black;');
        }
        $($(this).parent().next().children()[i]).attr('style', 'background: crimson !important; color: black;');
    })

    $('body').on('mouseout', '.table-condensed td:nth-child(6)', function () {
        $(this).next().attr('style', 'background: #fff !important;');
        for (var i = 0; i < 5; i++) {
            $($(this).parent().next().children()[i]).attr('style', 'background: #fff !important; color: #80808f;');
        }
    })

    // var date = $("#report_date").data("start");
    // $('#report_date').datepicker('setDate', new Date(date));
    // var caption = $("#report_date").data("caption");
    // $('#report_date').val(caption);

    $("#view_community").selectpicker()

    $("#report_date").on('change', function () {
        var date = $("#report_date").val();
        var community_id = $("#view_community").val();

        document.location.href = base_url + "/report/community_view_edit?date=" + date + "&community_id=" + community_id;
    });

    $("#view_community").on('change', function () {
        // var get_date = $("#report_date").datepicker('getDate');
        var date = $("#report_date").val();

        // var month = get_date.getMonth() + 1;
        // if (month < 10) {
        //     month = '0' + month;
        // }

        // var day = get_date.getDate();
        // if (day < 10) {
        //     day = '0' + day;
        // }

        // var date = get_date.getFullYear() + '-' + month + '-' + day;
        var community_id = $("#view_community").val();

        document.location.href = base_url + "/report/community_view_edit?date=" + date + "&community_id=" + community_id;
    });


    $("[id^='building_name']").selectpicker();
    $("[id^='move_type']").selectpicker();
    $("[id^='inquiry_type']").selectpicker();

    $("#save_btn").on('click', function () {
        // if ($("#cencaps_num").val() == 0) {
        //     errorAlert('Cencaps data is blanked');
        // }
        // else if ($("#moveouts_num").val() == 0) {
        //     errorAlert('Moveout data is blanked');
        // }
        // else {
        $("#report_form").submit();
        // }
    });


    $("#add_cencaps_btn").on('click', function () {
        var cencaps_count = $("#cencaps_num").val();
        cencaps_count = parseInt(cencaps_count) + 1;

        $("#cencaps_num").val(cencaps_count);

        var building_html = '';
        for (var i = 0; i < buildingsData.length; i++) {
            building_html += '<option value="' + buildingsData[i].id + '">' + buildingsData[i].name + '</option>';
        }

        var html = '<tr id="cencaps_record_' + cencaps_count + '">' +
            '<td class="w-30">' +
            '<select class="form-control selectpicker" name="building_name_' + cencaps_count + '" id="building_name_' + cencaps_count + '">' +
            building_html +
            '</select>' +
            '</td>' +
            '<td class="w-30">' +
            '<input type="text" value="" name="census_' + cencaps_count + '" class="form-control form-control-solid census" required>' +
            '</td>' +
            '<td class="w-30">' +
            '<input type="text" value="" name="capacity_' + cencaps_count + '" class="form-control form-control-solid capacity" required>' +
            '</td>' +
            '<td class="w-10">' +
            '<a class="btn btn-sm btn-clean btn-icon" id="del_cencaps_btn_' + cencaps_count + '" href="javascript:;" onclick="del_cencaps(' + cencaps_count + ')" title="Delete">' +
            '<span><i class="far fa-times-circle"></i></span>' +
            '</a>' +
            '</td>' +
            '</tr>';



        $(".cencaps_body").append(html);
        $("#building_name_" + cencaps_count).selectpicker();
    });


    $("#add_inquiry_btn").on('click', function () {
        var inquiry_count = $("#inquiries_num").val();
        inquiry_count = parseInt(inquiry_count) + 1;

        $("#inquiries_num").val(inquiry_count);

        var inquiry_html = '';

        if (company_id == "1") {
            inquiry_html = '<input type="text" id="inquiry_type_' + inquiry_count + '" name="inquiry_type_' + inquiry_count + '" value="" class="form-control form-control-solid" required>';
        }
        else {
            inquiry_html = '<select class="form-control selectpicker" name="inquiry_type_' + inquiry_count + '" id="inquiry_type_' + inquiry_count + '">' +
                '<option value="DI/DEP CALLS">DI/DEP CALLS</option>' +
                '<option value="COMM VISITS">COMM VISITS</option>' +
                '</select>';
        }

        var html = '<tr id="inquiry_record_' + inquiry_count + '">' +
            '<td class="w-30">' + inquiry_html +
            '</td>' +
            '<td class="w-30">' +
            '<input type="text" value="" name="inquiry_' + inquiry_count + '" class="form-control form-control-solid" required>' +
            '</td>' +
            '<td class="w-10">' +
            '<a class="btn btn-sm btn-clean btn-icon" id="del_inquiry_btn_' + inquiry_count + '" href="javascript:;" onclick="del_inquiry(' + inquiry_count + ')" title="Delete">' +
            '<span><i class="far fa-times-circle"></i></span>' +
            '</a>' +
            '</td>' +
            '</tr>';



        $(".inquiry_body").append(html);
        if (company_id == "2") {
            $("#inquiry_type_" + inquiry_count).selectpicker();
        }
    });


    $("#add_moveout_btn").on('click', function () {
        var moveout_count = $("#moveouts_num").val();
        moveout_count = parseInt(moveout_count) + 1;

        $("#moveouts_num").val(moveout_count);

        var moveout_html = '';

        if (company_id == "1") {
            moveout_html = '<input type="text" id="move_type_' + moveout_count + '" name="move_type_' + moveout_count + '" value="" class="form-control form-control-solid" required>';
        }
        else {
            moveout_html = '<select class="form-control selectpicker" name="move_type_' + moveout_count + '" id="move_type_' + moveout_count + '">' +
                '<option value="SPEC DEP">SPEC DEP</option>' +
                '<option value="GEN DEP">GEN DEP</option>' +
                '</select>';
        }

        var html = '<tr id="moveout_record_' + moveout_count + '">' +
            '<td class="w-30">' + moveout_html +
            '</td>' +
            '<td class="w-30">' +
            '<input type="text" value="" name="move_' + moveout_count + '" class="form-control form-control-solid" required>' +
            '</td>' +
            '<td class="w-10">' +
            '<a class="btn btn-sm btn-clean btn-icon" id="del_moveout_btn_' + moveout_count + '" href="javascript:;" onclick="del_moveout(' + moveout_count + ')" title="Delete">' +
            '<span><i class="far fa-times-circle"></i></span>' +
            '</a>' +
            '</td>' +
            '</tr>';



        $(".moveout_body").append(html);
        if (company_id == "2") {
            $("#move_type_" + moveout_count).selectpicker();
        }
    });


});
