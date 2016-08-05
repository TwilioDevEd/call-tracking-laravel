<h3>Lead sources</h3>
<hr/>
<div class="row">
    <div class="col-lg-8">
        {!! Html::link('https://www.twilio.com/user/account/apps/' . $appSid, 'App
         configuration', ['class' => 'btn btn-default', 'target' => '_blank']) !!}
        <table class="table">
            <thead>
                <th>Lead source</th>
                <th>Number</th>
                <th>Forwarded to</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($leadSources as $leadSource)
                    <tr>
                        <td> {{ $leadSource->description }} </td>
                        <td> {{ $leadSource->number }} </td>
                        <td> {{ $leadSource->forwarding_number }} </td>
                        <td>
                            {!! Html::link(route('lead_source.edit', $leadSource->id), 'Edit',
                                           ['class' => 'btn btn-default btn-xs']) !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
