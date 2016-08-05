@extends('layouts.master')

@section('content')
<div class="col-lg-6">
<h2>Purchase phone numbers</h2>
    <h3>Available numbers</h3>
    <p>For area code: {{ $areaCode or 'any' }}</p>
    <p>The number you choose will be used to create a Lead Source. On the next page, you will set a name and forwarding number for this lead source.</p>
    <table class="table">
        <thead>
            <th>Phone number</th>
            <th>State</th>
            <th></th>
        </thead>
        <tbody>
            @foreach ($numbers as $number)
                <tr>
                    <td> {{ $number->friendlyName }} </td>
                    <td> {{ $number->region }} </td>
                    <td>
                        {!! Form::open(['url' => route('lead_source.store')]) !!}
                        {!! Form::hidden('phoneNumber', $number->phoneNumber) !!}
                        {!! Form::submit('Purchase', ['class' => 'btn btn-primary btn-xs']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop
