@extends('layouts.master')

@section('content')
    <h2>Call tracking</h2>
    <div class="row">
        <div class="col-md-4">
            @include('available_numbers.index', ['numbers' => $availableNumbers])
        </div>
        <div class="col-md-4">
        <h3>Leads by lead source</h3>
            <canvas id="pie-by-lead-source"></canvas>
        </div>
        <div class="col-md-4">
        <h3>Leads by city</h3>
            <canvas id="pie-by-city"></canvas>
        </div>
      @include('lead_sources.index', ['leadSources' => $leadSources, 'appSid' => $appSid])
    </div>
    <div class="row">
    </div>
@stop

@section('scripts')
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js') !!}
    {!! Html::script('//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js')!!}
    {!! Html::script('/js/pieCharts.js') !!}
@stop
