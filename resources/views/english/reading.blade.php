@extends('english.layout')

@section('content')
    <!-- Grid -->
    <div class="grid md:grid-cols-2 gap-3">
        <div>
            <div class="grid space-y-3">
                <dl class="grid sm:flex gap-x-3 text-sm">
                    <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                        Created by:
                    </dt>
                    <dd class="text-gray-800 dark:text-gray-200">
                        <a class="inline-flex items-center gap-x-1.5 text-green-600 decoration-2 hover:underline font-medium"
                           href="mailto:{{ $reading->user->email }}">
                            {{ $reading->user->full_name }}
                        </a>
                    </dd>
                </dl>

                <dl class="grid sm:flex gap-x-3 text-sm">
                    <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                        Topic:
                    </dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">
                        <span
                            class="block font-semibold">{{ $reading->topic ?? __('No topic') }}</span>
                    </dd>
                </dl>
            </div>
        </div>
        <!-- Col -->

        <div>
            <div class="grid space-y-3">
                <dl class="grid sm:flex gap-x-3 text-sm">
                    <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                        Created date:
                    </dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $reading->created_at->format('Y/m/d') }}
                    </dd>
                </dl>

                <dl class="grid sm:flex gap-x-3 text-sm">
                    <dt class="min-w-[150px] max-w-[200px] text-gray-500">
                        Billing method:
                    </dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">
                        User points
                    </dd>
                </dl>
            </div>
        </div>
        <!-- Col -->

        <div
            class="mt-8 border border-gray-200 col-span-full p-4 rounded-lg relative text-justify">
            <span
                class="absolute -top-3 left-4 inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                Paragraph
            </span>
            {{ $reading->response['paragraph'] }}
        </div>

        @foreach($reading->response['form'] as $question)
            <div
                class="mt-4 border border-gray-200 col-span-full p-4 rounded-lg relative">
                <span
                    class="absolute -top-3 left-4 inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                    Question {{ $loop->index + 1 }}
                </span>
                <fieldset>
                    <legend
                        class="text-sm font-semibold leading-6 text-gray-900">{{ $question['question'] }}</legend>
                    <div class="mt-3 space-y-3">
                        @foreach($question['choices'] as $key => $choice)
                            <div class="flex gap-x-3">
                                <input id="push-everything"
                                       name="question-{{ $loop->parent->index + 1 }}" type="radio"
                                       class="h-4 w-4 mt-1 border-gray-300 text-indigo-600 focus:ring-indigo-600 shrink-0 accent-green-600">
                                <label for="push-everything"
                                       class="block text-sm font-medium leading-6 text-gray-900">
                                    {{ $key }}.
                                    {{ $choice }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            </div>
        @endforeach
    </div>
    <!-- End Grid -->
@endsection
