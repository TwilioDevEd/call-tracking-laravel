<h2>Purchase phone numbers</h2>

{!! Form::open(['url' => route('lead.index'), 'method' => 'GET']) !!}
    {!! Form::label('areaCode', 'Area code: ') !!}
    {!! Form::number('areaCode') !!}
    {!! Form::submit('Search', ['class' => 'btn btn-primary btn-xs']) !!}
{!! Form::close() !!}

    <h3>Available numbers</h3>
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
