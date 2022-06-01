var primary = "#6993FF";
var success = "#1BC5BD";
var info = "#8950FC";
var warning = "#FFA800";
var danger = "#F64E60";
var realFlag = false;
var mainDateObj = [];
var mainVal = ["", "", "", "", "", "", "", "", "", ""];
var eTypeV = "";

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

function filter_btn(obj) {
    $(".dashboard-btn-group button.active").removeClass('active');
    obj.addClass('active');

    var filter_option = get_filter_option();

    // var get_date = $("#community_view_from_btn").datepicker('getDate');
    var date = $("#community_view_from_btn").val();

    // var month = get_date.getMonth() + 1;
    // if (month < 10) {
    //     month = '0' + month;
    // }

    // var day = get_date.getDate();
    // if (day < 10) {
    //     day = '0' + day;
    // }

    // var date = get_date.getFullYear() + '-' + month + '-' + day;

    datatableDraw(date, filter_option);
}

function get_filter_option() {
    var btn_obj = $(".dashboard-btn-group button.active");
    var filter_option = btn_obj.attr('id');
    return filter_option;
}

/** begin updated by majesty **/
function datatableDraw(date, flag) {
    $("#table-wrapper").empty();

    // console.log(date);

    var obj = {
        _token: $("[name='_token']").val(),
        flag: flag,
        date: date
    }

    $.ajax({
        url: '/getdatatableinfodata',
        type: 'POST',
        data: obj,
        success: function (html) {
            $("#table-wrapper").html(html);
            //$("#main_report_table").DataTable();
        }
    });
}

/** end updated by majesty **/

