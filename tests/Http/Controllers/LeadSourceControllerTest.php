<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use App\LeadSource;

class LeadSourceControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testStore()
    {
        // Given
        Session::start();

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

        // When

        $response = $this->call(
            'POST', route('lead_source.store'),
            ['phoneNumber' => '+15005550006',
             '_token' => Session::token()]
        );

        $this->assertTrue($response->isRedirect());


        // Then

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
        // Given

        $newLeadSource = new LeadSource(
            ['number' => '+136428733',
             'description' => 'Some billboard somewhere',
             'forwarding_number' => '+13947283']
        );
        $newLeadSource->save();
        $leadSourceId = $newLeadSource->id;

        // When

        $response = $this->call('GET', route('lead_source.edit', $leadSourceId));

        // Then

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

    public function testDestroy()
    {
        // Given
        Session::start();

        $newLeadSource = new LeadSource(
            ['number' => '+136428733',
             'description' => 'Some billboard somewhere',
             'forwarding_number' => '+13947283',
             'token' => csrf_token()]
        );
        $newLeadSource->save();
        $leadSourceId = $newLeadSource->id;

        $mockNumber = Mockery::mock();
        $mockNumber->sid = 'sup3runiq3s1d';

        $mockTwilio = Mockery::mock('Services_Twilio');
        $mockTwilio->account = Mockery::mock();
        $mockTwilio->account->incoming_phone_numbers = Mockery::mock();

        $mockTwilio->account->incoming_phone_numbers
            ->shouldReceive('getNumber')
            ->with($newLeadSource['number'])
            ->andReturn($mockNumber);

        $mockTwilio->account->incoming_phone_numbers
            ->shouldReceive('delete')
            ->with('sup3runiq3s1d');


        App::instance('Twilio', $mockTwilio);

        // When

        $response = $this->call(
            'DELETE',
            route('lead_source.destroy', $leadSourceId),
            ['_token' => csrf_token()]
        );

        // Then

        $this->assertCount(0, LeadSource::all());
    }
}