<x-layouts.app>
    @section('title', 'Dashboard')

    <div class="container mx-auto mt-6 px-4">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold mb-4">Welcome to your Dashboard</h1>
            <p class="mb-4">Hello, {{ Auth::user()->name }}!</p>
            <p>This is your dashboard. You can add more content and functionality here.</p>
        </div>
    </div>
</x-layouts.app>