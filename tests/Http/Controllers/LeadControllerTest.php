<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;

use App\LeadSource;
use App\Lead;

class LeadControllerTest extends TestCase
{
    use DatabaseTransactions;

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

}