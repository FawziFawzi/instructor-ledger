<?php

namespace App\Http\Controllers;

use App\Contracts\RevenueAllocationServiceInterface;
use App\Models\Subscription;
use App\Models\SubscriptionInstructorShare;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->get();

        return view(
            'subscriptions.index',
            compact('plans')
        );
    }

    public function store(
        Request $request,
        RevenueAllocationServiceInterface $service
    ) {

        $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id']
        ]);


        $student = User::query()
            ->where('type', 'student')
            ->first();


        $plan = SubscriptionPlan::findOrFail(
            $request->plan_id
        );

        $platformFee = $plan->price * config('platform-fee.percentage');

        $subscription = Subscription::create([
            'student_id' => $student->id,

            'subscription_plan_id' => $plan->id,

            'amount' => $plan->price,

            'start_date' => now(),

            'end_date' => now()->addDays(
                $plan->duration_days
            ),
            'platform_fee_amount' => $platformFee,

            'status' => 'active',
        ]);

        $instructors = User::query()
            ->where('type', 'instructor')
            ->take(3)
            ->get();

        $percentages = [50, 30, 20];

        foreach ($instructors as $index => $instructor) {

            SubscriptionInstructorShare::create([

                'subscription_id' => $subscription->id,

                'instructor_id' => $instructor->id,

                'instructor_percentage' =>
                    $percentages[$index],

                'amount' => 0,
            ]);
        }


        $service->allocate($subscription);

        return redirect()
            ->back()
            ->with(
                'success',
                'Subscription purchased successfully.'
            );
    }
}