function chartDraw(dateFrom, dateTo, community_id) {
    try {
        var obj = {
            _token: $("[name='_token']").val()
        }

        if (community_id != undefined) {
            obj['community_id'] = community_id;
        }

        if (dateFrom != undefined && dateTo != undefined) {
            obj['from'] = dateFrom;
            obj['to'] = dateTo;
        } else if (dateFrom != undefined || dateTo != undefined) {
            obj['date'] = dateFrom || dateTo;
        } else {
            $("#chart_12").html("");
            $("#chart_3").html("");
            return;
        }
        $.ajax({
            url: '/getchartinfodata',
            type: 'POST',
            data: obj,
            success: function (data) {
                $("#chart_12").html("");
                $("#chart_3").html("");
                var currentCensus = data['currentCensus'];
                var buildings = data['buildings'];
                if (data['role'] == 'm1') {
                    var seriesArr = [],
                        buildingName = [];

                    var total_occ = 0
                    for (var i = 0; i < currentCensus.length; i++) {
                        total_occ += currentCensus[i].census;
                    }

                    for (var i = 0; i < buildings.length; i++) {
                        buildingName.push(buildings[i]['name']);

                        for (var j = 0; j < currentCensus.length; j++) {
                            if (buildings[i]['name'] == currentCensus[j].name) {
                                seriesArr.push(currentCensus[j].census);
                                break;
                            }
                        }
                    }

                    var e = {
                        series: seriesArr,
                        chart: {
                            width: 380,
                            type: "pie"
                        },
                        labels: buildingName,
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: "left"
                                }
                            }
                        }],
                        colors: [primary, success, danger, info]
                    };

                    new ApexCharts(document.querySelector("#chart_12"), e).render();

                    var censusSum = [0, 0, 0, 0];
                    var capacitySum = [0, 0, 0, 0];

                    for (var i = 0; i < currentCensus.length; i++) {
                        if (currentCensus[i]['building_id'] == 1) {
                            censusSum[0] += currentCensus[i]['capacity'];
                            capacitySum[0] += currentCensus[i]['census'];
                        }
                        else if (currentCensus[i]['building_id'] == 2) {
                            censusSum[1] += currentCensus[i]['capacity'];
                            capacitySum[1] += currentCensus[i]['census'];
                        }
                        else if (currentCensus[i]['building_id'] == 3) {
                            censusSum[2] += currentCensus[i]['capacity'];
                            capacitySum[2] += currentCensus[i]['census'];
                        }
                        else if (currentCensus[i]['building_id'] == 4) {
                            censusSum[3] += currentCensus[i]['capacity'];
                            capacitySum[3] += currentCensus[i]['census'];
                        }
                    }

                    var census_data = new Array;
                    var capacity_data = new Array;

                    for (var i = 0; i < buildings.length; i++) {

                        census_data[i] = {
                            x: buildings[i].name,
                            y: 0
                        };

                        capacity_data[i] = {
                            x: buildings[i].name,
                            y: 0
                        };

                        for (var j = 0; j < currentCensus.length; j++) {
                            if (buildings[i].name == currentCensus[j].name) {
                                census_data[i].y = currentCensus[j].census;
                                capacity_data[i].y = currentCensus[j].capacity;
                                break;
                            }
                        }
                    }

                    var e1 = {
                        series: [{
                            name: "OCC",
                            data: census_data
                        }, {
                            name: "Open Unit",
                            data: capacity_data
                        }],
                        chart: {
                            type: "bar",
                            height: 500
                        },
                        plotOptions: {
                            bar: {
                                horizontal: !1,
                                columnWidth: "55%",
                                endingShape: "rounded"
                            }
                        },
                        dataLabels: {
                            enabled: !1
                        },
                        stroke: {
                            show: !0,
                            width: 2,
                            colors: ["transparent"]
                        },
                        xaxis: {
                            categories: []
                        },
                        yaxis: {
                            title: {
                                text: ""
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (e1) {
                                    return e1
                                }
                            }
                        },
                        colors: [warning, primary]
                    };
                    new ApexCharts(document.querySelector("#chart_3"), e1).render()
                } else if (data['role'] == 'm2') {
                    var censusSum1 = 0,
                        capacitySum1 = 0;

                    // console.log('aa');

                    for (var i = 0; i < currentCensus.length; i++) {
                        censusSum1 += parseInt(currentCensus[i]['census']);
                        capacitySum1 += parseInt(currentCensus[i]['capacity']) - parseInt(currentCensus[i]['census']);
                    }

                    var e = {
                        series: [censusSum1, capacitySum1],
                        chart: {
                            width: 380,
                            type: "pie"
                        },
                        labels: ['OCC', 'Open Unit'],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: "left"
                                }
                            }
                        }],
                        colors: [primary, success, danger, info]
                    };
                    new ApexCharts(document.querySelector("#chart_12"), e).render();
                    var censusSum = [0, 0, 0, 0];
                    var capacitySum = [0, 0, 0, 0];

                    for (var i = 0; i < currentCensus.length; i++) {
                        if (currentCensus[i]['building_id'] == 1) {
                            censusSum[0] += currentCensus[i]['capacity'];
                            capacitySum[0] += currentCensus[i]['census'];
                        }
                        else if (currentCensus[i]['building_id'] == 2) {
                            censusSum[1] += currentCensus[i]['capacity'];
                            capacitySum[1] += currentCensus[i]['census'];
                        }
                        else if (currentCensus[i]['building_id'] == 3) {
                            censusSum[2] += currentCensus[i]['capacity'];
                            capacitySum[2] += currentCensus[i]['census'];
                        }
                        else if (currentCensus[i]['building_id'] == 4) {
                            censusSum[3] += currentCensus[i]['capacity'];
                            capacitySum[3] += currentCensus[i]['census'];
                        }
                    }

                    var census_data = new Array;
                    var capacity_data = new Array;

                    for (var i = 0; i < buildings.length; i++) {

                        census_data[i] = {
                            x: buildings[i].name,
                            y: 0
                        };

                        capacity_data[i] = {
                            x: buildings[i].name,
                            y: 0
                        };

                        for (var j = 0; j < currentCensus.length; j++) {
                            if (buildings[i].name == currentCensus[j].name) {
                                census_data[i].y = currentCensus[j].census;
                                capacity_data[i].y = currentCensus[j].capacity;
                                break;
                            }
                        }
                    }

                    var e1 = {
                        series: [{
                            name: "Total Unit",
                            data: census_data
                        }, {
                            name: "OCC",
                            data: capacity_data
                        }],
                        chart: {
                            type: "bar",
                            height: 500
                        },
                        plotOptions: {
                            bar: {
                                horizontal: !1,
                                columnWidth: "10%",
                                endingShape: "rounded"
                            }
                        },
                        dataLabels: {
                            enabled: !1
                        },
                        stroke: {
                            show: !0,
                            width: 2,
                            colors: ["transparent"]
                        },
                        xaxis: {
                            categories: []
                        },
                        yaxis: {
                            title: {
                                text: ""
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (e1) {
                                    return e1
                                }
                            }
                        },
                        colors: [warning, primary]
                    };
                    new ApexCharts(document.querySelector("#chart_3"), e1).render()
                }

            }
        })
        realFlag = true;
    } catch (error) { }
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

