<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use App\LeadSource;

class LeadSourceControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function testIndex()
    {
        $response = $this->call('GET', route('lead_source.index'));
        $leadSources = $response->getOriginalContent()['leadSources'];

        $this->assertCount(0, $leadSources);

        $newLeadSource = new LeadSource(
            ['number' => '+136428733',
             'description' => 'Some billboard somewhere',
             'forwarding_number' => '+13947283']
        );
        $newLeadSource->save();

        $newResponse = $this->call('GET', route('lead_source.index'));
        $newLeadSources = $newResponse->getOriginalContent()['leadSources'];
        $this->assertCount(1, $newLeadSources);

        $this->assertEquals($newLeadSources[0]['number'], '+136428733');
        $this->assertEquals($newLeadSources[0]['description'], 'Some billboard somewhere');
        $this->assertEquals($newLeadSources[0]['forwarding_number'], '+13947283');
    }

    public function testStore()
    {
        $mockTwilio = Mockery::mock('Services_Twilio');
        $mockTwilio->account = Mockery::mock();
        $mockTwilio->account->incoming_phone_numbers = Mockery::mock();

        $mockTwilio->account->incoming_phone_numbers
            ->shouldReceive('create')
            ->with(
                ['PhoneNumber' => '+15005550006',
                 'VoiceCallerIdLookup' => true,
                 'VoiceApplicationSid' => env('TWILIO_APP_SID')
                ]
            );

        App::instance('Twilio', $mockTwilio);

        $response = $this->call('POST', route('lead_source.store'), ['phoneNumber' => '+15005550006']);
        $allLeadSources = LeadSource::all();

        $this->assertCount(1, $allLeadSources);

        $this->assertEquals($allLeadSources[0]['number'], '+15005550006');
        $this->assertEquals($allLeadSources[0]['description'], null);
        $this->assertEquals($allLeadSources[0]['forwarding_number'], null);
    }
}