<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;

class AvailableNumberControllerTest extends TestCase
{
    public function testIndex()
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
        $response = $this->call('GET', route('available_number.index'));

        // Then
        $this->assertEquals(
            $response->getOriginalContent()['numbers'],
            $mockNumbers
        );

        $this->assertEquals(
            $response->getOriginalContent()['areaCode'],
            null
        );
    }
}