@props([
    'isManager' => false,
    'activeWaiters' => [],
    'actionUrl' => route('waiter.table.transfer'), // Apne backend route ke hisaab se adjust kar sakte ho
])

<div x-data="{
    open: false,
    tableId: null,
    tableNumber: '',
    currentWaiterName: '',
    selectedWaiterId: '',
    transferNotes: '',
    isSubmitting: false,

    showValidationToast(message) {
        if (typeof window.showToast === 'function') {
            window.showToast({
                type: 'warning',
                message,
                duration: 3500,
            });
            return;
        }

        alert(message);
    },

    initTransfer(detail) {
        this.tableId = detail.tableId;
        this.tableNumber = detail.tableNumber || '';
        this.currentWaiterName = detail.currentWaiterName || '';
        this.selectedWaiterId = '';
        this.transferNotes = '';
        this.open = true;
    },

    submitTransfer() {
        if (!this.selectedWaiterId) {
            this.showValidationToast('Kripya target waiter select karein.');
            return;
        }

        if (!this.transferNotes && !{{ $isManager ? 'true' : 'false' }}) {
            this.showValidationToast('Kripya transfer ka reason select karein.');
            return;
        }

        this.isSubmitting = true;

        fetch('{{ $actionUrl }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                table_id: this.tableId,
                target_waiter_id: this.selectedWaiterId,
                notes: this.transferNotes,
                is_force_assign: {{ $isManager ? 'true' : 'false' }}
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.open = false;
                // Live table update event ya simple reload
                if (window.Livewire) {
                    Livewire.dispatch('refreshFloor');
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Transfer process fail ho gaya.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error, please try again.');
        })
        .finally(() => {
            this.isSubmitting = false;
        });
    }
}"
@open-transfer-modal.window="initTransfer($event.detail)"
x-show="open"
x-cloak
class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4"
style="display: none;">

    <div @click.away="if(!isSubmitting) open = false"
         class="w-full max-w-md bg-gray-900 border border-gray-700 text-white rounded-2xl p-5 sm:p-6 space-y-5 shadow-2xl">
       
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-800 pb-3.5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-500/10 text-orange-500 border border-orange-500/20 flex items-center justify-center text-sm">
                    <i class="fas fa-arrow-right-arrow-left"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base text-white">
                        {{ $isManager ? 'Re-assign Waiter' : 'Transfer Table' }}
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                        <span>Table: <strong class="text-orange-400" x-text="tableNumber"></strong></span>
                        <template x-if="currentWaiterName">
                            <span class="text-gray-500">• Current: <span x-text="currentWaiterName"></span></span>
                        </template>
                    </div>
                </div>
            </div>
            <button type="button" @click="open = false" class="text-gray-400 hover:text-white transition">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <!-- Target Waiter Selection -->
        <div class="space-y-2">
            <label class="text-xs font-bold uppercase tracking-wider text-gray-400 block">
                {{ $isManager ? 'Assign to Waiter' : 'Send Transfer To' }}
            </label>
            <div class="relative">
                <select x-model="selectedWaiterId"
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3.5 py-3 text-sm text-white focus:border-orange-500 focus:outline-none transition appearance-none cursor-pointer">
                    <option value="" class="bg-gray-800 text-gray-400">Select Active Waiter</option>
                    @foreach($activeWaiters as $waiter)
                        <option value="{{ $waiter->id }}" class="bg-gray-800 text-white">
                            {{ $waiter->name }} (Active)
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Notes / VIP Reason -->
        <div class="space-y-2">
            <label class="text-xs font-bold uppercase tracking-wider text-gray-400 block">
                Note / Remarks{{ $isManager ? '' : ' *' }}
            </label>
            <div class="relative">
                <select x-model="transferNotes" {{ $isManager ? '' : 'required' }}
                        class="w-full appearance-none cursor-pointer bg-gray-800 border border-gray-700 rounded-xl px-3.5 py-3 pr-10 text-sm text-white focus:border-orange-500 focus:outline-none transition">
                    <option value="" class="bg-gray-800 text-gray-400">Select a reason</option>
                    <option value="VIP guest" class="bg-gray-800 text-white">VIP guest</option>
                    <option value="Rush at other table" class="bg-gray-800 text-white">Rush at other table</option>
                    <option value="Special guest request" class="bg-gray-800 text-white">Special guest request</option>
                    <option value="Waiter availability" class="bg-gray-800 text-white">Shift End</option>
                    <option value="Customer requested waiter change" class="bg-gray-800 text-white">Customer requested waiter change</option>
                    <option value="Waiter availability" class="bg-gray-800 text-white">Waiter availability</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-2 flex items-center gap-3">
            <button type="button"
                    @click="open = false"
                    :disabled="isSubmitting"
                    class="w-1/2 py-3 rounded-xl border border-gray-700 hover:bg-red-100 text-gray-300 text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                Cancel
            </button>

            <button type="button"
                    @click="submitTransfer()"
                    :disabled="isSubmitting"
                    class="w-1/2 bg-orange-600 hover:bg-orange-500 text-white py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-orange-600/20 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fas fa-paper-plane text-xs" x-show="!isSubmitting"></i>
                <span x-show="!isSubmitting">{{ $isManager ? 'Confirm Assign' : 'Request Transfer' }}</span>
                <span x-show="isSubmitting">Please wait...</span>
            </button>
        </div>

    </div>
  </div>  