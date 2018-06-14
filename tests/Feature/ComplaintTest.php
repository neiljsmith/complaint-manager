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

    public function testFindMatchingEmailOrAccountNo()
    {
        // At least one user required to create customers
        factory(User::class)->create([
            'line_manager_user_id' => 1,
        ]);

        $customersData = [
            ['account_number' => 11111111, 'email' => 'aaaaa@example.net'],
            ['account_number' => 11111112, 'email' => 'aaaab@example.net'],
            ['account_number' => 11111113, 'email' => 'aaaac@example.net'],
            ['account_number' => 11211114, 'email' => 'aaaad@example.net'],
            ['account_number' => 11211115, 'email' => 'aabae@example.net'],
            ['account_number' => 11211116, 'email' => 'aabaaf@example.net'],
            ['account_number' => 11311117, 'email' => 'aacag@example.net'],
            ['account_number' => 11411118, 'email' => 'aadah@example.net'],
            ['account_number' => 11511119, 'email' => 'aaeai@example.net'],
            ['account_number' => 11611120, 'email' => 'aafaj@example.net'],
            ['account_number' => 11711121, 'email' => 'aagak@example.net'],
            ['account_number' => 11811122, 'email' => 'aahal@example.net'],
            ['account_number' => 11911123, 'email' => 'aaiam@example.net'],
            ['account_number' => 12011124, 'email' => 'aajan@example.net'],
            ['account_number' => 12111125, 'email' => 'baaao@example.net'],
        ];
        $customers = [];
        foreach ($customersData as $customerData) {
            $customers[] = factory(Customer::class)->create($customerData);
            
        }

        foreach ($customers as $customer) {
            factory(Complaint::class)->create(['user_id' => 1, 'customer_id' => $customer->id]);
        }

        $searchResult = Customer::findMatchingEmailOrAccountNo('aaa');        
        $this->assertEquals(4, $searchResult->count());

        $searchResult = Customer::findMatchingEmailOrAccountNo('baa');        
        $this->assertEquals(1, $searchResult->count());

        $searchResult = Customer::findMatchingEmailOrAccountNo('baz');        
        $this->assertEquals(0, $searchResult->count());

        $searchResult = Customer::findMatchingEmailOrAccountNo('111');        
        $this->assertEquals(3, $searchResult->count());

        $searchResult = Customer::findMatchingEmailOrAccountNo('113');        
        $this->assertEquals(1, $searchResult->count());

        $searchResult = Customer::findMatchingEmailOrAccountNo('999');        
        $this->assertEquals(0, $searchResult->count());

    }

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
