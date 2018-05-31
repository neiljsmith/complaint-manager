<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Complaint;
use App\User;
use App\Customer;
use App\ComplaintNote;
use App\Reward;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    public function testFindWithDetails()
    {
        $user = factory(User::class)->create(['line_manager_user_id' => 0]);
        $customer = factory(Customer::class)->create();
        $testComplaint = factory(Complaint::class)->create();
        $testComplaint->reward()->save(factory(Reward::class)->create([
            'value' => 10,
            'reward_provider_id' => 2
        ]));
        $testComplaint->complaintNotes()->save(factory(ComplaintNote::class)->create([
            'created_at' => '2018-01-02',
            'content' => 'latest'
        ]));
        $testComplaint->complaintNotes()->save(factory(ComplaintNote::class)->create([
            'created_at' => '2018-01-01',
            'content' => 'earliest'
        ]));

        $complaint = Complaint::findWithDetails($testComplaint->id);

        $this->assertEquals($testComplaint->id, $complaint->id);
        $this->assertEquals($testComplaint->customer->id, $complaint->customer->id);
        $this->assertEquals($testComplaint->user->id, $complaint->user->id);
        $this->assertEquals(10, $complaint->reward->value);
        $this->assertEquals(2, $complaint->reward->reward_provider_id);
        $this->assertCount(2, $complaint->complaintNotes()->get());
        $this->assertEquals('latest', $complaint->complaintNotes()->first()->content);
    }
}
