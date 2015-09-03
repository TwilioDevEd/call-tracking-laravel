@extends('layouts.master')

@section('content')
    <h2>Leads by lead source</h2>
    <div class="row">
        <div class="col-md-4">
                <h2>Available numbers</h2>
                <p>Area code: </p>
                <table class="table">
                    <thead>
                        <th>Phone number</th>
                        <th>State</th>
                        <th></th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
        </div>
        <div class="col-md-4">
        <h3>Leads by lead source</h3>
            <canvas id="pie-by-lead-source"></canvas>
        </div>
        <div class="col-md-4">
        <h3>Leads by city</h3>
            <canvas id="pie-by-city"></canvas>
        </div>
    </div>
    <div class="row">
      @include('lead_sources.index', ['leadSources' => $leadSources, 'appSid' => $appSid])
    </div>
@stop

@section('scripts')
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js') !!}
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js')!!}
    {!! Html::script('/js/pieCharts.js') !!}
    {!! Html::script('/js/searchForNumbers.js') !!}
@stop
