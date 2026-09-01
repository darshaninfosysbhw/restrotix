<x-core::ui.form-modal id="branchModal" backdrop-id="branchModalBackdrop" title-id="branchModalTitle"
    subtitle-id="branchModalSubtitle" close-id="closeBranchModal" form-id="branchForm"
    store-url="{{ route('admin.branches.store') }}" method-id="branchFormMethod" cancel-id="cancelBranchModal"
    submit-id="branchSubmitButton" title="Add New Branch" subtitle="Fill details to create a new branch entry"
    action="{{ route('admin.branches.store') }}" submit-label="Save Branch" submit-icon="fas fa-save"
    submit-icon-class="mr-2" max-width-class="max-w-xl" body-class="no-scrollbar flex-1 min-h-0 overflow-y-auto p-5 space-y-4"
    footer-class="flex-none flex justify-end gap-2 px-5 py-4 border-t border-gray-700 bg-gray-800"
    cancel-button-class="px-4 py-2.5 rounded-lg text-sm bg-white/5 hover:bg-white/10 text-gray-300 transition cursor-pointer"
    submit-button-class="px-4 py-2.5 rounded-lg text-sm bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 border border-orange-500/30 transition cursor-pointer"
    overlay-class="absolute inset-0 bg-black/60 backdrop-blur-sm">
    @include('admin.branches.partials.modal-fields')
</x-core::ui.form-modal>
