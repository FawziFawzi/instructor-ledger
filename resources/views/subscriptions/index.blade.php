<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Subscriptions</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="bg-gray-100 min-h-screen">

<div
    x-data="{ selectedPlan: null }"
    class="max-w-5xl mx-auto py-16"
>

    <h1 class="text-4xl font-bold mb-10">
        Choose Subscription Plan
    </h1>

    @if(session('success'))

        <div class="bg-green-200 p-4 rounded mb-6">
            {{ session('success') }}
        </div>

    @endif

    <div class="grid grid-cols-3 gap-6">

        @foreach($plans as $plan)

            <div
                @click="selectedPlan = {{ $plan->id }}"
                class="bg-white rounded-2xl p-6 border cursor-pointer transition"

                :class="
                    selectedPlan === {{ $plan->id }}
                    ? 'border-black shadow-lg'
                    : 'border-gray-200'
                "
            >

                <h2 class="text-2xl font-bold">
                    {{ $plan->name }}
                </h2>

                <div class="mt-4 text-4xl font-bold">
                    ${{ number_format($plan->price / 100, 2) }}
                </div>

                <div class="mt-2 text-gray-500">
                    {{ $plan->duration_days }} days
                </div>

            </div>

        @endforeach

    </div>

    <form
        method="POST"
        action="/subscriptions"
        class="mt-10"
    >
        @csrf

        <input
            type="hidden"
            name="plan_id"
            x-model="selectedPlan"
        >

        <button
            type="submit"
            class="bg-black text-white px-8 py-4 rounded-xl"
        >
            Purchase Subscription
        </button>

    </form>

</div>

</body>
</html>
