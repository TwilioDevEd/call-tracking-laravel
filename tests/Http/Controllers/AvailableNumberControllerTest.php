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
        $mockTwilioClient->availablePhoneNumbers = Mockery::mock();

        $mockUsPhones = Mockery::mock();
        $mockTwilioClient->availablePhoneNumbers
            ->shouldReceive("getContext")
            ->withAnyArgs()
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
