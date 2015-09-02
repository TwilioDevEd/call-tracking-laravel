@extends('layouts.master')

@section('content')
    <h1>Leads by lead source</h1>
    <div class="row">
        <div class="col-md-6">
        <h2>Leads by lead source</h2>
            <canvas id="pie-by-lead-source"></canvas>
        </div>
        <div class="col-md-6">
        <h2>Leads by city</h2>
            <canvas id="pie-by-city"></canvas>
        </div>
    </div>
@stop

@section('scripts')
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js') !!}
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js')!!}
    {!! Html::script('/js/pieCharts.js') !!}
@stop
