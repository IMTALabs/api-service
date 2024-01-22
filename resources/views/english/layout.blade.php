<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    @vite('resources/css/app.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 10px 0;
            font-weight: bold;
            text-align: left;
        }
        body{
            font-family: 'Montserrat', sans-serif;
        }

        h6 {
            font-size: 1rem;
        }
        h5{
            font-size: 1.125rem;
        }
        h4{
            font-size: 1.25rem;
        }
        h3{
            font-size: 1.375rem;
        }
        h2{
            font-size: 1.5rem;
        }
        h1{
            font-size: 1.625rem;
        }

    </style>
</head>

<body
    class="bg-[url('https://res.cloudinary.com/dyxp9ndma/image/upload/c_scale,w_768/v1705746651/welcome_little_one_fscpaf.png')] min-h-screen">
    <!-- Invoice -->
    <div class="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto my-4 sm:my-10">
        <!-- Grid -->
        <div class="mb-5 pb-5 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-1">
                <img class="w-12 h-12"
                    src="https://3dicons.sgp1.cdn.digitaloceanspaces.com/v1/dynamic/premium/fire-dynamic-premium.png"
                    alt="">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{$title}}</h2>
            </div>
            <!-- Col -->

            <div class="inline-flex gap-x-2">
                <span
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5  h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    Created by IMTA English
                </span>
            </div>
            <!-- Col -->
        </div>
        <!-- End Grid -->

        @yield('content')
    </div>
    <!-- End Invoice -->
</body>

</html>
