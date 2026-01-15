@props(['client' => null])

<div id="edit-client-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit Client</h3>
        <form id="edit-client-form" method="POST" action="{{ $client ? route('travelers.update', $client) : '' }}">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">First Name</label>
                        <input type="text" name="first_name" id="edit-client-first-name" value="{{ $client?->first_name }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Last Name</label>
                        <input type="text" name="last_name" id="edit-client-last-name" value="{{ $client?->last_name }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" id="edit-client-email" value="{{ $client?->email }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                    <input type="text" name="phone" id="edit-client-phone" value="{{ $client?->phone }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                    <input type="date" name="dob" id="edit-client-dob" value="{{ $client?->dob?->format('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_lead" id="edit-client-is-lead" {{ $client?->is_lead ? 'checked' : '' }} class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                    <label for="edit-client-is-lead" class="text-sm text-slate-700">Lead Traveler</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('edit-client-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditClientModal(clientId, firstName, lastName, email, phone, dob, isLead) {
        const form = document.getElementById('edit-client-form');
        if (clientId) {
            form.action = `/travelers/${clientId}`;
            document.getElementById('edit-client-first-name').value = firstName || '';
            document.getElementById('edit-client-last-name').value = lastName || '';
            document.getElementById('edit-client-email').value = email || '';
            document.getElementById('edit-client-phone').value = phone || '';
            document.getElementById('edit-client-dob').value = dob || '';
            document.getElementById('edit-client-is-lead').checked = isLead || false;
        }
        document.getElementById('edit-client-modal').classList.remove('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('edit-client-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
</script>