function successAlert(data) {
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

    toastr.success(data);
}

function community_view(date, community_id) {

    $.ajax({
        type: 'POST',
        url: base_url + '/report/community_view',
        data: {
            _token: $("[name='_token']").val(),
            community_id: community_id,
            date: date
        },
        dataType: 'json',
        success: function (response) {
            var period_data = response.period_data;
            var user_data = response.user_data;
            var community_flag = response.community_flag;

            if (period_data == null) {
                // var caption = $("#community_view_from_btn").data('caption')
                // $("#community_view_from_btn").val(caption);
                errorAlert('not existed period');
            }
            else {

                if (response.add_flag == 0 && response.report_id != "") {

                    if (user_data.levelreport == 1 && community_flag == 1 || user_data.levelreport > 1) {
                        $("#community_view_go").removeClass('btn-disabled');
                        $("#community_view_go").removeAttr("disabled");
                        $("#community_view_go").attr('href', base_url + '/report/community_view_goto?report_id=' + response.report_id);
                    }
                    else {
                        $("#community_view_go").removeClass('btn-disabled');
                        $("#community_view_go").addClass('btn-disabled');
                        $("#community_view_go").attr("disabled", true);
                        $("#community_view_go").attr('href', "javascript:void(0)");
                    }

                    $("#addBtn").attr("href", "javascript:void(0)");
                    $("#addBtn").hide();

                    if (user_data.leveledit == 1 && community_flag == 1 || user_data.leveledit > 1) {
                        $("#editBtn").removeClass('btn-disabled');
                        $("#editBtn").removeAttr("disabled");
                        $("#editBtn").attr('href', base_url + '/report/community_view_edit?report_id=' + response.report_id);
                        $("#editBtn").show();
                    }
                    else {
                        $("#editBtn").removeClass('btn-disabled');
                        $("#editBtn").addClass('btn-disabled');
                        $("#editBtn").attr("disabled", true);
                        $("#editBtn").attr('href', "javascript:void(0)");
                        $("#editBtn").show();
                    }
                }
                else {
                    $("#community_view_go").removeClass('btn-disabled');
                    $("#community_view_go").addClass('btn-disabled');
                    $("#community_view_go").attr("disabled", true);
                    $("#community_view_go").attr('href', "javascript:void(0)");

                    $("#editBtn").attr('href', "javascript:void(0)");
                    $("#editBtn").hide();

                    if (user_data.leveladd == 1 && community_flag == 1 || user_data.leveladd > 1) {
                        $("#addBtn").removeClass('btn-disabled');
                        $("#addBtn").removeAttr("disabled");
                        $("#addBtn").attr('href', base_url + '/report/community_view_add?community_id=' + community_id + '&period_id=' + period_data.id);
                        $("#addBtn").show();
                    }
                    else {
                        $("#addBtnn").removeClass('btn-disabled');
                        $("#addBtn").addClass('btn-disabled');
                        $("#addBtn").attr("disabled", true);
                        $("#addBtn").attr("href", "javascript:void(0)");
                        $("#addBtn").show();
                    }
                }

                // $("#community_view_from_btn").data('id', period_data.id);
                // $("#community_view_from_btn").data('start', period_data.starting);
                // $("#community_view_from_btn").data('caption', period_data.caption);

                // $("#community_view_from_btn").val(period_data.id);
            }
        }
    })
}

