<head>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>

    <div class="flex justify-center items-center min-h-screen w-full">

        <form action="{{ route('store-events') }}" method="POST" role="form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <legend class="text-2xl text-center my-6">
                Create Event
            </legend>




            <label class="block text-sm my-4 w-96">
                <span class="text-gray-700 dark:text-gray-400">Title</span>
                <!-- focus-within sets the color for the icon when input is focused -->
                <div class="rounded text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        class="rounded-lg block w-full mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="Title" name="title" value="">
                </div>
            </label>



            <label class="block text-sm my-4">
                <span class="text-gray-700 dark:text-gray-400">Description</span>
                <!-- focus-within sets the color for the icon when input is focused -->
                <div class="rounded text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        class="rounded-lg block w-full mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="Description" name="description" value="">
                </div>
            </label>



            <label class="block text-sm my-4">
                <span class="text-gray-700 dark:text-gray-400">Start Date</span>
                <!-- focus-within sets the color for the icon when input is focused -->
                <div class="rounded text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input type="datetime-local"
                        class="rounded-lg block w-full mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="Start Date" name="start_date" value="">
                </div>
            </label>


            <label class="block text-sm my-4">
                <span class="text-gray-700 dark:text-gray-400">End Date</span>
                <!-- focus-within sets the color for the icon when input is focused -->
                <div class="rounded text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input type="datetime-local"
                        class="rounded-lg block w-full mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="End Date" name="end_date" value="">
                </div>
            </label>

            <div class="w-full flex justify-center mt-6">
                <button
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                    type="submit">
                    Submit
                </button>
            </div>




        </form>
    </div>
</body>
