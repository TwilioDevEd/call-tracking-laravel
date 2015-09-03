<h2>All lead sources</h2>
<hr/>

<div class="col-md-8">
    {!! Html::link(route('available_number.index'), 'New', ['class' => 'btn btn-default']) !!}
    {!! Html::link('https://www.twilio.com/user/account/apps/' . $appSid, 'App configuration', ['class' => 'btn btn-default']) !!}
    <table class="table">
        <thead>
            <th>Lead source description</th>
            <th>Number</th>
            <th>Forwarded to</th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            @foreach ($leadSources as $leadSource)
                <tr>
                    <td> {{ $leadSource->description }} </td>
                    <td> {{ $leadSource->number }} </td>
                    <td> {{ $leadSource->forwarding_number }} </td>
                    <td>
                        {!! Form::open(['url' => route('lead_source.destroy', $leadSource->id),
                                        'method' => 'DELETE']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                    </td>
                    <td>
                        {!! Html::link(route('lead_source.edit', $leadSource->id), 'Edit',
                                       ['class' => 'btn btn-default btn-sm']) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