function community_trend(from, to, community_id) {
    $.ajax({
        type: 'POST',
        url: base_url + '/report/community_trend',
        dataType: 'json',
        data: {
            _token: $("[name='_token']").val(),
            community_id: community_id,
            from: from,
            to: to
        },
        success: function (response) {
            var from_period_data = response.from_period_data;
            var to_period_data = response.to_period_data;
            var user_data = response.user_data;
            var community_flag = response.community_flag;

            if (from_period_data == null) {
                // var caption = $("#community_trend_from_btn").data('caption')
                // $("#community_trend_from_btn").val(caption);

                // errorAlert('not existed from period');
            }
            else if (to_period_data == null) {
                // var caption = $("#community_trend_to_btn").data('caption')
                // $("#community_trend_to_btn").val(caption);

                // errorAlert('not existed to period');
            }
            else {

                if (response.flag == 1) {
                    if (user_data.levelreport == 1 && community_flag == 1 || user_data.levelreport > 1) {
                        $("#community_trend_go").removeClass('btn-disabled');
                        $("#community_trend_go").removeAttr("disabled");
                        $("#community_trend_go").attr('href', base_url + '/report/community_trend_goto?community_id=' + community_id + '&from=' + from + '&to=' + to);
                    }
                    else {
                        $("#community_trend_go").removeClass('btn-disabled');
                        $("#community_trend_go").addClass('btn-disabled');
                        $("#community_trend_go").attr("disabled", true);
                        $("#community_trend_go").attr('href', "javascript:void(0)");
                    }
                }
                else {
                    $("#community_trend_go").removeClass('btn-disabled');
                    $("#community_trend_go").addClass('btn-disabled');
                    $("#community_trend_go").attr("disabled", true);
                    $("#community_trend_go").attr('href', "javascript:void(0)");
                }

                // $("#community_trend_from_btn").data('id', from_period_data.id);
                // $("#community_trend_from_btn").data('start', from_period_data.starting);
                // $("#community_trend_from_btn").data('caption', from_period_data.caption);

                // $("#community_trend_from_btn").val(from_period_data.caption);

                // $("#community_trend_to_btn").data('id', to_period_data.id);
                // $("#community_trend_to_btn").data('start', to_period_data.starting);
                // $("#community_trend_to_btn").data('caption', to_period_data.caption);

                // $("#community_trend_to_btn").val(to_period_data.caption);
            }
        }
    })
}

