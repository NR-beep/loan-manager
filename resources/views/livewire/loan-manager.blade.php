<div class="p-6 space-y-6">
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
        <input type="number" wire:model="loan_amount" placeholder="Loan Amount" class="border rounded px-3 py-2 w-full">
        @error('loan_amount') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <input type="number" wire:model="interest_rate" step="0.01" placeholder="Interest Rate (%)" class="border rounded px-3 py-2 w-full">
        @error('interest_rate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <input type="number" wire:model="loan_term" placeholder="Loan Term (months)" class="border rounded px-3 py-2 w-full">
        @error('loan_term') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <input type="date" wire:model="start_date" class="border rounded px-3 py-2 w-full">
        @error('start_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <button type="submit" class="bg-blue-600 text-black px-4 py-2 rounded">
            {{ $isEdit ? 'Update' : 'Add' }} Loan
        </button>
    </form>

    <hr class="my-6 border-gray-300">

    <h2 class="text-xl font-semibold">Existing Loans</h2>

    <table class="w-full border-collapse border border-gray-300 mt-2 text-sm">
        <thead>
        <tr class="bg-gray-100">
            <th class="border px-2 py-1 text-left">Amount</th>
            <th class="border px-2 py-1 text-left">Rate</th>
            <th class="border px-2 py-1 text-left">Term</th>
            <th class="border px-2 py-1 text-left">Start Date</th>
            <th class="border px-2 py-1 text-left">Status</th>
            <th class="border px-2 py-1 text-left">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($loans as $loan)
        <tr>
            <td class="border px-2 py-1">{{ $loan->loan_amount }}</td>
            <td class="border px-2 py-1">{{ $loan->interest_rate }}%</td>
            <td class="border px-2 py-1">{{ $loan->loan_term }} months</td>
            <td class="border px-2 py-1">{{ $loan->start_date }}</td>
            <td class="border px-2 py-1">
                <select wire:change="updateStatus({{ $loan->id }}, $event.target.value)" class="border rounded px-1 py-0.5">
                    @foreach(['pending', 'active', 'repaid'] as $status)
                    <option value="{{ $status }}" @selected($loan->status === $status)>
                        {{ ucfirst($status) }}
                    </option>
                    @endforeach
                </select>
            </td>
            <td class="border px-2 py-1 space-y-1">
                <div class="flex gap-2">
                    <button wire:click="edit({{ $loan->id }})" class="text-blue-600 underline">Edit</button>
                    <button wire:click="delete({{ $loan->id }})" class="text-red-600 underline">Delete</button>
                </div>
                <a href="{{ route('loans.payments', ['loanId' => $loan->id]) }}">
                    <button type="button" class="mt-1 text-green-700 underline">View Schedule</button>
                </a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @if (!empty($schedule))
    <h3 class="text-lg font-semibold mt-6">Amortization Schedule</h3>
    <table class="w-full border-collapse border border-gray-300 text-sm mt-2">
        <thead class="bg-gray-100">
        <tr>
            <th class="border px-2 py-1">Payment #</th>
            <th class="border px-2 py-1">Date</th>
            <th class="border px-2 py-1">Principal</th>
            <th class="border px-2 py-1">Interest</th>
            <th class="border px-2 py-1">Total</th>
            <th class="border px-2 py-1">Remaining Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($schedule as $entry)
        <tr>
            <td class="border px-2 py-1">{{ $entry['number'] }}</td>
            <td class="border px-2 py-1">{{ $entry['date'] }}</td>
            <td class="border px-2 py-1">{{ $entry['principal'] }}</td>
            <td class="border px-2 py-1">{{ $entry['interest'] }}</td>
            <td class="border px-2 py-1">{{ $entry['total'] }}</td>
            <td class="border px-2 py-1">{{ $entry['balance'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
