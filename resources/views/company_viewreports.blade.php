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
        <div class="alert alert-custom alert-notice alert-light-danger alert-dismissible fade show mb-5"
            role='alert'>
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
                    <th>Building</th>
                    @foreach ($report_data as $report)
                        <th>{{ $report->get_Community['name'] }}
                        </th>
                    @endforeach
                    <th>Total</th>
                </thead>
                <tbody>
                    @foreach ($building_data as $building)
                        <tr>
                            <td>{{ $building }}</td>
                            @php
                                $total = 0;
                                $count = 0;
                            @endphp
                            @foreach ($report_data as $report)
                                <td>
                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($building == $cencaps->get_Building->name)
                                            @php
                                                $total += $cencaps->census;
                                                $count++;
                                            @endphp

                                            {{ $cencaps->census }}
                                            @break
                                        @endif
                                    @endforeach
                                </td>
                            @endforeach
                            <td>
                                {{ $total }}
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
                        @foreach ($report_data as $report)
                            @php
                                $period_total = 0;

                            @endphp
                            <td>
                                @foreach ($building_data as $building)
                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($building == $cencaps->get_Building->name)
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
                                {{ $total }}
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
                    <th>Building</th>
                    @foreach ($report_data as $report)
                        <th>{{ $report->get_Community['name'] }}
                        </th>
                    @endforeach
                    <th>Total</th>
                </thead>
                <tbody>
                    @foreach ($building_data as $building)
                        @php
                            $total = 0;
                            $count = 0;
                        @endphp
                        <tr>
                            <td>{{ $building }}</td>
                            @foreach ($report_data as $report)
                                <td>
                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($building == $cencaps->get_Building->name)
                                            @php
                                                $total += $cencaps->capacity;
                                                $count++;
                                            @endphp

                                            {{ $cencaps->capacity }}
                                                    @break
                                                @endif
                                        @endforeach
                                </td>
                            @endforeach
                            <td>
                                {{ $total }}
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
                        @foreach ($report_data as $report)
                            @php
                                $period_total = 0;

                            @endphp
                            <td>
                                @foreach ($building_data as $building)
                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($building == $cencaps->get_Building->name)
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
                                {{ $total }}
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
                    <th>Building</th>
                    @foreach ($report_data as $report)
                        <th>{{ $report->get_Community['name'] }}
                        </th>
                    @endforeach
                    <th>Total</th>
                </thead>
                <tbody>
                    @foreach ($building_data as $building)
                        @php
                            $total_census = 0;
                            $total_capacity = 0;
                            $count = 0;
                        @endphp
                        <tr>
                            <td>{{ $building }}</td>
                            @foreach ($report_data as $report)
                                <td>

                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($building == $cencaps->get_Building->name)
                                            @if ($cencaps->capacity)
                                                @php
                                                    $total_census += $cencaps->census;
                                                    $total_capacity += $cencaps->capacity;
                                                    $count++;
                                                @endphp

                                                {{ number_format(100 * ($cencaps->census / $cencaps->capacity), '2', '.', '') }}%
                                            @endif
                                                @break
                                            @endif
                                    @endforeach

                                </td>
                            @endforeach
                            <td>
                                @if ($count != 0)
                                    {{ number_format($total_census * 100/ $total_capacity, '2', '.', '') }}%
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
                        @foreach ($report_data as $report)
                            @php
                                $period_total_cencus = 0;
                                $period_total_capacity = 0;
                                $period_count = 0;
                            @endphp
                            <td>
                                @foreach ($building_data as $building)
                                    @foreach ($report->get_Cencaps as $cencaps)
                                        @if ($building == $cencaps->get_Building->name)
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
                    <th></th>
                    @php
                        $total_count = 0;
                    @endphp
                    @foreach ($report_data as $report)
                        @php
                            $total_count++;
                        @endphp
                        <th>{{ $report->get_Community['name'] }}
                        </th>
                    @endforeach
                    <th>Average</th>
                </thead>
                <tbody>
                    @foreach ($moveout_data as $moveout)
                        @php
                            $total = 0;
                            $count = 0;
                        @endphp
                        <tr>
                            <td>{{ $moveout }}</td>
                            @foreach ($report_data as $report)
                                <td>
                                    @foreach ($report->get_Moveouts as $move)
                                        @if ($moveout == $move->description)
                                            @php
                                                $total += $move->number;
                                            @endphp

                                            {{ $move->number }}

                                        @endif
                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach
                                </td>
                            @endforeach
                            <td>
                                @if ($count != 0)
                                    {{ number_format($total / $total_count, '2', '.', '') }}%
                                @else
                                0
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
                        @foreach ($report_data as $report)
                            @php
                                $period_total = 0;
                            @endphp
                            <td>
                                @foreach ($building_data as $building)
                                    @foreach ($report->get_Moveouts as $move)
                                        @if ($building == $cencaps->get_Building->name)
                                            @php
                                                $period_total += $move->number;
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
                                {{ number_format($total / $count, '2', '.', '') }}%
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
            <table class="table table-hover">
                <thead class="table-success">
                    <th></th>
                    @foreach ($report_data as $report)
                        <th>{{ $report->get_Community['name'] }}
                        </th>
                    @endforeach
                    <th>Average</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>Inquiries</td>
                        @foreach ($report_data as $report)
                            <td>
                                @php
                                    $total_period = $report->inquiry;
                                    $total += $report->inquiry;
                                    $count++;
                                @endphp

                                {{ $total_period }}
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>Unqualified</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->unqualified }}
                                @php
                                    $total += $report->unqualified;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>Tours</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->tours }}
                                @php
                                    $total += $report->tours;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>Deposits</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->deposits }}
                                @php
                                    $total += $report->deposits;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                        $total_tours=0;
                        $total_inquiries = 0;
                    @endphp
                    <tr>
                        <td>Inquiries to Tours</td>
                        @foreach ($report_data as $report)
                            <td>

                                @if ($report->tours != 0 && $report->inquiry != 0)
                                    {{ number_format(($report->tours / $report->inquiry) * 100, '2', '.', '') }}%
                                    @php
                                        $total_tours += $report->tours;
                                        $total_inquiries += $report->inquiry;
                                        $total += ($report->tours / $report->inquiry) * 100;
                                        $count++;
                                    @endphp
                                @endif
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}%
                            @endif

                        </td>
                        <td>
                        @if ($total_inquiries != 0)
                            {{ number_format(($total_tours / $total_inquiries) * 100, '2', '.', '') }}%
                        @else
                            0%
                        @endif
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                        $total_deposits=0;
                        $total_tours = 0;
                    @endphp
                    <tr>
                        <td>Tours to Deposits</td>
                        @foreach ($report_data as $report)
                            <td>
                                @php
                                    $total_deposits += $report->deposits;
                                    $total_tours += $report->tours;
                                @endphp
                                @if ($report->tours != 0)
                                    {{ number_format(100 * ($report->deposits / $report->tours), '2', '.', '') }}%
                                    @php
                                        $total += 100 * ($report->deposits / $report->tours);
                                        $count++;
                                    @endphp
                                @else
                                0%
                                @endif
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                        @if ($total_tours != 0)
                            {{ number_format(($total_deposits / $total_tours) * 100, '2', '.', '') }}%
                        @else
                            0%
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
                    <th></th>
                    @foreach ($report_data as $report)
                        <th>{{ $report->get_Community['name'] }}
                        </th>
                    @endforeach
                    <th>Average</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>WTD Move-Ins</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->wtd_movein }}
                                @php
                                    $total += $report->wtd_movein;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>WTD Move-Outs</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->wtd_moveout }}
                                @php
                                    $total += $report->wtd_moveout;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>WTD Net Residents</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->wtd_movein - $report->wtd_moveout }}
                                @php
                                    $total += $report->wtd_movein - $report->wtd_moveout;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>YTD Move-Ins</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->ytd_movein }}
                                @php
                                    $total += $report->ytd_movein;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>YTD Move-Outs</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->ytd_moveout }}
                                @php
                                    $total += $report->ytd_moveout;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
                        </td>
                    </tr>
                    @php
                        $total = 0;
                        $count = 0;
                    @endphp
                    <tr>
                        <td>YTD Net Residents</td>
                        @foreach ($report_data as $report)
                            <td>
                                {{ $report->ytd_movein - $report->ytd_moveout }}
                                @php
                                    $total += $report->ytd_movein - $report->ytd_moveout;
                                    $count++;
                                @endphp
                            </td>
                        @endforeach
                        <td>
                            @if ($count != 0)
                                {{ number_format($total / $count, '2', '.', '') }}
                            @endif
                        </td>
                        <td>
                            {{ $total }}
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
                @foreach ($report_data as $report)
                    <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $report->get_Community['name'] }}</div>
                @endforeach
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total</div>
            </div>
            @php
                $total = 0;
                $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;">Current Cencaps</div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">OCC</div>
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_census = 0;
                        @endphp

                        @foreach ($report->get_Cencaps as $cencaps)
                            @php
                                $total_census += $cencaps->census;
                            @endphp
                        @endforeach

                        {{ $total_census }}

                        @php
                            $total += $total_census;
                            $count++;
                        @endphp
                    </div>
                @endforeach
                <div class="col-md-1">
                    {{ $total }}
                </div>
            </div>

            @php
                $total = 0;
                $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total Unit</div>
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_capacity = 0;
                        @endphp

                        @foreach ($report->get_Cencaps as $cencaps)
                            @php
                                $total_capacity += $cencaps->capacity;
                            @endphp
                        @endforeach

                        {{ $total_capacity }}

                        @php
                            $total += $total_capacity;
                            $count++;
                        @endphp
                    </div>
                @endforeach
                <div class="col-md-1">
                    {{ $total }}
                </div>
            </div>
            @php
                $total = 0;
                $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">%</div>
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_percent = 0;
                        @endphp

                        @foreach ($report->get_Cencaps as $cencaps)
                            @php
                                $total_percent += 100 * ($total_census / $total_capacity);
                            @endphp
                        @endforeach

                        @if ($total_capacity != 0)
                            {{ number_format(100 * ($total_census / $total_capacity), '2', '.', '') }}(%)
                        @endif

                        @php
                            $total += $total_percent;
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
                <div class="col-md-1 sub-header" style="font-size: 12px;">Open Unit</div>
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_unit = 0;
                        @endphp

                        @foreach ($report->get_Cencaps as $cencaps)
                            @php
                                $total_unit += $total_capacity - $total_census;
                            @endphp
                        @endforeach

                        {{ $total_unit }}

                        @php
                            $total += $total_unit;
                            $count++;
                        @endphp
                    </div>
                @endforeach
                <div class="col-md-1">
                    {{ $total }}
                </div>
            </div>

            @php
                $total = 0;
                $count = 0;
            @endphp
            <div class="row mb-4">
                <div class="col-md-1 sub-header" style="font-size: 12px;"></div>
                <div class="col-md-1 sub-header" style="font-size: 12px;">Total Resident</div>
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_resident = 0;
                        @endphp
                        @foreach ($report->get_Cencaps as $cencaps)
                            @php
                                $total_resident += $cencaps->total_resident;
                            @endphp
                        @endforeach

                        {{ $total_resident }}

                        @php
                            $total += $total_capacity;
                            $count++;
                        @endphp
                    </div>
                @endforeach
                <div class="col-md-1">
                    {{ $total }}
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
                @foreach ($report_data as $report)
                    <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $report->get_Community['name'] }}</div>
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_gen_dep = 0;
                        @endphp
                        @foreach ($report->get_Moveouts as $moveout)
                            @if ($moveout->description == 'COMM VISITS')
                                @php
                                    $total_gen_dep += $moveout->number;
                                @endphp
                            @endif
                        @endforeach

                        {{ $total_gen_dep }}

                        @php
                            $total += $total_gen_dep;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_spec_dep = 0;
                        @endphp

                        @foreach ($report->get_Moveouts as $moveout)
                            @if ($moveout->description == 'SPEC DEP')
                                @php
                                    $total_spec_dep += $moveout->number;
                                @endphp
                            @endif
                        @endforeach

                        {{ $total_spec_dep }}

                        @php
                            $total += $total_spec_dep;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_moveout = 0;
                        @endphp

                        @foreach ($report->get_Moveouts as $moveout)
                            @if ($moveout->description == 'SPEC DEP' || $moveout->description == 'COMM VISITS')
                                @php
                                    $total_moveout += $moveout->number;
                                @endphp
                            @endif
                        @endforeach

                        {{ $total_moveout }}

                        @php
                            $total += $total_moveout;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $report->get_Community['name'] }}</div>
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        {{ $report->wtd_movein - $report->wtd_moveout }}

                        @php
                            $total += $report->wtd_movein - $report->wtd_moveout;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        {{ $report->wtd_movein }}

                        @php
                            $total += $report->wtd_movein;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        {{ $report->wtd_moveout }}

                        @php
                            $total += $report->wtd_moveout;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $total_mo_notice = 0;
                        @endphp

                        @foreach ($report->get_Moveouts as $moveout)
                            @if ($moveout->description == 'M/O NOTICE')
                                @php
                                    $total_mo_notice += $moveout->number;
                                @endphp
                            @endif
                        @endforeach

                        {{ $total_mo_notice }}

                        @php
                            $total += $total_mo_notice;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @php
                            $week_data = Reports::where('id', '>=', $report->id)
                                ->where('community_id', $report->community_id)
                                ->get();

                            $report_week = 0;

                            for ($i = 0; $i < count($week_data); $i++) {
                                $cencaps_data = $week_data[$i]->get_Cencaps->first();
                                $census = $cencaps_data->census;
                                $capacity = $cencaps_data->capacity;

                                if ($census == $capacity) {
                                    $report_week++;
                                } else {
                                    break;
                                }
                            }
                        @endphp

                        {{ $report_week }}

                        @php
                            $total += $report_week;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1 sub-header" style="font-size: 12px;">{{ $report->get_Community['name'] }}</div>
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        {{ $report->prior_ye_occ }}

                        @php
                            $total += $report->prior_ye_occ;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
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

                        {{ $total_census - $report->prior_ye_occ }}

                        @php
                            $total += $total_census - $report->prior_ye_occ;
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
                @foreach ($report_data as $report)
                    <div class="col-md-1">
                        @if ($total_capacity != 0)
                            {{ number_format(100 * (($total_census - $report->prior_ye_occ) / $total_capacity), '2', '.', '') }}(%)
                            @php
                                $total += 100 * (($total_census - $report->prior_ye_occ) / $total_capacity);
                                $count++;
                            @endphp
                        @endif
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
