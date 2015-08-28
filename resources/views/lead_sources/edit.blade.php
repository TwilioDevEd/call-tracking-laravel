@extends('layouts.master')

@section('content')
    <h1>Purchase phone numbers</h1>

    {!! Form::open(['url' => route('available_number.index'), 'method' => 'GET']) !!}
      {!! Form::label('areaCode', 'Area code: ') !!}
      {!! Form::number('areaCode') !!}
      {!! Form::submit('Search', ['class' => 'btn btn-primary btn-xs']) !!}
    {!! Form::close() !!}

    <div class="col-md-6">
        <h2>Available numbers</h2>
        <p>Area code: {{ $areaCode or 'any' }}</p>
    <table class="table">
        <thead>
            <th>Phone number</th>
            <th>State</th>
            <th></th>
        </thead>
    <tbody>
        @foreach ($numbers as $number)
        <tr>
            <td> {{ $number->friendly_name }} </td>
            <td> {{ $number->region }} </td>
            <td>
                {!! Form::open(['url' => route('lead_source.store')]) !!}
                    {!! Form::hidden('phoneNumber', $number->phone_number) !!}
                    {!! Form::submit('Buy', ['class' => 'btn btn-primary btn-sm']) !!}
                {!! Form::close() !!}
            </td>
        </tr>
        @endforeach
    </tbody>
    </table>
    </div>
@stop
