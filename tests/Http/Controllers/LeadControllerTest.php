<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;

use App\LeadSource;
use App\Lead;

class LeadControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testLeadSourceIndex()
    {
        // Given
        $mockNumberList = Mockery::mock();
        $mockNumber = Mockery::mock();

        $mockNumber->friendly_name = '(555) 444 444';
        $mockNumber->region = 'Some region';
        $mockNumber->phone_number = '+1555444444';

        $mockNumbers = [$mockNumber];

        $mockNumberList->available_phone_numbers = $mockNumbers;

        $mockTwilio = Mockery::mock('Services_Twilio');
        $mockTwilio->account = Mockery::mock();
        $mockTwilio->account->available_phone_numbers = Mockery::mock();
        $mockTwilio->account->available_phone_numbers
            ->shouldReceive('getList')
            ->with('US', 'Local', ['AreaCode' => null])
            ->andReturn($mockNumberList);

        App::instance('Twilio', $mockTwilio);

        $newLeadSource = new LeadSource(
            ['number' => '+136428733',
             'description' => 'Some billboard somewhere',
             'forwarding_number' => '+13947283']
        );
        $newLeadSource->save();

        // When

        $newResponse = $this->call('GET', route('dashboard'));

        // Then

        $newLeadSources = $newResponse->getOriginalContent()['leadSources'];
        $this->assertCount(1, $newLeadSources);

        $this->assertEquals($newLeadSources[0]['number'], '+136428733');
        $this->assertEquals($newLeadSources[0]['description'], 'Some billboard somewhere');
        $this->assertEquals($newLeadSources[0]['forwarding_number'], '+13947283');
    }

    public function testAvailableNumberIndex()
    {
        // Given

        $mockNumberList = Mockery::mock();
        $mockNumber = Mockery::mock();

        $mockNumber->friendly_name = '(555) 444 444';
        $mockNumber->region = 'Some region';
        $mockNumber->phone_number = '+1555444444';

        $mockNumbers = [$mockNumber];

        $mockNumberList->available_phone_numbers = $mockNumbers;

        $mockTwilio = Mockery::mock('Services_Twilio');
        $mockTwilio->account = Mockery::mock();
        $mockTwilio->account->available_phone_numbers = Mockery::mock();
        $mockTwilio->account->available_phone_numbers
            ->shouldReceive('getList')
            ->with('US', 'Local', ['AreaCode' => null])
            ->andReturn($mockNumberList);

        App::instance('Twilio', $mockTwilio);

        // When
        $response = $this->call('GET', route('dashboard'));

        // Then
        $this->assertEquals(
            $response->getOriginalContent()['availableNumbers'],
            $mockNumbers
        );
    }
    

    public function testStore()
    {
        // Given

        $this->assertCount(0, Lead::all());

        $newLeadSource = new LeadSource(
            ['number' => '+1153614723',
             'description' => 'Downtown south billboard',
             'forwarding_number' => '+155005500']
        );
        $newLeadSource->save();

        // When

        $requestParameters = [
            'FromCity' => 'Boston',
            'FromState' => 'MS',
            'From' => '+177007700',
            'To' => '+1153614723',
            'CallerName' => 'John Doe',
            'CallSid' => '8934dj83749hd874535934'
        ];

        $response = $this->call('POST', route('lead.store'), $requestParameters);

        // Then

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertContains('Dial', $response->getContent());
        $this->assertContains('+155005500', $response->getContent());

        $this->assertCount(1, Lead::all());

        $lead = $newLeadSource->leads()->first();

        $this->assertEquals('Boston', $lead->city);
        $this->assertEquals('MS', $lead->state);
        $this->assertEquals('+177007700', $lead->caller_number);
        $this->assertEquals('John Doe', $lead->caller_name);
        $this->assertEquals('8934dj83749hd874535934', $lead->call_sid);
    }

    public function testSummaries()
    {
        // Given

        $fakeNumberOne = '+1153614723';
        $fakeNumberTwo = '+1153619723';

        $leadSourceOne = new LeadSource(
            ['number' => '+1153614723',
             'description' => 'Downtown south billboard',
             'forwarding_number' => '+155005501']
        );

        $leadSourceTwo = new LeadSource(
            ['number' => '+1153619723',
             'description' => 'Downtown north billboard',
             'forwarding_number' => '+155005502']
        );

        $leadSourceOne->save();
        $leadSourceTwo->save();

        $leadOne = new Lead(
            ['caller_number' => '+148975933',
             'city' => 'Some city',
             'state' => 'Some state',
             'caller_name' => 'John Doe',
             'call_sid' => 'sup3runiq3']
        );

        $leadTwo = new Lead(
            ['caller_number' => '+149824734',
             'city' => 'Some other city',
             'state' => 'Some state',
             'caller_name' => 'John Doe',
             'call_sid' => 'sup3runiq3']
        );

        $leadOne->leadSource()->associate($leadSourceOne->id);
        $leadOne->save();
        $leadTwo->leadSource()->associate($leadSourceTwo->id);
        $leadTwo->save();

        // When

        $responseByCity = $this->call('GET', route('lead.summary_by_city'));
        $responseByLeadSource = $this->call('GET', route('lead.summary_by_lead_source'));

        // Then

        $responseContentOne = json_decode($responseByCity->getContent(), true);
        $responseContentTwo= json_decode($responseByLeadSource->getContent(), true);

        $this->assertEquals(
            [
                ["lead_count" => 1, "city" => "Some other city"],
                ["lead_count" => 1, "city" => "Some city"]
            ],
            $responseContentOne
        );
        $this->assertEquals(
            [
                ["lead_count" => 1, "description" => "Downtown south billboard", "number" => '+1153614723'],
                ["lead_count" => 1, "description" => "Downtown north billboard", "number" => '+1153619723'],
            ],
            $responseContentTwo
        );


    }
}