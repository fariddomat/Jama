@props([
    'columns' => [],
    'data' => [],
    'routePrefix' => '',
    'parentId' => null,
    'show' => false,
    'edit' => false,
    'delete' => false,
    'restore' => false,
])

<div class="overflow-hidden bg-white shadow-md rounded-lg">
    <table class="w-full border-collapse">
        <!-- Table Head -->
        <thead class="bg-blue-800 text-white">
            <tr>
                <th class="px-4 py-2 border border-gray-300 text-left md:hidden">Details</th>
                @foreach ($columns as $col)
                    <th class="px-4 py-2 border border-gray-300 text-left hidden md:table-cell">
                        {{ ucfirst(str_replace('_', ' ', $col)) }}
                    </th>
                @endforeach
                @if ($show || $edit || $delete || $restore || $slot->isNotEmpty())
                    <th class="px-4 py-2 border border-gray-300 text-center hidden md:table-cell">Action</th>
                @endif
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-gray-300 bg-gray-50">
            @forelse ($data as $row)
                <tr class="hover:bg-gray-100 transition" x-data="{ expanded: false, confirmStatusChange: false, newStatusId: {{ $row->status_id ?? 'null' }} }">
                    <!-- Mobile Expand Button -->
                    <td class="px-4 py-2 border border-gray-300 md:hidden" @click="expanded = !expanded">
                        <div class="flex justify-between items-center cursor-pointer">
                            <span>{{ $row->{$columns[0]} ?? '—' }}</span>
                            <button class="text-blue-500" type="button">
                                <i class="fas fa-chevron-down" x-show="!expanded"></i>
                                <i class="fas fa-chevron-up" x-show="expanded"></i>
                            </button>
                        </div>
                        <div class="flex-row p-4 bg-gray-100 border border-gray-300 mt-3" x-show="expanded" x-cloak>
                            @foreach ($columns as $col)
                                <div class="flex justify-between py-1">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $col)) }}:</strong>
                                    <span>
                                        @if (($col === 'img' || $col === 'image') && $row->$col)
                                            <img src="{{ Storage::url($row->$col) }}" alt="Image" class="w-16 h-16 rounded">
                                        @elseif ($col === 'file' && $row->$col)
                                            <a href="{{ Storage::url($row->$col) }}" target="_blank">View PDF</a>
                                        @elseif (Str::endsWith($col, '_id'))
                                            @php
                                                $relation = Str::before($col, '_id');
                                                $relationName = $col === 'customer_id' ? 'customer' : ($col === 'merchant_id' ? 'merchant' : ($col === 'delivery_agent_id' ? 'deliveryAgent' : ($col === 'order_id' ? 'order' : $relation)));
                                            @endphp
                                            {{ $row->$relationName ? $row->$relationName->name : '—' }}
                                        @elseif ($col === 'customer_name' && $row instanceof \App\Models\Order)
                                            {{ $row->customer ? $row->customer->name : '—' }}
                                        @elseif ($col === 'status' && $row instanceof \App\Models\Order)
                                            <select wire:model.live="data.{{ $loop->index }}.status_id" @change="confirmStatusChange = true; newStatusId = $event.target.value" class="border-gray-300 rounded-md shadow-sm">
                                                <option value="">Select Status</option>
                                                @foreach (\App\Models\Status::whereNull('deleted_at')->pluck('name', 'id') as $id => $name)
                                                    <option value="{{ $id }}" {{ $row->status_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            <div x-show="confirmStatusChange" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                                <div class="bg-white p-6 rounded-lg shadow-lg">
                                                    <p class="mb-4">Confirm status change?</p>
                                                    <div class="flex space-x-4">
                                                        <button @click="confirmStatusChange = false; $wire.updateOrderStatus({{ $row->id }}, newStatusId)" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Confirm</button>
                                                        <button @click="confirmStatusChange = false; $wire.set('data.{{ $loop->index }}.status_id', {{ $row->status_id ?? 'null' }})" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{ $row->$col ?? '—' }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach

                            <!-- Actions in Mobile View -->
                            @if ($show || $edit || $delete || $restore || $slot->isNotEmpty())
                                <div class="mt-2 flex space-x-4">
                                    @if ($show)
                                        <a href="{{ route($routePrefix . '.show', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                            class="text-blue-500 hover:text-blue-700" wire:navigate>
                                            <i class="fas fa-eye"></i> Show
                                        </a>
                                    @endif
                                    @if ($edit && !$row->trashed())
                                        <a href="{{ route($routePrefix . '.edit', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                            class="text-yellow-500 hover:text-yellow-700" wire:navigate>
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if ($delete && !$row->trashed())
                                        <form
                                            action="{{ route($routePrefix . '.destroy', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                            method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                    @if ($restore && $row->trashed())
                                        <form
                                            action="{{ route($routePrefix . '.restore', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                            method="POST" onsubmit="return confirm('Are you sure you want to restore?');">
                                            @csrf
                                            <button type="submit" class="text-green-500 hover:text-green-700">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </form>
                                    @endif
                                    {{ $slot }}
                                </div>
                            @endif
                        </div>
                    </td>

                    <!-- Normal Columns (Hidden on Mobile) -->
                    @foreach ($columns as $col)
                        <td
                            class="px-4 py-2 border border-gray-300 hidden md:table-cell {{ $row->trashed() ? 'text-gray-400 italic' : '' }}">
                            @if (($col === 'img' || $col === 'image') && $row->$col)
                                <img src="{{ Storage::url($row->$col) }}" alt="Image" class="w-16 h-16 rounded">
                            @elseif ($col === 'file' && $row->$col)
                                <a href="{{ Storage::url($row->$col) }}" target="_blank">View PDF</a>
                            @elseif (Str::endsWith($col, '_id'))
                                @php
                                    $relation = Str::before($col, '_id');
                                    $relationName = $col === 'customer_id' ? 'customer' : ($col === 'merchant_id' ? 'merchant' : ($col === 'delivery_agent_id' ? 'deliveryAgent' : ($col === 'order_id' ? 'order' : $relation)));
                                @endphp
                                {{ $row->$relationName ? $row->$relationName->name : '—' }}
                            @elseif ($col === 'customer_name' && $row instanceof \App\Models\Order)
                                {{ $row->customer ? $row->customer->name : '—' }}
                            @elseif ($col === 'status' && $row instanceof \App\Models\Order)
                                <select wire:model.live="data.{{ $loop->index }}.status_id" @change="confirmStatusChange = true; newStatusId = $event.target.value" class="border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select Status</option>
                                    @foreach (\App\Models\Status::whereNull('deleted_at')->pluck('name', 'id') as $id => $name)
                                        <option value="{{ $id }}" {{ $row->status_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="confirmStatusChange" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white p-6 rounded-lg shadow-lg">
                                        <p class="mb-4">Confirm status change?</p>
                                        <div class="flex space-x-4">
                                            <button @click="confirmStatusChange = false; $wire.updateOrderStatus({{ $row->id }}, newStatusId)" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Confirm</button>
                                            <button @click="confirmStatusChange = false; $wire.set('data.{{ $loop->index }}.status_id', {{ $row->status_id ?? 'null' }})" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{ $row->$col ?? '—' }}
                            @endif
                        </td>
                    @endforeach

                    <!-- Actions (Hidden on Mobile) -->
                    @if ($show || $edit || $delete || $restore || $slot->isNotEmpty())
                        <td class="px-4 py-2 border border-gray-300 text-center hidden md:table-cell">
                            <div class="flex justify-center space-x-1">
                                @if ($show)
                                    <a href="{{ route($routePrefix . '.show', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                        class="text-blue-500 hover:text-blue-700" wire:navigate>
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                                @if ($edit && !$row->trashed())
                                    <a href="{{ route($routePrefix . '.edit', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700" wire:navigate>
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if ($delete && !$row->trashed())
                                    <form
                                        action="{{ route($routePrefix . '.destroy', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                        method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                @if ($restore && $row->trashed())
                                    <form
                                        action="{{ route($routePrefix . '.restore', $parentId ? [$parentId, $row->id] : $row->id) }}"
                                        method="POST" onsubmit="return confirm('Are you sure you want to restore?');">
                                        @csrf
                                        <button type="submit" class="text-green-500 hover:text-green-700">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                                {{ $slot }}
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + ($show || $edit || $delete || $restore || $slot->isNotEmpty() ? 1 : 0) }}"
                        class="px-4 py-2 text-center text-gray-500">
                        No records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
