<div
    class="max-w-lg p-6"
    x-data="{
        fileName: '',
        uploading: false,
        progress: 0,
    }"
    x-on:livewire-upload-start="uploading = true"
    x-on:livewire-upload-finish="uploading = false"
    x-on:livewire-upload-cancel="uploading = false"
    x-on:livewire-upload-error="uploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    <form
        wire:submit.prevent="submit"
        class="space-y-6"
        enctype="multipart/form-data"
    >
        <!-- File Upload -->
        <div>
            {{-- <label
                for="file"
                class="block text-gray-700"
            >{{ __('Select Image') }}</label> --}}

            <div class="flex items-center my-1">
                <label
                    class="relative overflow-hidden block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg cursor-pointer duration-100 hover:bg-gray-100 focus:outline-none"
                >
                    <span class="flex items-center space-x-2">
                        <!-- SVG Icon for file upload -->
                        <svg
                            viewBox="0 0 24 24"
                            class="w-8 h-8 fill-gray-300"
                        >
                            <path
                                d="M14.2647 15.9377L12.5473 14.2346C11.758 13.4519 11.3633 13.0605 10.9089 12.9137C10.5092 12.7845 10.079 12.7845 9.67922 12.9137C9.22485 13.0605 8.83017 13.4519 8.04082 14.2346L4.04193 18.2622M14.2647 15.9377L14.606 15.5991C15.412 14.7999 15.8149 14.4003 16.2773 14.2545C16.6839 14.1262 17.1208 14.1312 17.5244 14.2688C17.9832 14.4253 18.3769 14.834 19.1642 15.6515L20 16.5001M14.2647 15.9377L18.22 19.9628M18.22 19.9628C17.8703 20 17.4213 20 16.8 20H7.2C6.07989 20 5.51984 20 5.09202 19.782C4.7157 19.5903 4.40973 19.2843 4.21799 18.908C4.12583 18.7271 4.07264 18.5226 4.04193 18.2622M18.22 19.9628C18.5007 19.9329 18.7175 19.8791 18.908 19.782C19.2843 19.5903 19.5903 19.2843 19.782 18.908C20 18.4802 20 17.9201 20 16.8V13M11 4H7.2C6.07989 4 5.51984 4 5.09202 4.21799C4.7157 4.40973 4.40973 4.71569 4.21799 5.09202C4 5.51984 4 6.0799 4 7.2V16.8C4 17.4466 4 17.9066 4.04193 18.2622M18 9V6M18 6V3M18 6H21M18 6H15"
                                class="stroke-gray-500"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        <span
                            class="text-gray-500 truncate"
                            x-text="fileName ? fileName : 'Click to upload a file'"
                        >Click to upload a file</span>
                    </span>


                    <!-- File Input -->
                    <input
                        wire:model="file"
                        id="file"
                        class="hidden"
                        type="file"
                        name="file"
                        required
                        x-on:disabled="uploading"
                        @change="fileName = $event.target.files[0].name"
                    />

                    <!-- Progress Bar -->
                    <div
                        x-show="uploading"
                        class="absolute top-6 h-1 left-14 right-4 w-auto opacity-70"
                    >
                        <progress
                            max="100"
                            x-bind:value="progress"
                            class="w-full h-full"
                        ></progress>
                    </div>

                </label>
            </div>
            <p class="text-sm text-gray-500 flex gap-1">
                Accepted formats:
                <span class="px-1 py-0.5 bg-slate-200 rounded-md text-slate-600 text-xs">.jpeg</span>
                <span class="px-1 py-0.5 bg-slate-200 rounded-md text-slate-600 text-xs">.jpg</span>
                <span class="px-1 py-0.5 bg-slate-200 rounded-md text-slate-600 text-xs">.png</span>
                <span class="px-1 py-0.5 bg-red-200 rounded-md text-red-600 text-xs">.zip</span>
            </p>
            @error('file')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Model Selection (Styled to match file upload) -->
        <div>
            {{-- <label
                for="model"
                class="block text-gray-700"
            >{{ __('Select Model') }}</label> --}}

            <div class="flex items-center my-1 relative">
                <label
                    class="block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg cursor-pointer duration-100 hover:bg-gray-100 focus:outline-none"
                    for="model"
                >
                    <span class=" ">
                        <!-- SVG Icon for model selection -->
                        <svg
                            class="w-8 h-8 fill-gray-500"
                            viewBox="0 0 512 512"
                            xml:space="preserve"
                        >

                            <path
                                d="M498.095,198.122c0-25.763-9.68-50.313-27.257-69.131c-13.197-14.129-29.915-24.039-48.274-28.848   c0.253-2.749,0.397-5.521,0.397-8.313C422.962,41.194,381.768,0,331.133,0C300.099,0,272.631,15.49,256,39.129   C239.369,15.49,211.901,0,180.867,0c-50.635,0-91.828,41.194-91.828,91.828c0,2.794,0.145,5.565,0.397,8.313   c-18.359,4.808-35.075,14.719-48.274,28.848c-17.578,18.819-27.257,43.37-27.257,69.132c0,21.486,6.731,41.427,18.185,57.837   c-11.804,16.895-18.185,37.001-18.185,57.919c0,46.95,32.11,86.538,75.522,97.967c-0.249,2.761-0.39,5.537-0.39,8.327   c0,50.635,41.194,91.828,91.828,91.828c31.035,0,58.503-15.49,75.133-39.129C272.631,496.51,300.099,512,331.133,512   c50.635,0,91.828-41.194,91.828-91.828c0-2.788-0.14-5.565-0.39-8.327c43.412-11.429,75.522-51.016,75.522-97.967   c0-20.919-6.382-41.025-18.185-57.919C491.365,239.549,498.095,219.608,498.095,198.122z M239.304,343.088v77.084   c0,32.221-26.216,58.437-58.437,58.437c-32.223,0-58.437-26.216-58.437-58.437c0-1.771,0.088-3.532,0.245-5.284   c25.807-1.92,50.123-13.714,67.616-33.029l-24.749-22.417c-12.857,14.195-31.206,22.337-50.343,22.337   c-37.442,0-67.902-30.461-67.902-67.901c0-11.892,3.075-23.392,8.834-33.519c16.639,11.985,37.042,19.055,59.068,19.055v-33.391   c-37.442,0-67.902-30.461-67.902-67.901c0-31.399,21.722-58.325,51.228-65.815c10.151,20.567,27.843,37.177,50.093,45.523   l11.728-31.264c-22.678-8.507-37.916-30.504-37.916-54.737c0-32.223,26.216-58.437,58.437-58.437s58.437,26.216,58.437,58.437   v77.084v23.211h-13.79v-23.211h-33.391v23.211h-23.211v33.391h23.211v13.79h-23.211v33.391h23.211v13.789h-23.211v33.391h23.211   v23.211h33.391v-23.211h13.79V343.088z M225.516,286.485v-60.969h60.969v60.969H225.516z M455.869,280.359   c5.759,10.128,8.834,21.628,8.834,33.519c0,37.442-30.461,67.901-67.902,67.901c-19.137,0-37.486-8.141-50.343-22.337   L321.71,381.86c17.494,19.314,41.809,31.107,67.616,33.029c0.157,1.751,0.245,3.513,0.245,5.284   c0,32.223-26.216,58.437-58.437,58.437s-58.438-26.214-58.438-58.437v-77.084v-23.211h13.789v23.211h33.391v-23.211h23.211   v-33.391h-23.211v-13.789h23.211v-33.391h-23.211v-13.79h23.211v-33.391h-23.211v-23.211h-33.391v23.211h-13.789v-23.211V91.828   c0-32.223,26.216-58.437,58.437-58.437s58.437,26.214,58.437,58.437c0,24.232-15.238,46.229-37.916,54.737l11.728,31.264   c22.251-8.347,39.942-24.957,50.094-45.523c29.506,7.491,51.228,34.416,51.228,65.815c0,37.441-30.461,67.901-67.902,67.901   v33.391C418.827,299.414,439.23,292.343,455.869,280.359z"
                            />
                        </svg>
                        <select
                            wire:model="model"
                            id="model"
                            class="left-0 right-0 top-0 bottom-0 border-none !shadow-transparent outline-none bg-transparent appearance-none absolute pl-14 w-full h-full z-20 cursor-pointer text-gray-500 truncate capitalize"
                            style="background-image: none;"
                            name="model"
                            required
                        >
                            <option value="">{{ __('Choose a model') }}</option>
                            @foreach ($models as $modelName)
                                <option value="{{ $modelName }}">{{ $modelName }}</option>
                            @endforeach
                        </select>
                    </span>
                </label>
            </div>
            <p class="text-sm text-gray-500">The selected model will handle image cleaning</p>
            @error('model')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button with Loading Indicator -->
        <div class="flex items-center justify-end mt-6">
            <button
                type="submit"
                class="rounded-md bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none relative"
                x-bind:disabled="!fileName || uploading"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                <span
                    wire:target="submit"
                    wire:loading.class="opacity-20"
                >
                    {{ __('Submit') }}
                </span>
                <span
                    wire:loading
                    wire:target="submit"
                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center"
                >
                    <svg
                        class="animate-spin h-5 w-5 text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="2"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        ></path>
                    </svg>
                </span>
            </button>
        </div>
    </form>
</div>
