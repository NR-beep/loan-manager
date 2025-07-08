<div class="overflow-x-auto">
    <h2 class="text-lg font-semibold mb-4">Loan #{{ $loan->id }} Payments</h2>
    <table class="table-auto w-full border border-gray-200 text-sm">
        <thead class="bg-gray-100 text-gray-700">
        <tr>
            <th class="p-2 border border-gray-200">#</th>
            <th class="p-2 border border-gray-200">Date</th>
            <th class="p-2 border border-gray-200">Principal</th>
            <th class="p-2 border border-gray-200">Interest</th>
            <th class="p-2 border border-gray-200">Total</th>
            <th class="p-2 border border-gray-200">Balance</th>
        </tr>
        </thead>
        <tbody class="text-gray-800">
        @foreach ($payments as $index => $payment)
        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
            <td class="p-2 border border-gray-200">{{ $index + 1 }}</td>
            <td class="p-2 border border-gray-200">{{ $payment->payment_date }}</td>
            <td class="p-2 border border-gray-200">{{ number_format($payment->principal, 2) }}</td>
            <td class="p-2 border border-gray-200">{{ number_format($payment->interest, 2) }}</td>
            <td class="p-2 border border-gray-200">{{ number_format($payment->principal + $payment->interest, 2) }}</td>
            <td class="p-2 border border-gray-200">{{ number_format($payment->balance, 2) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
