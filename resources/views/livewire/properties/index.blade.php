<div>
    <button type="button"
        class="whitespace-nowrap rounded-radius bg-primary border border-primary px-4 py-2 text-sm font-medium tracking-wide text-on-primary transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-primary-dark dark:border-primary-dark dark:text-on-primary-dark dark:focus-visible:outline-primary-dark">Primary</button>

    <div class="overflow-hidden w-full overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead
                class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
                <tr>
                    <th scope="col" class="p-4">ID</th>
                    <th scope="col" class="p-4">Name</th>
                    <th scope="col" class="p-4">Real Name</th>
                    <th scope="col" class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse($properties as $property)
                    <tr>
                        <td class="p-4">{{ $property->id }}</td>
                        <td class="p-4">{{ $property->name }}</td>
                        <td class="p-4">{{ $property->real_name }}</td>
                        <td class="p-4">
                            <a href="{{ route('room_rate.index',['id'=>$property->id]) }}" wire:navigate>Rates</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-center" colspan="4">No properties found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $properties->links() }}
    </div>
</div>