$(document).ready(function () {

    // init 
    var alert = $("#alert").val();
    if (alert != '') {
        successAlert(alert);
    }

    // $('#community_view_from_btn, #community_trend_from_btn, #community_trend_to_btn, #company_view_from_btn, #company_trend_from_btn, #company_trend_to_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // $('#community_view_from_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var community_view_from_datepicker = $('#community_view_from_btn').data('datepicker');

    // disableWeekends(community_view_from_datepicker.picker);

    // var _fill = community_view_from_datepicker.fill;
    // community_view_from_datepicker.fill = function () {
    //     _fill.call(this);
    //     disableWeekends(this.picker);
    // };

    // $('#community_trend_from_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var community_trend_from_datepicker = $('#community_trend_from_btn').data('datepicker');

    // disableWeekends(community_trend_from_datepicker.picker);

    // var _fill = community_trend_from_datepicker.fill;
    // community_trend_from_datepicker.fill = function () {
    //     _fill.call(this);
    //     disableWeekends(this.picker);
    // };

    // $('#community_trend_to_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var community_trend_to_datepicker = $('#community_trend_to_btn').data('datepicker');

    // disableWeekends(community_trend_to_datepicker.picker);

    // var _fill = community_trend_to_datepicker.fill;
    // community_trend_to_datepicker.fill = function () {
    //     _fill.call(this);
    //     disableWeekends(this.picker);
    // };

    // $('#company_view_from_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var company_view_from_datepicker = $('#company_view_from_btn').data('datepicker');

    // disableWeekends(company_view_from_datepicker.picker);

    // var _fill = company_view_from_datepicker.fill;
    // company_view_from_datepicker.fill = function () {
    //     _fill.call(this);
    //     disableWeekends(this.picker);
    // };

    // $('#company_trend_from_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var company_trend_from_datepicker = $('#company_trend_from_btn').data('datepicker');

    // disableWeekends(company_trend_from_datepicker.picker);

    // var _fill = company_trend_from_datepicker.fill;
    // company_trend_from_datepicker.fill = function () {
    //     _fill.call(this);
    //     disableWeekends(this.picker);
    // };

    // $('#company_trend_to_btn').datepicker({
    //     format: 'yyyy-mm-dd',
    //     endDate: '+1d',
    //     datesDisabled: '+1d',
    //     //daysOfWeekDisabled: [0, 1, 2, 3, 4, 6, 7],  //Disable sunday
    //     autoclose: true,
    // });

    // var company_trend_to_datepicker = $('#company_trend_to_btn').data('datepicker');

    // disableWeekends(company_trend_to_datepicker.picker);

    // var _fill = company_trend_to_datepicker.fill;
    // company_trend_to_datepicker.fill = function () {
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


    // $('#community_view_from_btn, #community_trend_from_btn, #community_trend_to_btn, #company_view_from_btn, #company_trend_from_btn, #company_trend_to_btn').on('dp.change', function (e) {
    //     value = $(this).val();
    //     firstDate = moment(value, "yyyy-mm-dd").day(0).format("yyyy-mm-dd");
    //     lastDate =  moment(value, "yyyy-mm-dd").day(6).format("yyyy-mm-dd");
    //     $(this).val(firstDate + "   -   " + lastDate);
    // });

    // var date = $("#community_view_from_btn").data("start");
    // $('#community_view_from_btn, #community_trend_from_btn, #community_trend_to_btn, #company_view_from_btn, #company_trend_from_btn, #company_trend_to_btn').datepicker('setDate', new Date(date));
    // var caption = $("#community_view_from_btn").data("caption");
    // $('#community_view_from_btn, #community_trend_from_btn, #community_trend_to_btn, #company_view_from_btn, #company_trend_from_btn, #company_trend_to_btn').val(caption);

    $("#view_community").selectpicker();
    $("#community_trend_from_btn").selectpicker();
    $("#community_trend_to_btn").selectpicker();

    var fromV = $("#community_view_from_btn").val();
    var toV = $("#community_view_from_btn").val();

    var community_id = $("#view_community").val()

    chartDraw(fromV, toV, community_id);

    var filter_option = get_filter_option();
    datatableDraw(fromV, filter_option);


    $("body").on('click', '#default_btn, #full_percent_btn, #more_year_btn, #less_year_btn, #pre_btn, #westen_btn', function () {
        var obj = $(this);
        filter_btn(obj);
    });

    /**** updated by majesty  ****/

    // $('body').click(function () {
    //     var community_view_from_caption = $("#community_view_from_btn").data('caption');
    //     $("#community_view_from_btn").attr('value', community_view_from_caption);

    //     var community_trend_from_caption = $("#community_trend_from_btn").data('caption');
    //     $("#community_trend_from_btn").attr('value', community_trend_from_caption);

    //     var comnunity_trend_to_caption = $("#community_trend_to_btn").data('caption');
    //     $("#community_trend_to_btn").attr('value', comnunity_trend_to_caption);

    //     var company_view_from_caption = $("#company_view_from_btn").data('caption');
    //     $("#company_view_from_btn").attr('value', company_view_from_caption);

    //     var company_trend_from_caption = $("#company_trend_from_btn").data('caption');
    //     $("#company_trend_from_btn").attr('value', company_trend_from_caption);

    //     var company_trend_to_caption = $("#company_trend_to_btn").data('caption');
    //     $("#company_trend_to_btn").attr('value', company_trend_to_caption);
    // });



    $("#community_view_from_btn").on('change', function () {
        var date = $("#community_view_from_btn").val();
        var community_id = $("#view_community").val();

        community_view(date, community_id);
        chartDraw(date, null, community_id);
        var filter_option = get_filter_option();
        datatableDraw(date, filter_option);
    });

    $("#view_community").on('change', function () {
        // var get_date = $("#community_view_from_btn").datepicker('getDate');
        var date = $("#community_view_from_btn").val();

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
        community_view(date, community_id);
        chartDraw(date, null, community_id);
    });

    $("#community_trend_from_btn").on('change', function () {
        // var from_date = $("#community_trend_from_btn").datepicker('getDate');

        // var from_month = from_date.getMonth() + 1;
        // if (from_month < 10) {
        //     from_month = '0' + from_month;
        // }

        // var from_day = from_date.getDate();
        // if (from_day < 10) {
        //     from_day = '0' + from_day;
        // }

        // var from = from_date.getFullYear() + '-' + from_month + '-' + from_day;

        // var to_date = $("#community_trend_to_btn").datepicker('getDate');
        // console.log(to_date);

        // var to_month = to_date.getMonth() + 1;
        // if (to_month < 10) {
        //     to_month = '0' + to_month;
        // }

        // var to_day = to_date.getDate();
        // if (to_day < 10) {
        //     to_day = '0' + to_day;
        // }

        // var to = to_date.getFullYear() + '-' + to_month + '-' + to_day;

        var from = $("#community_trend_from_btn").selectpicker('val');
        var to = $("#community_trend_to_btn").selectpicker('val');
        if (from > to) {
            errorAlert('Please select correct period.');
            return;
        }
        var community_id = $("#trend_community").val();

        // var from_date = new Date(from);
        // var to_date = new Date(to);

        // if (from_date > to_date) {
        //     var temp = from;
        //     from = to;
        //     to = temp;
        // }

        community_trend(from, to, community_id);
        chartDraw(from, to, community_id);
    });

    $("#community_trend_to_btn").on('change', function () {
        // var from_date = $("#community_trend_from_btn").datepicker('getDate');

        var from = $("#community_trend_from_btn").selectpicker('val');
        var to = $("#community_trend_to_btn").selectpicker('val');

        if (from > to) {
            errorAlert('Please select correct period.');
            return;
            // from = to;
            // // $("#community_trend_from_btn").selectpicker('val', to);
            // $('#community_trend_from_btn').selectpicker('destroy');
            // $("#community_trend_from_btn").val(to);
            // $('#community_trend_from_btn').selectpicker();
        }

        // var from_month = from_date.getMonth() + 1;
        // if (from_month < 10) {
        //     from_month = '0' + from_month;
        // }

        // var from_day = from_date.getDate();
        // if (from_day < 10) {
        //     from_day = '0' + from_day;
        // }

        // var from = from_date.getFullYear() + '-' + from_month + '-' + from_day;

        // var to_date = $("#community_trend_to_btn").datepicker('getDate');
        // console.log(to_date);

        // var to_month = to_date.getMonth() + 1;
        // if (to_month < 10) {
        //     to_month = '0' + to_month;
        // }

        // var to_day = to_date.getDate();
        // if (to_day < 10) {
        //     to_day = '0' + to_day;
        // }

        // var to = to_date.getFullYear() + '-' + to_month + '-' + to_day;


        var community_id = $("#trend_community").val();

        // var from_date = new Date(from);
        // var to_date = new Date(to);

        // if (from_date > to_date) {
        //     var temp = from;
        //     from = to;
        //     to = temp;
        // }

        community_trend(from, to, community_id);
        chartDraw(from, to, community_id);
    });

    $("#trend_community").on('change', function () {
        // var from_date = $("#community_trend_from_btn").datepicker('getDate');

        var from = $("#community_trend_from_btn").val();
        var to = $("#community_trend_to_btn").val();
        if (from > to) {
            errorAlert('Please select correct period.');
            return;
        }
        // var from_month = from_date.getMonth() + 1;
        // if (from_month < 10) {
        //     from_month = '0' + from_month;
        // }

        // var from_day = from_date.getDate();
        // if (from_day < 10) {
        //     from_day = '0' + from_day;
        // }

        // var from = from_date.getFullYear() + '-' + from_month + '-' + from_day;

        // var to_date = $("#community_trend_to_btn").datepicker('getDate');

        // var to_month = to_date.getMonth() + 1;
        // if (to_month < 10) {
        //     to_month = '0' + to_month;
        // }

        // var to_day = to_date.getDate();
        // if (to_day < 10) {
        //     to_day = '0' + to_day;
        // }

        // var to = to_date.getFullYear() + '-' + to_month + '-' + to_day;


        var community_id = $("#trend_community").val();

        // var from_date = new Date(from);
        // var to_date = new Date(to);

        // if (from_date > to_date) {
        //     var temp = from;
        //     from = to;
        //     to = temp;
        // }

        community_trend(from, to, community_id);
        chartDraw(from, to, community_id);
    });


    $("#company_view_from_btn").on('change', function () {
        var date = $(this).val();

        $.ajax({
            type: 'POST',
            url: base_url + '/report/company_view',
            data: {
                _token: $("[name='_token']").val(),
                date: date
            },
            dataType: 'json',
            success: function (response) {
                var period_data = response.period_data;

                if (period_data == null) {
                    // var caption = $("#company_view_from_btn").data('caption')
                    // $("#company_view_from_btn").val(caption);
                    // errorAlert('not existed period');
                }
                else {
                    if (response.flag == 1) {
                        $("#company_view_go").removeClass('btn-disabled');
                        $("#company_view_go").removeAttr("disabled");
                        $("#company_view_go").attr('href', base_url + '/report/company_view_goto?date=' + date);
                    }
                    else {
                        $("#company_view_go").removeClass('btn-disabled');
                        $("#company_view_go").addClass('btn-disabled');
                        $("#company_view_go").attr("disabled", true);
                        $("#company_view_go").attr('href', "javascript:void(0)");
                    }

                    // $("#company_view_from_btn").data('id', period_data.id);
                    // $("#company_view_from_btn").data('start', period_data.starting);
                    // $("#company_view_from_btn").data('caption', period_data.caption);

                    // $("#company_view_from_btn").val(period_data.caption);

                    // $("#company_view_from_btn").val(period_data.id);
                }
            }
        })

        chartDraw(date, null, null);
    });

    $("#company_trend_from_btn").on('change', function () {
        // var from_date = $("#company_trend_from_btn").datepicker('getDate');
        var from = $("#company_trend_from_btn").val();
        var to = $("#company_trend_to_btn").val();

        if (from > to) {
            errorAlert('Please select correct period.');
            return;
        }
        // var from_month = from_date.getMonth() + 1;
        // if (from_month < 10) {
        //     from_month = '0' + from_month;
        // }

        // var from_day = from_date.getDate();
        // if (from_day < 10) {
        //     from_day = '0' + from_day;
        // }

        // var from = from_date.getFullYear() + '-' + from_month + '-' + from_day;

        // var to_date = $("#company_trend_to_btn").datepicker('getDate');

        // var to_month = to_date.getMonth() + 1;
        // if (to_month < 10) {
        //     to_month = '0' + to_month;
        // }

        // var to_day = to_date.getDate();
        // if (to_day < 10) {
        //     to_day = '0' + to_day;
        // }

        // var to = to_date.getFullYear() + '-' + to_month + '-' + to_day;

        // var from_date = new Date(from);
        // var to_date = new Date(to);

        // if (from_date > to_date) {
        //     var temp = from;
        //     from = to;
        //     to = temp;
        // }

        $.ajax({
            type: 'POST',
            url: base_url + '/report/company_trend',
            data: {
                _token: $("[name='_token']").val(),
                from: from,
                to: to
            },
            dataType: 'json',
            success: function (response) {
                var from_period_data = response.from_period_data;
                var to_period_data = response.to_period_data;

                if (from_period_data == null) {
                    // var caption = $("#company_trend_from_btn").data('caption')
                    // $("#company_trend_from_btn").val(caption);

                    // errorAlert('not existed from period');
                }
                else if (to_period_data == null) {
                    //     // var caption = $("#company_trend_to_btn").data('caption')
                    //     // $("#company_trend_to_btn").val(caption);

                    //     errorAlert('not existed to period');
                }
                else {
                    if (response.flag == 1) {
                        $("#company_trend_go").removeClass('btn-disabled');
                        $("#company_trend_go").removeAttr("disabled");
                        $("#company_trend_go").attr('href', base_url + '/report/company_trend_goto?from=' + from + '&to=' + to);
                    }
                    else {
                        $("#company_trend_go").removeClass('btn-disabled');
                        $("#company_trend_go").addClass('btn-disabled');
                        $("#company_trend_go").attr("disabled", true);
                        $("#company_trend_go").attr('href', "javascript:void(0)");
                    }

                    // $("#company_trend_from_btn").data('id', from_period_data.id);
                    // $("#company_trend_from_btn").data('start', from_period_data.starting);
                    // $("#company_trend_from_btn").data('caption', from_period_data.caption);

                    // $("#company_trend_from_btn").val(from_period_data.caption);
                    // $("#company_trend_from_btn").val(from_period_data.id);

                    // $("#company_trend_to_btn").data('id', to_period_data.id);
                    // $("#company_trend_to_btn").data('start', to_period_data.starting);
                    // $("#company_trend_to_btn").data('caption', to_period_data.caption);

                    // $("#company_trend_to_btn").val(to_period_data.caption);
                    // $("#company_trend_to_btn").val(to_period_data.id);
                }
            }
        })

        chartDraw(from, to, null);
    });

    $("#company_trend_to_btn").on('change', function () {
        var from = $("#company_trend_from_btn").val();
        var to = $("#company_trend_to_btn").val();

        if (from > to) {
            errorAlert('Please select correct period.');
            return;
        }
        // var from_date = $("#company_trend_from_btn").datepicker('getDate');

        // var from_month = from_date.getMonth() + 1;
        // if (from_month < 10) {
        //     from_month = '0' + from_month;
        // }

        // var from_day = from_date.getDate();
        // if (from_day < 10) {
        //     from_day = '0' + from_day;
        // }

        // var from = from_date.getFullYear() + '-' + from_month + '-' + from_day;

        // var to_date = $("#company_trend_to_btn").datepicker('getDate');

        // var to_month = to_date.getMonth() + 1;
        // if (to_month < 10) {
        //     to_month = '0' + to_month;
        // }

        // var to_day = to_date.getDate();
        // if (to_day < 10) {
        //     to_day = '0' + to_day;
        // }

        // var to = to_date.getFullYear() + '-' + to_month + '-' + to_day;

        // var from_date = new Date(from);
        // var to_date = new Date(to);

        // if (from_date > to_date) {
        //     var temp = from;
        //     from = to;
        //     to = temp;
        // }

        $.ajax({
            type: 'POST',
            url: base_url + '/report/company_trend',
            data: {
                _token: $("[name='_token']").val(),
                from: from,
                to: to
            },
            dataType: 'json',
            success: function (response) {
                var from_period_data = response.from_period_data;
                var to_period_data = response.to_period_data;

                if (from_period_data == null) {
                    // var caption = $("#company_trend_from_btn").data('caption')
                    // $("#company_trend_from_btn").val(caption);

                    // errorAlert('not existed from period');
                }
                else if (to_period_data == null) {
                    // var caption = $("#company_trend_to_btn").data('caption')
                    // $("#company_trend_to_btn").val(caption);

                    // errorAlert('not existed to period');
                }
                else {
                    if (response.flag == 1) {
                        $("#company_trend_go").removeClass('btn-disabled');
                        $("#company_trend_go").removeAttr("disabled");
                        $("#company_trend_go").attr('href', base_url + '/report/company_trend_goto?from=' + from + '&to=' + to);
                    }
                    else {
                        $("#company_trend_go").removeClass('btn-disabled');
                        $("#company_trend_go").addClass('btn-disabled');
                        $("#company_trend_go").attr("disabled", true);
                        $("#company_trend_go").attr('href', "javascript:void(0)");
                    }

                    // $("#company_trend_from_btn").data('id', from_period_data.id);
                    // $("#company_trend_from_btn").data('start', from_period_data.starting);
                    // $("#company_trend_from_btn").data('caption', from_period_data.caption);

                    // $("#company_trend_from_btn").val(from_period_data.caption);
                    // $("#company_trend_from_btn").val(from_period_data.id);

                    // $("#company_trend_to_btn").data('id', to_period_data.id);
                    // $("#company_trend_to_btn").data('start', to_period_data.starting);
                    // $("#company_trend_to_btn").data('caption', to_period_data.caption);

                    // $("#company_trend_to_btn").val(to_period_data.caption);
                    // $("#company_trend_to_btn").val(to_period_data.id);
                }
            }
        })

        chartDraw(from, to, null);
    });


    // $("#prior_ye_occ, .census, .capacity").on('change', function() {

    //     var prior_ye_occ = $("#prior_ye_occ").val();

    //     if(prior_ye_occ == '' || isNaN(prior_ye_occ)) {
    //         return;
    //     }

    //     var census = $(".census").val();
    //     if(census == '' || isNaN(census)) {
    //         return;
    //     }

    //     var ytd_unit = census-prior_ye_occ;

    //     $("#ytd_unit").val(ytd_unit);

    //     var capacity = $(".capacity").val();

    //     if(capacity == '' || isNaN(capacity)) {
    //         return;
    //     }

    //     var change_percent = (ytd_unit / capacity)*100;
    //     change_percent = change_percent.toFixed(0);

    //     $("#change_percent").val(change_percent);
    // })

});