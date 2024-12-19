@vite('resources/css/app.css')

<div>
    <div
        x-data="{ tab: 'result' }"
        class="flex border-2 border-black dark:border-white mb-4 select-none"
    >
        <div class="w-4/6 border-r-2 border-black dark:border-white relative cursor-none">
            <img
                x-show="tab === 'original'"
                src="{{ $image->getUrl() }}"
                alt="{{ $image->name }}"
                class="w-full"
            >

            <img
                x-show="tab === 'mask'"
                src="{{ $mask->getUrl() }}?u={{ md5($mask->updated_at) }}"
                alt="{{ $mask->name }}"
                class="w-full"
            >

            <img
                x-show="tab === 'result'"
                src="{{ $result->getUrl() }}?u={{ md5($result->updated_at) }}"
                alt="{{ $result->name }}"
                class="w-full"
            >
        </div>

        <div class="w-2/6 p-2">
            <div class="sticky top-[4.5rem] p-4 bg-slate-100 dark:bg-slate-800 rounded-md">
                <x-filament::tabs>
                    <x-filament::tabs.item
                        @click="tab = 'original'"
                        :alpine-active="'tab === \'original\''"
                    >
                        Original
                    </x-filament::tabs.item>

                    <x-filament::tabs.item
                        @click="tab = 'mask'"
                        :alpine-active="'tab === \'mask\''"
                    >
                        Mask
                    </x-filament::tabs.item>
                    <x-filament::tabs.item
                        @click="tab = 'result'"
                        :alpine-active="'tab === \'result\''"
                    >
                        Result
                    </x-filament::tabs.item>
                </x-filament::tabs>
                <div class="font-semibold mt-2">
                    # {{ $image->name }}
                </div>
            </div>
        </div>
    </div>
</div>
