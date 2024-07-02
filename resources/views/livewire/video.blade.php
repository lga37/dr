<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Videos') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="mx-auto max-w-12xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-hidden overflow-x-auto bg-white border-b border-gray-200">
         
                    <x-msg />

                    <div class="flex items-center justify-around mb-3">
                        <div class="flex items-center space-x-4">
                            <x-input-label>Search</x-input-label>
                            <x-text-input wire:model.live.debounce.500ms="search" name="q" /></div>
                        <div>
                            <div class="flex space-x-4 items-center">
                                <x-input-label>Per page</x-input-label>
                                <select wire:model.live="perPage" class="rounded">
                                    <option value="10">10</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                        </div>
                        <div class="">{{ $videos->links() }}</div>
                    </div>



                    <div class="min-w-full align-middle">

                        
                        <table class="min-w-full border divide-y divide-gray-200">
                           

                            <thead>
                                <tr>
                                    <th wire:click="doSort('id')" class="px-2 py-1 text-left bg-gray-50">
                                        <x-datatable-item columnName="id" :sortColumn="$sortColumn" :sortDirection="$sortDirection" /> 
                                    </th>
                                    <th wire:click="doSort('slug')" class="px-2 py-1 text-left bg-gray-50">
                                        <x-datatable-item columnName="slug" :sortColumn="$sortColumn" :sortDirection="$sortDirection" /> 
                                    </th>
                                    <th wire:click="doSort('nome')" class="px-2 py-1 text-left bg-gray-50">
                                        <x-datatable-item columnName="nome" :sortColumn="$sortColumn" :sortDirection="$sortDirection" /> 
                                    </th>
                                   
                                   
                                    
    
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                                @forelse($videos as $video)
                                    <tr class="bg-white" wire:key="video-{{ $video->id }}">
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->id }}
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a href="http://youtube.com{{ $video->slug }}" 
                                                class="underline hover:no-underline text-blue-600 hover:text-blue-900 visited:text-purple-600"
                                                target="_blank">
                                                {{ Str::limit($video->slug, 15) }}
                                                   
                                            </a>
                                            
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->nome }}
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->likes }}
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->comments }}
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->views }}
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->favorites }}
                                        </td>
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->duration }}
                                        </td>
                                        
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->dislikes }}
                                        </td>

                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->comentarios()->count() }}
                                        </td>

                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->busca->slug ?? 'x' }}
                                        </td>

                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a wire:click.prevent="API()" href="#"
                                                class="text-green-700 font-semibold">Api</a>
                                        </td>
                                        
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a wire:click.prevent="Url('{{ $video->slug }}')" href="#"
                                                class="text-green-700 font-semibold">Url</a>
                                        </td>
                                        
                                        <td class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a wire:click.prevent="getComments('{{ $video->slug }}')" href="#"
                                                class="text-blue-700 font-semibold">getComments</a>
                                        </td>

                                        <td class="px-2 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <x-danger-button class="" wire:click="del('{{ $video->id }}')">del</x-danger-button>
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr class="bg-white">
                                        <td colspan="3" class="px-3 py-1 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            No videos found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                  
                    
                 </div>
            </div>
        </div>
    </div>
</div>