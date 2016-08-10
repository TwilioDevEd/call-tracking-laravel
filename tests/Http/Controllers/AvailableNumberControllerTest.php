<?php

use Illuminate\Support\Facades\App;
use Twilio\Rest\Client;

class AvailableNumberControllerTest extends TestCase
{

    public function testIndex()
    {
        // Given
        $mockNumber = Mockery::mock();

        $mockNumber->friendlyName = '(555) 444 444';
        $mockNumber->region = 'Some region';
        $mockNumber->phoneNumber = '+1555444444';

        $mockNumbers = [$mockNumber];

        $mockTwilioClient = Mockery::mock(Client::class);

        $mockUsPhones = Mockery::mock();
        $mockTwilioClient
            ->shouldReceive("availablePhoneNumbers")
            ->withAnyArgs("US")
            ->andReturn($mockUsPhones);

        $mockUsPhones->local = Mockery::mock();
        $mockUsPhones->local
            ->shouldReceive('stream')
            ->andReturn($mockNumbers);

        App::instance(Client::class, $mockTwilioClient);

        //Then

        $this->visit(route('available_number.index'))
            ->assertViewHas("numbers", $mockNumbers);
    }
}
