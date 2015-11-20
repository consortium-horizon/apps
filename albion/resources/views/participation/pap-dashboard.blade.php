@extends('layouts.main')
@section('content')
    <div class="row" style="margin-top: 3%">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Participation / Total Guild</h3>
                </div>
                <div class="panel-body">
                    <div id="papsTotalDC"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Participation / Event type</h3>
                    </div>
                <div class="panel-body">
                        <div id="papsTypeDC"></div>
                </div><!--panel-body-->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>Participation this month</h3>
                </div>
                <div class="panel-body">
                        <div id="papsMonthly"></div>
                </div><!--panel-body-->
            </div>
        </div>
    </div>
@endsection

@section('customScripts')
    <script>
        new Morris.Donut({
            element: 'papsTotalDC',
            data: [
                { label: '{{$userName}}', value: {{$papsTotalUser}} },
                { label: 'Others' , value: {{$papsUserRatio}} }
            ],
            resize: true
        });
        new Morris.Donut({
            element: 'papsTypeDC',
            data: [
                { label: 'PvP', value: {{$papsUserPvP}} },
                { label: 'PvE' , value: {{$papsUserPvE}} }
            ],
            resize: true,
            colors: [
                '#B20000',
                '#297A29'
            ]
        });
        var mData = JSON.parse( '<?php echo json_encode($monthData); ?>' );
        new Morris.Area({
            element: 'papsMonthly',
            data: mData,
            xkey: 'date',
            ykeys: ['papsTotal', 'papsUser'],
            xLabels:"day",
            labels: ['Guild', '{{$userName}}'],
            resize: true
        });
    </script>
@endsection