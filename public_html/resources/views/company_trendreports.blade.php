@extends('layouts.app')

@section('additional_css')

@endsection

@section('contents')

@php
use App\model\Reports;
@endphp

<div class="container">

    @if (empty($report_data))
    <div style="height: 520px;">
        <div class="alert alert-custom alert-notice alert-light-danger alert-dismissible fade show mb-5" role='alert'>
            <button type="button" class="close" data-dismiss="alert">×</button>
            No Report Data !
        </div>
    </div>
    @else
    @if ($user_data->company_id == 1)

    <div class="row pt-12 reports-data">
        <div class="col-md-12 mb-4">
            <h3 class="landingtitle">Current Census</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Building</th>
                        @foreach ($period_data as $period)
                        <th>{{ $period->caption }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($building_data as $building)
                    <tr>
                        <td>{{ $building }}</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp
                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @foreach ($report->get_Cencaps as $cencaps)
                            @if ($building == $cencaps->get_Building->name)
                            @php
                            $period_total += $cencaps->census;
                            @endphp
                            @break;
                            @endif
                            @endforeach
                            @endif
                            @endforeach

                            {{ $period_total }}
                            @php
                            $total += $period_total;
                            $count++;
                            @endphp
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-dark text-dark">
                        <td>Total</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;

                        @endphp
                        <td>
                            @foreach ($report_data as $report)
                            @foreach ($report->get_Cencaps as $cencaps)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $cencaps->census;
                            @endphp
                            @endif
                            @endforeach
                            @endforeach
                            @php
                            $total += $period_total;
                            $count++;
                            @endphp
                            {{ $period_total }}
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <div class="row reports-data">
        <div class="col-md-12 mb-4">
            <h3 class="landingtitle">Total Capacity</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Building</th>
                        @foreach ($period_data as $period)
                        <th>{{ $period->caption }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($building_data as $building)
                    <tr>
                        <td>{{ $building }}</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp
                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @foreach ($report->get_Cencaps as $cencaps)
                            @if ($building == $cencaps->get_Building->name)
                            @php
                            $period_total += $cencaps->capacity;
                            @endphp
                            @break;
                            @endif
                            @endforeach
                            @endif
                            @endforeach

                            {{ $period_total }}
                            @php
                            $total += $period_total;
                            $count++;
                            @endphp
                        </td>
                        @endforeach
                        <td>
                            {{ number_format($total / $count, '2', '.', '') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-dark text-dark">
                        <td>Total</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;

                        @endphp
                        <td>
                            @foreach ($report_data as $report)
                            @foreach ($report->get_Cencaps as $cencaps)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $cencaps->capacity;
                            @endphp
                            @endif
                            @endforeach
                            @endforeach
                            @php
                            $total += $period_total;
                            $count++;
                            @endphp
                            {{ $period_total }}
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
    <div class="row reports-data">
        <div class="col-md-12 mb-4">
            <h3 class="landingtitle">Census VS Capacity</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Building</th>
                        @foreach ($period_data as $period)
                        <th>{{ $period->caption }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($building_data as $building)
                        <tr>
                            <td>{{ $building }}</td>
                            @php
                                $total_census = 0;
                                $total_capacity = 0;
                                $count = 0;
                            @endphp

                            @foreach ($period_data as $period)
                                @php
                                    $period_total_cencus = 0;
                                    $period_total_capacity = 0;
                                    $period_count = 0;
                                @endphp
                                <td>
                                    @foreach ($report_data as $report)
                                        @if ($report->period_id == $period->id)
                                            @foreach ($report->get_Cencaps as $cencaps)
                                                @if ($building == $cencaps->get_Building->name)
                                                    @php
                                                        $period_total_cencus += $cencaps->census;
                                                        $period_total_capacity += $cencaps->capacity;
                                                        $period_count++;
                                                    @endphp
                                                @break;
                                            @endif
                                        @endforeach
                                    @endif
                            @endforeach

                            @if ($period_count != 0)
                                {{ number_format($period_total_cencus * 100 / $period_total_capacity, '2', '.', '') }}%
                            @endif

                            @if ($period_count != 0)
                                @php
                                    $total_census += $period_total_cencus;
                                    $total_capacity += $period_total_capacity;
                                    $count++;
                                @endphp
                            @endif
                            </td>
                    @endforeach
                    <td>
                        @if ($period_count != 0)
                            {{ number_format($total_census * 100 / $total_capacity, '2', '.', '') }}%
                        @endif
                    </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-dark text-dark">
                        <td>Total</td>
                        @php
                            $total_census = 0;
                            $total_capacity = 0;
                            $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                            @php
                                $period_count = 0;
                                $period_total_cencus = 0;
                                $period_total_capacity = 0;

                            @endphp
                            <td>
                                @foreach ($report_data as $report)
                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($report->period_id == $period->id)
                                            @php
                                                $period_total_cencus += $cencaps->census;
                                                $period_total_capacity += $cencaps->capacity;
                                                $period_count++;
                                            @endphp
                                        @endif
                                    @endforeach
                                @endforeach

                                @if ($period_count != 0)
                                    @php
                                        $total_census += $period_total_cencus;
                                        $total_capacity += $period_total_capacity;
                                        $count++;
                                    @endphp
                                @endif

                                @if ($period_count != 0)
                                    {{ number_format($period_total_cencus * 100 / $period_total_capacity, '2', '.', '') }}%
                                @endif
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total_census * 100 / $total_capacity, '2', '.', '') }}%
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <hr />

    <?php
    $moveout_data = [];
    foreach ($report_data as $report) {
        foreach ($report->get_Moveouts as $moveout) {
            $flag = 0;
            for ($i = 0; $i < count($moveout_data); $i++) {
                if ($moveout_data[$i] == $moveout->description) {
                    $flag = 1;
                    break;
                }
            }

            if ($flag == 0) {
                array_push($moveout_data, $moveout->description);
            }
        }
    }
    ?>
    <div class="row reports-data">
        <div class="col-md-12 mb-4">
            <h3 class="landingtitle">Move-Out Reason</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Description</th>
                        @foreach ($period_data as $period)
                        <th>{{ $period->caption }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($moveout_data as $moveout)
                    <tr>
                        <td>{{ $moveout }}</td>

                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp
                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @foreach ($report->get_Moveouts as $move)
                            @if ($moveout == $move->description)
                            @php
                            $period_total += $move->number;
                            @endphp
                            @break;
                            @endif
                            @endforeach
                            @endif
                            @endforeach
                            {{ $period_total }}

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp
                        </td>
                        @endforeach


                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-dark text-dark">
                        <td>Total</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        $period_count = 0;
                        @endphp
                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @foreach ($report->get_Moveouts as $move)
                            @php
                            $period_total += $move->number;
                            $period_count++;
                            @endphp
                            @endforeach
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
    <hr />

    <div class="row reports-data">
        <div class="col-md-12 mb-4">
            <h3 class="landingtitle">Statistics</h3>
        </div>
        <div class="table-responsive">
            @php
            $total_inquiry = 0;
            $total_tours = 0;
            $total_deposits = 0;
            @endphp
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th></th>
                        @foreach ($period_data as $period)
                        <th>{{ $period->caption }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Inquiries</td>
                        @php

                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)

                            @php
                            $period_total += $report->inquiry;
                            @endphp

                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            $total_inquiry = $period_total;
                            @endphp
                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>


                    <tr>
                        <td>Unqualified</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->unqualified;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                    @php
                    $total = 0;
                    $count = 0;
                    @endphp
                    <tr>
                        <td>Tours</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->tours;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            $total_tours = $period_total;
                            @endphp
                            {{ $period_total }}
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)

                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                    @php
                    $total = 0;
                    $count = 0;
                    @endphp
                    <tr>
                        <td>Deposits</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->deposits;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            $total_deposits = $total;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>Inquiries to Tours</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        $period_count = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                                @if ($report->period_id == $period->id)
                                    @php
                                    $total_inquiries = 0;
                                    @endphp

                                    @php
                                    $total_inquiries += $report->inquiry;
                                    @endphp

                                    @if ($total_inquiries > 0)
                                        @php
                                        $period_total += ($report->tours / $total_inquiries) * 100;
                                        $period_count++;
                                        @endphp
                                    @endif
                                @endif
                            @endforeach

                            @if ($period_count != 0)
                                @php
                                $total += $period_total / $period_count;
                                $count++;
                                @endphp
                            @endif
                            @php
                            $total = ($total_tours / $total_inquiry) * 100;
                            @endphp
                            @if ($period_count != 0)
                                {{ number_format($total, '2', '.', '') }}%
                            @endif
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>Tours to Deposits</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp
                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        $period_count = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @if ($report->deposits != 0)
                            @php
                            $period_total += 100 * ($report->tours / $report->deposits);
                            $period_count++;
                            @endphp
                            @endif
                            @endif
                            @endforeach

                            @if ($period_count != 0)
                            @php
                            $total += $period_total / $period_count;
                            $count++;
                            $total = ($total_deposits / $total_tours) * 100;
                            @endphp
                            @endif

                            @if ($period_count != 0)
                            {{ number_format($total, '2', '.', '') }}%
                            @endif
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <hr />

    <div class="row reports-data">
        <div class="col-md-12 mb-4">
            <h3 class="landingtitle">MoveIn/OUT</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th></th>
                        @foreach ($period_data as $period)
                        <th>{{ $period->caption }}</th>
                        @endforeach
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>WTD Move-Ins</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->wtd_movein;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>WTD Move-Outs</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->wtd_moveout;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>WTD Net Residents</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->wtd_movein - $report->wtd_moveout;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>YTD Move-Ins</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->ytd_movein;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>YTD Move-Outs</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->ytd_moveout;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>YTD Net Residents</td>
                        @php
                        $total = 0;
                        $count = 0;
                        @endphp

                        @foreach ($period_data as $period)
                        @php
                        $period_total = 0;
                        @endphp

                        <td>
                            @foreach ($report_data as $report)
                            @if ($report->period_id == $period->id)
                            @php
                            $period_total += $report->ytd_movein - $report->ytd_moveout;
                            @endphp
                            @endif
                            @endforeach

                            @php
                            $total += $period_total;
                            $count++;
                            @endphp

                            {{ $period_total }}
                        </td>
                        @endforeach

                        <td>
                            @if ($count != 0)
                            {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    @else
    <div class="row reports-data">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                @foreach ($period_data as $period)
                <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $period->caption }}</div>
                @endforeach
                <div class="col-md-1 sub-header" style="font-size: 12px;">Average</div>
            </div>
            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">Current Cencaps</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">OCC</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)

                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Cencaps as $cencaps)
                    @php
                    $period_total += $cencaps->census;
                    @endphp
                    @endforeach
                    @endif

                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>

                @endforeach
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total Unit</div>
                @foreach ($period_data as $period)

                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Cencaps as $cencaps)
                    @php
                    $period_total += $cencaps->capacity;
                    @endphp
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>
            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">%</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    $period_count = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Cencaps as $cencaps)
                    @if ($cencaps->capacity != 0)
                    @php
                    $period_total += 100 * ($cencaps->census / $cencaps->capacity);
                    $period_count++;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    @if ($period_count != 0)
                    {{ number_format($period_total / $period_count, '2', '.', '') }}(%)
                    @php
                    $total += $period_total / $period_count;
                    $count++;
                    @endphp
                    @endif


                </div>
                @endforeach

                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>
            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Open Unit</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Cencaps as $cencaps)
                    @php
                    $period_total += $cencaps->capacity - $cencaps->census;
                    @endphp
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach


                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total Resident</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Cencaps as $cencaps)
                    @php
                    $period_total += $cencaps->total_resident;
                    @endphp
                    @endforeach
                    @endif
                    @endforeach
                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

        </div>
    </div>
    <hr />
    <div class="row reports-data">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                @foreach ($period_data as $period)
                <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $period->caption }}</div>
                @endforeach
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Average</div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">Type</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">COMM VISITS</div>

                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Inquiries as $inquiry)
                    @if ($inquiry->description == 'COMM VISITS')
                    @php
                    $period_total += $inquiry->number;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach


                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">Of Inquiries</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">DI/DEP CALLS</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp
                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Inquiries as $inquiry)
                    @if ($inquiry->description == 'DI/DEP CALLS')
                    @php
                    $period_total += $inquiry->number;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp
                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Inquiries as $inquiry)
                    @php
                    $period_total += $inquiry->number;
                    @endphp
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach


                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="row reports-data">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                @foreach ($period_data as $period)
                <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $period->caption }}</div>
                @endforeach
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Average</div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">Deposits</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">GEN DEP</div>

                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Moveouts as $moveout)
                    @if ($moveout->description == 'COMM VISITS')
                    @php
                    $period_total += $moveout->number;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">SPEC DEP</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Moveouts as $moveout)
                    @if ($moveout->description == 'SPEC DEP')
                    @php
                    $period_total += $moveout->number;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Moveouts as $moveout)
                    @if ($moveout->description == 'SPEC DEP' || $moveout->description == 'COMM VISITS')
                    @php
                    $period_total += $moveout->number;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="row reports-data">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                @foreach ($period_data as $period)
                <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $period->caption }}</div>
                @endforeach
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Average</div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">Move In/Out</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">INCR/DECR</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $sub_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @php
                    $sub_total += $report->wtd_movein - $report->wtd_moveout;

                    @endphp
                    @endif
                    @endforeach

                    {{ $sub_total }}
                    @php
                    $total += $sub_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">MOVE IN</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $sub_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)

                    @php
                    $sub_total += $report->wtd_movein;
                    @endphp
                    @endif
                    @endforeach

                    {{ $sub_total }}
                    @php
                    $total += $sub_total;
                    $count++;
                    @endphp

                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">MOVE OUT</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $sub_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)

                    @php
                    $sub_total += $report->wtd_moveout;
                    @endphp
                    @endif
                    @endforeach

                    {{ $sub_total }}
                    @php
                    $total += $sub_total;
                    $count++;
                    @endphp

                </div>
                @endforeach
                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">M/O NOTICE</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @foreach ($report->get_Moveouts as $moveout)
                    @if ($moveout->description == 'M/O NOTICE')
                    @php
                    $period_total += $moveout->number;
                    @endphp
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">WEEK AT 100%</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $sub_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @php
                    $week_data = Reports::where('id', '>=', $report->id)
                    ->where('community_id', $report->community_id)
                    ->get();

                    $report_week = 0;

                    for ($i = 0; $i < count($week_data); $i++) { $cencaps_data=$week_data[$i]->get_Cencaps->first();
                        $census = $cencaps_data->census;
                        $capacity = $cencaps_data->capacity;

                        if ($census == $capacity) {
                        $report_week++;
                        } else {
                        break;
                        }
                        }

                        $sub_total += $report_week;

                        @endphp

                        @endif
                        @endforeach

                        {{ $sub_total }}

                        @php
                        $total += $sub_total;
                        $count++;
                        @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="row reports-data">
        <div class="col-md-12">
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                @foreach ($period_data as $period)
                <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $period->caption }}</div>
                @endforeach
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Average</div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">OTHER</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Prior YE OCC</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $sub_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)

                    @php
                    $sub_total += $report->prior_ye_occ;
                    @endphp
                    @endif
                    @endforeach

                    {{ $sub_total }}

                    @php
                    $total += $sub_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">YTD Units</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @php
                    $total_census = 0;
                    @endphp

                    @foreach ($report->get_Cencaps as $cencaps)
                    @php
                    $total_census += $cencaps->census;
                    @endphp
                    @endforeach

                    @php
                    $period_total += $total_census - $report->prior_ye_occ;
                    @endphp
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

            @php
            $total = 0;
            $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Change</div>
                @foreach ($period_data as $period)
                <div class="col-md-1">
                    @php
                    $period_total = 0;
                    @endphp

                    @foreach ($report_data as $report)
                    @if ($report->period_id == $period->id)
                    @php
                    $total_census = 0;
                    $total_capacity = 0;
                    @endphp

                    @foreach ($report->get_Cencaps as $cencaps)
                    @php
                    $total_census += $cencaps->census;
                    $total_capacity += $cencaps->capacity;
                    @endphp
                    @endforeach

                    @if ($total_capacity != 0)
                    @php
                    $period_total += 100 * (($total_census - $report->prior_ye_occ) / $total_capacity);
                    @endphp
                    @endif
                    @endif
                    @endforeach

                    {{ $period_total }}

                    @php
                    $total += $period_total;
                    $count++;
                    @endphp
                </div>
                @endforeach

                <div class="col-md-1">
                    {{ $total }}
                </div>
                <div class="col-md-1">
                    @if ($count != 0)
                    {{ number_format($total / $count, '2', '.', '') }}
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif
    @endif

</div>

@endsection

@section('additional_js')

@endsection
