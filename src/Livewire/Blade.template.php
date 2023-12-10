<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid-cols-6"></div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="bg-gray-50 px-6 py-4 lg:px-8 lg:py-6">
                    <div class="grid grid-cols-2">
                        <div>
                            Show <x-input wire:model.live="collection_limit" type="number" min="5" max="30" class="w-fit" /> entries
                        </div>
                        <div class="justify-self-end">
                            <x-input wire:model.live="search" type="text" class="block" placeholder="Search" />
                        </div>
                    </div>
                </div>

                <div class="p-6 lg:p-8 bg-white">
                    <div class="grid grid-cols-{{ count($fields_shown)+1 }} gap-1">
                        @foreach( $fields_shown as $field_shown )
                        <div class="bg-indigo-50 text-center text-sm px-2 py-1 rounded font-bold border border-indigo-300">{{ $fields[$field_shown] }}</div>
                        @endforeach
                        <div class="bg-indigo-50 text-center text-sm px-2 py-1 rounded font-bold border border-indigo-300">Actions</div>

                        @foreach( $collection as $paginated_item )
                        @foreach( $fields_shown as $field_shown )
                        <div class="border px-2 py-1 rounded break-words">{{ $paginated_item->{$field_shown} }}</div>
                        @endforeach
                        <div class="border px-2 py-1 rounded text-center">
                            <button wire:click="edit({{ $paginated_item->{$primary_key} }})" class="inline-flex items-center gap-2 m-1 ~text-gray-500 border-indigo-500/50 hover:bg-indigo-50 hover:border-indigo-500 px-3 py-1 rounded-xl bg-opacity-20 border text-sm font-medium">Edit</button>
                            <button wire:click="delete({{ $paginated_item->{$primary_key} }})" class="inline-flex items-center gap-2 m-1 ~text-gray-500 border-red-500/50 hover:bg-red-50 hover:border-red-500 px-1.5 py-1 rounded-xl bg-opacity-20 border text-sm font-medium">Delete</button>
                        </div>
                        @endforeach
                        @foreach( $fields_shown as $field_shown )
                        <div class="">&nbsp;</div>
                        @endforeach
                        <div class="p-3 text-center">
                            <button wire:click="add()" class="inline-flex items-center gap-2 ~text-gray-500 border-indigo-500/50 hover:bg-indigo-50 hover:border-indigo-500 px-3 py-1 rounded-xl bg-opacity-20 border text-sm font-medium">Add</button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 lg:px-8 lg:py-6">
                    <div class="grid grid-cols-2 items-center">
                        <div class="text-sm">Showing {{ $collection_start_offset }} to {{ $collection_end_offset }} of {{ $collection_total_entries }} entries</div>
                        <div class="flex justify-self-end">

                        <button wire:click="setPage(1)" class="bg-white w-fit py-1 px-2 font-mono border" title="First page">&laquo;</button>
                        <button wire:click="setPage({{ ($page>1 ? $page-1 : 1) }})" class="bg-white w-fit py-1 px-2 font-mono border" title="Previous page">&lsaquo;</button>
                        @for( $p=1; $p<=$pages; $p++ )
                        <button wire:click="setPage({{ $p }})" class="bg-white w-fit py-1 px-2 font-mono border {{ ($p==$page ? 'text-black font-bold' : 'text-gray-700') }}">{{ $p }}</button>
                        @endfor
                        <button wire:click="setPage({{ ($pages>$page ? $page+1 : $pages) }})" class="bg-white w-fit py-1 px-2 font-mono border" title="Next page">&rsaquo;</button>
                        <button wire:click="setPage({{ $pages }})" class="bg-white w-fit py-1 px-2 font-mono border" title="Last page">&raquo;</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit modal -->
    @if( in_array($mode, ['add','edit']) )
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!--
        Background backdrop, show/hide based on modal state.

        Entering: "ease-out duration-300"
        From: "opacity-0"
        To: "opacity-100"
        Leaving: "ease-in duration-200"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity {{ (in_array($mode, ['add','edit']) ? 'ease-in duration-200 opacity-100' : 'ease-out duration-300 opacity-0') }}"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!--
            Modal panel, show/hide based on modal state.

            Entering: "ease-out duration-300"
            From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            To: "opacity-100 translate-y-0 sm:scale-100"
            Leaving: "ease-in duration-200"
            From: "opacity-100 translate-y-0 sm:scale-100"
            To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        -->
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg {{ (in_array($mode, ['add','edit']) ? 'ease-in duration-200 opacity-100 translate-y-0 sm:scale-100' : 'ease-out duration-300 opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95') }}">
            <div class="bg-gray-50 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                    @if ($mode=='add')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    @elseif ($mode=='edit')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    @endif
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                    @if ($mode=='add')
                        Add item
                    @elseif ($mode=='edit')
                        Edit item
                    @endif
                </h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">
                        @if ($mode=='add')
                        Let's get started! Create a new item in the system.
                        @elseif ($mode=='edit')
                        Update the details below to properly define this item. Your changes make a difference in refining the overall system.
                        @endif
                    </p>
                </div>
                </div>
            </div>
            </div>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-1 items-center">{input-fields-edit}
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" wire:click="save()" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Save</button>
                <button type="button" wire:click="cancelAddorEdit()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
            </div>
        </div>
        </div>
    </div>
    </div>
    @endif

    <!-- Delete modal -->
    @if( $mode == 'delete' )
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!--
        Background backdrop, show/hide based on modal state.

        Entering: "ease-out duration-300"
        From: "opacity-0"
        To: "opacity-100"
        Leaving: "ease-in duration-200"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity {{ ($mode == 'delete' ? 'ease-in duration-200 opacity-100' : 'ease-out duration-300 opacity-0') }}"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!--
            Modal panel, show/hide based on modal state.

            Entering: "ease-out duration-300"
            From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            To: "opacity-100 translate-y-0 sm:scale-100"
            Leaving: "ease-in duration-200"
            From: "opacity-100 translate-y-0 sm:scale-100"
            To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        -->
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg {{ ($mode == 'delete' ? 'ease-in duration-200 opacity-100 translate-y-0 sm:scale-100' : 'ease-out duration-300 opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95') }}">
            <div class="bg-red-50 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-200 sm:mx-0 sm:h-10 sm:w-10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Delete item</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">Caution! You are about to delete this item. Confirm this action to remove it from the system. Please proceed wisely.</p>
                </div>
                </div>
            </div>
            </div>
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-1 items-center">{verify-fields-delete}
                </div>
            </div>
            <div class="bg-red-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
            <button type="button" wire:click="proceedDelete()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Delete</button>
            <button type="button" wire:click="cancelDelete()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
            </div>
        </div>
        </div>
    </div>
    </div>
    @endif
</div>