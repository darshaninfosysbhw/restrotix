<x-core::ui.form-modal id="employeeModal" backdrop-id="employeeModalBackdrop" title-id="employeeModalTitle"
    subtitle-id="employeeModalSubtitle" close-id="closeEmployeeModal" form-id="employeeForm"
    store-url="{{ route('admin.employee.store') }}" method-id="employeeFormMethod" cancel-id="cancelEmployeeModal"
    submit-id="employeeSubmitButton" title="Add New Employee" subtitle="Fill details to create a new employee entry"
    action="{{ route('admin.employee.store') }}" submit-label="Save Employee" max-width-class="max-w-xl"
    body-class="no-scrollbar flex-1 min-h-0 overflow-y-auto p-5 space-y-6"
    footer-class="flex-none flex justify-end gap-3 px-5 py-4 border-t border-gray-800 bg-gray-800"
    cancel-button-class="px-5 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-gray-300 transition duration-200 cursor-pointer"
    submit-button-class="px-5 py-2.5 rounded-lg text-sm bg-orange-500 hover:bg-orange-600 text-white font-semibold shadow-lg shadow-orange-500/20 transition duration-200 cursor-pointer"
    overlay-class="absolute inset-0 bg-black/60 backdrop-blur-sm">
    @include('admin.employee.partials.modal-fields')
</x-core::ui.form-modal>
