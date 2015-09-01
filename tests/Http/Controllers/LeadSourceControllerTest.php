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
        $this->assertTrue($response->isRedirect());

        $allLeadSources = LeadSource::all();
        $firstLeadSource = LeadSource::first();

        $this->assertEquals($response->getTargetUrl(), route('lead_source.edit', $firstLeadSource->id));

        $this->assertCount(1, $allLeadSources);

        $this->assertEquals($firstLeadSource['number'], '+15005550006');
        $this->assertEquals($firstLeadSource['description'], null);
        $this->assertEquals($firstLeadSource['forwarding_number'], null);
    }

    public function testEdit()
    {
        $newLeadSource = new LeadSource(
            ['number' => '+136428733',
             'description' => 'Some billboard somewhere',
             'forwarding_number' => '+13947283']
        );
        $newLeadSource->save();
        $leadSourceId = $newLeadSource->id;

        $response = $this->call('GET', route('lead_source.edit', $leadSourceId));

        $this->assertEquals($response->getOriginalContent()['leadSource']->id, $newLeadSource->id);

        $this->assertEquals(
            $response->getOriginalContent()['leadSource']->number,
            $newLeadSource->number
        );
        $this->assertEquals(
            $response->getOriginalContent()['leadSource']->description,
            $newLeadSource->description
        );
        $this->assertEquals(
            $response->getOriginalContent()['leadSource']->forwarding_number,
            $newLeadSource->forwarding_number
        );
    }
}