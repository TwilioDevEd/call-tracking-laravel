<?php

use App\LeadSource;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Twilio\Rest\Api\V2010\Account\ApplicationList;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberInstance;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberList;
use Twilio\Rest\Client;

class LeadSourceControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testStore()
    {
        // Given
        Session::start();

        $mockTwilioClient = Mockery::mock(Client::class);

        $mockTwilioClient->incomingPhoneNumbers
            = Mockery::mock(IncomingPhoneNumberList::class);

        $mockTwilioClient->incomingPhoneNumbers
            ->shouldReceive('create')
            ->withAnyArgs()
            ->once();

        $mockApplication = new \stdClass();
        $mockApplication->sid = "WAXXXXXXXX";

        $mockTwilioClient->applications = Mockery::mock(ApplicationList::class);
        $mockTwilioClient->applications
            ->shouldReceive('read')
            ->withAnyArgs()
            ->andReturn([0 => $mockApplication]);

        App::instance(Client::class, $mockTwilioClient);

        // When

        $this->post(
            route('lead_source.store'),
            [
                'phoneNumber' => '+15005550006',
                '_token' => Session::token()
            ]
        );

        // Then

        $allLeadSources = LeadSource::all();
        $firstLeadSource = LeadSource::first();

        $this->assertRedirectedToRoute("lead_source.edit", [$firstLeadSource]);

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

        $this->assertEquals($response->getOriginalContent()['leadSource']->id,
            $newLeadSource->id);

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

        $this->assertCount(0, LeadSource::all());

        $newLeadSource = new LeadSource(
            [
                'number' => '+136428733',
                'description' => 'Some billboard somewhere',
                'forwarding_number' => '+13947283'
            ]
        );
        $newLeadSource->save();

        $this->assertCount(1, LeadSource::all());

        $mockNumber = Mockery::mock();
        $mockNumber->sid = 'sup3runiq3s1d';

        $mockTwilioClient = Mockery::mock(Client::class);

        $mockPhoneToDelete = Mockery::mock(IncomingPhoneNumberInstance::class);
        $mockPhoneToDelete->shouldReceive("delete")->once();

        $mockTwilioClient->incomingPhoneNumbers = Mockery::mock(
            IncomingPhoneNumberList::class
        );
        $mockTwilioClient->incomingPhoneNumbers
            ->shouldReceive('read')
            ->withAnyArgs()
            ->once()
            ->andReturn([0 => $mockPhoneToDelete]);

        App::instance(Client::class, $mockTwilioClient);

        // When

        $response = $this->call(
            'DELETE',
            route('lead_source.destroy', [$newLeadSource]),
            [
                '_token' => Session::token()
            ]
        );

        // Then

        $this->assertCount(0, LeadSource::all());
    }
}
