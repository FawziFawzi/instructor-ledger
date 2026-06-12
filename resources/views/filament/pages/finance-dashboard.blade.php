<x-filament::page>

    {{-- Instructor Balances --}}

    <div class="mb-10">

        <h2 class="
            text-2xl
            font-bold
            mb-4
            text-gray-900
            dark:text-white
        ">
            Instructor Balances
        </h2>

        <div class="
            overflow-hidden
            rounded-xl
            border
            border-gray-200
            dark:border-gray-700
            bg-white
            dark:bg-gray-900
            shadow
        ">

            <table class="w-full">

                <thead class="
                    bg-gray-100
                    dark:bg-gray-800
                ">

                <tr>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Instructor
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Total
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Pending
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Available
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach($balances as $balance)

                    <tr class="
                            border-t
                            border-gray-200
                            dark:border-gray-700
                        ">

                        <td class="
                                p-4
                                text-gray-900
                                dark:text-white
                            ">
                            {{ $balance->instructor->name }}
                        </td>

                        <td class="
                                p-4
                                text-gray-700
                                dark:text-gray-300
                            ">
                            $
                            {{
                                number_format(
                                    $balance->total_balance / 100,
                                    2
                                )
                            }}
                        </td>

                        <td class="
                                p-4
                                text-yellow-700
                                dark:text-yellow-400
                            ">
                            $
                            {{
                                number_format(
                                    $balance->pending_balance / 100,
                                    2
                                )
                            }}
                        </td>

                        <td class="
                                p-4
                                text-green-700
                                dark:text-green-400
                            ">
                            $
                            {{
                                number_format(
                                    $balance->available_balance / 100,
                                    2
                                )
                            }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

    {{-- Payout History --}}

    <div>

        <h2 class="
            text-2xl
            font-bold
            mb-4
            text-gray-900
            dark:text-white
        ">
            Payout History
        </h2>

        <div class="
            overflow-hidden
            rounded-xl
            border
            border-gray-200
            dark:border-gray-700
            bg-white
            dark:bg-gray-900
            shadow
        ">

            <table class="w-full">

                <thead class="
                    bg-gray-100
                    dark:bg-gray-800
                ">

                <tr>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Instructor
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Amount
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Status
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Idempotency Key
                    </th>

                    <th class="
                            p-4
                            text-left
                            text-gray-700
                            dark:text-gray-200
                        ">
                        Date
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach($payouts as $payout)

                    <tr class="
                            border-t
                            border-gray-200
                            dark:border-gray-700
                        ">

                        <td class="
                                p-4
                                text-gray-900
                                dark:text-white
                            ">
                            {{ $payout->instructor->name }}
                        </td>

                        <td class="
                                p-4
                                text-gray-700
                                dark:text-gray-300
                            ">
                            $
                            {{
                                number_format(
                                    $payout->amount / 100,
                                    2
                                )
                            }}
                        </td>

                        <td class="p-4">

                                <span
                                    class="
                                        px-3
                                        py-1
                                        rounded-full
                                        text-sm

                                        @if($payout->status->value === 'success')
                                            bg-green-100
                                            text-green-700
                                            dark:bg-green-900
                                            dark:text-green-300

                                        @elseif($payout->status->value === 'failed')
                                            bg-red-100
                                            text-red-700
                                            dark:bg-red-900
                                            dark:text-red-300

                                        @elseif($payout->status->value === 'pending_verification')
                                            bg-yellow-100
                                            text-yellow-700
                                            dark:bg-yellow-900
                                            dark:text-yellow-300

                                        @else
                                            bg-gray-100
                                            text-gray-700
                                            dark:bg-gray-800
                                            dark:text-gray-300
                                        @endif
                                    "
                                >
                                    {{ $payout->status->value }}
                                </span>

                        </td>

                        <td class="
                                p-4
                                text-xs
                                text-gray-600
                                dark:text-gray-400
                            ">
                            {{ $payout->idempotency_key }}
                        </td>

                        <td class="
                                p-4
                                text-gray-700
                                dark:text-gray-300
                            ">
                            {{ $payout->created_at }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</x-filament::page>
