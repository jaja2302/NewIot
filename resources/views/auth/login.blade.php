<x-layouts.app>
    @section('title', 'Login')

    <div class="login-background"></div>
    <div class="min-h-screen h-full overflow-y-auto flex items-start justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="login-card w-full max-w-md my-8">
            <div>
                <div class="logo-container">
                    <img src="{{ asset('/img/CBIpreview.png') }}" alt="Company Logo" class="w-full h-full object-contain">
                </div>
                <h2 class="mt-8 text-center text-4xl font-bold text-gray-900 dark:text-white">
                    Welcome Back
                </h2>
                <p class="mt-4 text-center text-lg text-gray-600 dark:text-gray-400">
                    Please enter your credentials to access the<br>
                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">Web AWS IoT Portal</span>
                </p>
            </div>

            <form class="mt-12 space-y-6" action="{{ url('/') }}" method="POST" onsubmit="showLoadingScreen()">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="text" autocomplete="email" required
                            class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 
                            dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 
                            dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                            focus:border-indigo-500 focus:z-10 text-lg transition-all"
                            placeholder="Email">
                    </div>
                    <div class="relative">
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-300 
                            dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 
                            dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 
                            focus:border-indigo-500 focus:z-10 text-lg transition-all"
                            placeholder="Password">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent 
                        text-lg font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 
                        transition-colors">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-6 w-6 text-indigo-500 group-hover:text-indigo-400"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>