<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-slate-900">Safari Rates & Payment Schedule</h2>
        <x-action-button type="import" size="sm" label="Import from Safari Office" @click="$dispatch('open-import-modal')" />
    </div>

    @foreach($booking->groups as $group)
        <div class="border border-slate-200 rounded-xl mb-6 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h3 class="font-semibold text-slate-900">Group {{ $group->group_number }} - Base Safari Rates</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Traveler</th>
                            <th class="text-right">Safari Rate</th>
                            <th class="text-right">Add-ons</th>
                            <th class="text-right">Credits</th>
                            <th class="text-right">Total Rate</th>
                            <th class="text-right">Deposit</th>
                            <th class="text-right">90-Day</th>
                            <th class="text-right">45-Day</th>
                            <th class="text-center">Status</th>
                            @if(auth()->user()->isSuperAdmin())
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupTotal = 0;
                            $groupAddons = 0;
                            $groupCredits = 0;
                            $groupTotalRate = 0;
                            $groupDeposit = 0;
                            $group90Day = 0;
                            $group45Day = 0;
                        @endphp
                        @foreach($group->travelers as $traveler)
                            @php
                                $payment = $traveler->payment;
                                $safariRate = $payment ? $payment->safari_rate : 0;
                                $deposit = $payment ? $payment->deposit : 0;
                                $payment90 = $payment ? $payment->payment_90_day : 0;
                                $payment45 = $payment ? $payment->payment_45_day : 0;

                                // Calculate add-ons and credits for this traveler
                                $travelerAddons = $traveler->addons->where('type', '!=', 'credit')->sum('cost_per_person');
                                $travelerCredits = $traveler->addons->where('type', 'credit')->sum('cost_per_person');
                                $totalRate = $safariRate + $travelerAddons - $travelerCredits;

                                $groupTotal += $safariRate;
                                $groupAddons += $travelerAddons;
                                $groupCredits += $travelerCredits;
                                $groupTotalRate += $totalRate;
                                $groupDeposit += $deposit;
                                $group90Day += $payment90;
                                $group45Day += $payment45;
                            @endphp
                            <tr wire:key="payment-row-{{ $traveler->id }}">
                                <td>
                                    <div class="font-medium text-slate-900">
                                        {{ $traveler->first_name }} {{ $traveler->last_name }}
                                        @if($traveler->is_lead)
                                            <span class="text-xs text-orange-600">(Lead)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-right">
                                    @if($payment)
                                        @if($editingPaymentId === $payment->id)
                                            {{-- Editing mode --}}
                                            <div class="flex items-center gap-2 justify-end">
                                                <input type="number" wire:model="editingSafariRate" step="0.01" min="0"
                                                    class="w-28 text-right rounded border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"
                                                    wire:keydown.enter="updateSafariRate({{ $payment->id }})"
                                                    wire:keydown.escape="cancelEditing">
                                                <div class="flex gap-1">
                                                    <button type="button" wire:click="updateSafariRate({{ $payment->id }})" class="text-xs text-green-600 hover:text-green-800">Save</button>
                                                    <button type="button" wire:click="cancelEditing" class="text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                                                </div>
                                            </div>
                                        @elseif($payment->deposit_locked)
                                            {{-- Locked rate - show value with edit option for super admins --}}
                                            <div class="flex items-center gap-2 justify-end">
                                                <div>
                                                    <span class="font-medium text-slate-900">${{ number_format($safariRate, 2) }}</span>
                                                    @if($payment->original_rate && $payment->original_rate != $payment->safari_rate)
                                                        <div class="text-xs text-slate-500">Original: ${{ number_format($payment->original_rate, 2) }}</div>
                                                    @endif
                                                </div>
                                                @if(auth()->user()->isSuperAdmin())
                                                    <button type="button" wire:click="startEditing({{ $payment->id }}, {{ $safariRate }})"
                                                        class="text-xs text-orange-600 hover:text-orange-800">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            {{-- Not locked yet - show editable field --}}
                                            <div class="flex items-center gap-2 justify-end">
                                                <span class="font-medium text-slate-900 cursor-pointer hover:text-orange-600"
                                                    wire:click="startEditing({{ $payment->id }}, {{ $safariRate }})"
                                                    title="Click to edit">
                                                    ${{ number_format($safariRate, 2) }}
                                                </span>
                                                <button type="button" wire:click="startEditing({{ $payment->id }}, {{ $safariRate }})"
                                                    class="text-xs text-slate-400 hover:text-orange-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    @else
                                        {{-- No payment yet - show input to create --}}
                                        <div x-data="{ rate: 0 }">
                                            <input type="number" x-model="rate" step="0.01" min="0"
                                                class="w-28 text-right rounded border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"
                                                @change="if(rate > 0) $wire.createPayment({{ $traveler->id }}, rate)">
                                        </div>
                                    @endif
                                </td>
                                {{-- Add-ons column --}}
                                <td class="text-right text-slate-600">
                                    @if($travelerAddons > 0)
                                        ${{ number_format($travelerAddons, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                {{-- Credits column --}}
                                <td class="text-right text-blue-600">
                                    @if($travelerCredits > 0)
                                        -${{ number_format($travelerCredits, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                {{-- Total Rate column --}}
                                <td class="text-right font-semibold text-slate-900">
                                    ${{ number_format($totalRate, 2) }}
                                </td>
                                {{-- Deposit column with paid/unpaid toggle --}}
                                <td class="text-right">
                                    <div class="text-slate-600">${{ number_format($deposit, 2) }}</div>
                                    @if($payment && $payment->deposit_locked)
                                        @if($payment->deposit_paid)
                                            <span class="text-xs text-green-600">Paid</span>
                                        @else
                                            @if(auth()->user()->isSuperAdmin())
                                                <button type="button" wire:click="togglePaid({{ $payment->id }}, 'deposit')"
                                                    class="text-xs text-orange-600 hover:text-orange-800">Mark Paid</button>
                                            @else
                                                <span class="text-xs text-orange-600">Due</span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                {{-- 90-Day column --}}
                                <td class="text-right">
                                    <div class="text-slate-600">${{ number_format($payment90, 2) }}</div>
                                    @if($payment && $payment->deposit_locked)
                                        @if($payment->payment_90_day_paid)
                                            <span class="text-xs text-green-600">Paid</span>
                                        @else
                                            @if(auth()->user()->isSuperAdmin())
                                                <button type="button" wire:click="togglePaid({{ $payment->id }}, 'payment_90_day')"
                                                    class="text-xs text-orange-600 hover:text-orange-800">Mark Paid</button>
                                            @else
                                                <span class="text-xs text-orange-600">Due</span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                {{-- 45-Day column --}}
                                <td class="text-right">
                                    <div class="text-slate-600">${{ number_format($payment45, 2) }}</div>
                                    @if($payment && $payment->deposit_locked)
                                        @if($payment->payment_45_day_paid)
                                            <span class="text-xs text-green-600">Paid</span>
                                        @else
                                            @if(auth()->user()->isSuperAdmin())
                                                <button type="button" wire:click="togglePaid({{ $payment->id }}, 'payment_45_day')"
                                                    class="text-xs text-orange-600 hover:text-orange-800">Mark Paid</button>
                                            @else
                                                <span class="text-xs text-orange-600">Due</span>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                {{-- Status column --}}
                                <td class="text-center">
                                    @if($payment)
                                        @if($payment->deposit_paid && $payment->payment_90_day_paid && $payment->payment_45_day_paid)
                                            <span class="badge badge-success text-xs">Paid in Full</span>
                                        @elseif($payment->deposit_locked)
                                            <span class="badge badge-warning text-xs">Payments Due</span>
                                        @else
                                            <span class="badge text-xs" style="background: #f1f5f9; color: #475569;">Pending Lock</span>
                                        @endif
                                    @else
                                        <span class="badge text-xs" style="background: #fef3c7; color: #92400e;">No Payment</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isSuperAdmin())
                                    <td class="text-center">
                                        @if($payment && !$payment->deposit_locked)
                                            <form method="POST" action="{{ route('payments.lock', $payment) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs text-orange-600 hover:text-orange-800"
                                                    onclick="return confirm('Lock this payment schedule? This will enable payment tracking and cannot be easily undone.')">
                                                    Lock Schedule
                                                </button>
                                            </form>
                                        @elseif($payment && $payment->deposit_locked)
                                            <span class="text-xs text-green-600">Locked</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        {{-- Group totals row --}}
                        <tr class="bg-slate-50 font-semibold">
                            <td>Group {{ $group->group_number }} Total</td>
                            <td class="text-right">${{ number_format($groupTotal, 2) }}</td>
                            <td class="text-right">${{ number_format($groupAddons, 2) }}</td>
                            <td class="text-right text-blue-600">-${{ number_format($groupCredits, 2) }}</td>
                            <td class="text-right">${{ number_format($groupTotalRate, 2) }}</td>
                            <td class="text-right">${{ number_format($groupDeposit, 2) }}</td>
                            <td class="text-right">${{ number_format($group90Day, 2) }}</td>
                            <td class="text-right">${{ number_format($group45Day, 2) }}</td>
                            <td></td>
                            @if(auth()->user()->isSuperAdmin())
                                <td></td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
