<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-slate-900">Activity Log</h2>
    </div>

    <!-- Log Activity Form -->
    <form wire:submit="addNote" class="mb-6 p-4 bg-slate-50 rounded-xl">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Log Activity</label>
                <textarea wire:model="notes" rows="2" placeholder="Record a call, email, or other activity..." class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required></textarea>
                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-action-button type="add" label="Log Activity" :submit="true" />
            </div>
        </div>
    </form>

    <!-- Activity Timeline -->
    <div class="space-y-4">
        @forelse($logs as $log)
            <div class="flex gap-4" wire:key="log-{{ $log->id }}">
                <div class="flex-shrink-0">
                    @if($log->action_type === 'manual')
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <span class="text-orange-600 font-semibold text-sm">
                                {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                            </span>
                        </div>
                    @else
                        <div class="w-10 h-10 {{ $log->action_color }} rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $log->action_icon }}" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1 bg-white border border-slate-200 rounded-xl p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-slate-900">
                                {{ $log->user->name ?? 'System' }}
                                @if($log->action_type !== 'manual')
                                    <span class="ml-2 text-xs px-2 py-0.5 bg-slate-100 text-slate-600 rounded">Auto</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500">{{ $log->created_at->format('M j, Y g:i A') }} ({{ $log->created_at->diffForHumans() }})</div>
                        </div>
                        @if($log->action_type === 'manual')
                            <x-action-button type="delete" size="xs" :icon="false" wire:click="deleteLog({{ $log->id }})" wire:confirm="Delete this note?" />
                        @endif
                    </div>
                    <div class="mt-2 text-slate-700">{{ $log->notes }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-slate-500">
                <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>No activity yet</p>
            </div>
        @endforelse
    </div>
</div>
