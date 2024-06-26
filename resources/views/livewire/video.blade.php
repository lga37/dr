<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('videos') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="mx-auto max-w-12xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-hidden overflow-x-auto bg-white border-b border-gray-200">
 
                    @if (session('status'))
                    <div class="bg-green-200 p-3 rounded text-green-800" role="alert">
                        <span class="block sm:inline">{{ session('status') }}</span>
                      </div>
                    @endif

                    <div class="min-w-full align-middle">

                        
                        <table class="min-w-full border divide-y divide-gray-200">
                           
                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                                @forelse($videos as $video)
                                    <tr class="bg-white" wire:key="video-{{ $video->id }}">
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a href="http://youtube.com{{ $video->slug }}" 
                                                class="underline hover:no-underline text-blue-600 hover:text-blue-900 visited:text-purple-600"
                                                target="_blank">
                                                {{ Str::limit($video->slug, 15) }}
                                                   
                                            </a>
                                            
                                        </td>
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->nome }}
                                        </td>
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $video->busca->slug ?? 'x' }}
                                        </td>

                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a wire:click.prevent="getInfo('{{ $video->slug }}')" href="#"
                                                class="text-green-700 font-semibold">getInfo</a>
                                        </td>
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            <a wire:click.prevent="getComments('{{ $video->slug }}')" href="#"
                                                class="text-blue-700 font-semibold">getComments</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white">
                                        <td colspan="3" class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            No videos found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $videos->links() }}
                 </div>
            </div>
        </div>
    </div>
</div>