
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
    // report_date_datepicker.fill = function() {
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
    // console.log("date", date);
    // $('#report_date').val(date);

    $("#view_community").selectpicker()

    $("#report_date").on('change', function () {
        var date = $("#report_date").val();
        var community_id = $("#view_community").val();

        document.location.href = base_url + "/report/community_view_goto?date=" + date + "&community_id=" + community_id;
    });

    $("#view_community").on('change', function () {
        var date = $("#report_date").val();
        // var get_date = $("#report_date").datepicker('getDate');

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

        document.location.href = base_url + "/report/community_view_goto?date=" + date + "&community_id=" + community_id;
    });
});