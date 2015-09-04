@extends('layouts.master')

@section('content')
    <h1>Edit a lead source</h1>
    <hr>
    <h3>Number: {!! $leadSource->number !!}</h3>
        {!! Form::model($leadSource, ['url' => route('lead_source.update', $leadSource->id), 'method' => 'PUT']) !!}
        <div class="form-group">
            {!! Form::label('description', 'Lead source description:') !!}
            {!! Form::text('description') !!}
        </div>
        <div class="form-group">
            {!! Form::label('forwarding_number', 'Lead forwarding number:') !!}
            {!! Form::text('forwarding_number') !!}
        </div>
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}

    {!! Form::open(['url' => route('lead_source.destroy', $leadSource->id),
                    'method' => 'DELETE']) !!}
    {!! Form::submit('Delete this number', ['class' => 'btn btn-danger btn-sm']) !!}
    {!! Form::close() !!}
@stop
