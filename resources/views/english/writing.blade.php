@extends('english.layout')

@section('content')
    <!-- Grid -->
    <div class="flex flex-col gap-3 flex-1">
        <div class="flex">
            <div class="w-1/2">
                <div class="grid space-y-3">
                    <dl class="grid sm:flex gap-x-3 text-sm">
                        <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                            {{ __('Created by') }}:
                        </dt>
                        <dd class="text-gray-800 dark:text-gray-200">
                            <a class="inline-flex items-center gap-x-1.5 text-green-600 decoration-2 hover:underline font-medium"
                               href="mailto:{{ $reading->email }}">
                                {{ $reading->full_name }}
                            </a>
                        </dd>
                    </dl>

                    <dl class="grid sm:flex gap-x-3 text-sm">
                        <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                            {{ __('Topic') }}:
                        </dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">
                            <span
                                class="block font-semibold">{{ $reading->topic ?? __('No topic') }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
            <!-- Col -->

            <div class="w-1/2">
                <div class="grid space-y-3">
                    <dl class="grid sm:flex gap-x-3 text-sm">
                        <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                            {{ __('Created date') }}:
                        </dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">
                            {{ \Carbon\Carbon::parse($reading->created_at)->format('Y/m/d') }}
                        </dd>
                    </dl>

                    <dl class="grid sm:flex gap-x-3 text-sm">
                        <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                            {{ __('Billing method') }}:
                        </dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">
                            {{ __('User points') }}
                        </dd>
                    </dl>
                </div>
            </div>
            <!-- Col -->
        </div>

        <div
            class="mt-8 border border-gray-200 col-span-full p-4 rounded-lg relative text-justify">
            <span
                class="absolute -top-3 left-4 inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                {{ __('Prompt') }}
            </span>
            {!! $reading->response !!}
        </div>
        <div
            class="mt-8 border border-gray-200 col-span-full p-4 rounded-lg relative text-justify flex-1">
            <span
                class="absolute -top-3 left-4 inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                {{ __('Your paragraph') }}
            </span>
        </div>
    </div>
    <!-- End Grid -->
@endsection
