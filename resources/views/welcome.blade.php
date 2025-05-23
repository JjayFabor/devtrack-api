<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to DevTrack API</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Optional: Add a subtle background pattern or gradient */
        body {
            background-color: #f7fafc; /* Lighter gray */
            /* Or a subtle gradient:
            background-image: linear-gradient(to top right, #accbee, #e7f0fd);
            */
        }
        .hero-section {
            min-height: 80vh; /* Make hero take more space */
        }
    </style>
</head>
<body class="antialiased text-gray-800">
    <div class="container mx-auto px-4">

        <!-- Navigation (Optional) -->
        <nav class="py-6 flex justify-between items-center">
            <div class="text-2xl font-bold text-indigo-600">DevTrack API</div>
            <div>
                <a href="/docs" class="text-indigo-600 hover:text-indigo-800 font-medium">API Docs</a>
                {{-- Add other links like Login/Register if applicable --}}
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="hero-section flex flex-col justify-center items-center text-center py-16 md:py-24">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-gray-900 mb-6 leading-tight">
                    Welcome to <span class="text-indigo-600">DevTrack API</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-xl mx-auto">
                    Seamlessly integrate and manage your development projects. Explore our powerful and easy-to-use API.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="/docs"
                       class="bg-indigo-600 text-white font-semibold px-8 py-3 rounded-lg shadow-lg hover:bg-indigo-700 transition duration-300 ease-in-out transform hover:-translate-y-1 text-lg">
                        Explore API Docs
                    </a>
                    <a href="#features" {{-- Link to a features section if you add one --}}
                       class="bg-gray-200 text-gray-700 font-semibold px-8 py-3 rounded-lg shadow-md hover:bg-gray-300 transition duration-300 ease-in-out text-lg">
                        Learn More
                    </a>
                </div>
            </div>
        </header>

        <!-- Features Section (Example - Add your actual features) -->
        <section id="features" class="py-16 md:py-24 bg-white rounded-lg shadow-xl my-12">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Why Choose DevTrack API?</h2>
                <p class="text-gray-600 mb-12 max-w-2xl mx-auto">
                    Our API is designed for developers, by developers, focusing on simplicity, power, and reliability.
                </p>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="p-6 bg-indigo-50 rounded-lg">
                        <div class="text-indigo-500 mb-4">
                            {{-- Replace with an actual SVG icon or FontAwesome --}}
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Fast & Reliable</h3>
                        <p class="text-gray-600">Optimized for speed and uptime, ensuring your applications run smoothly.</p>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-lg">
                        <div class="text-indigo-500 mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Easy Integration</h3>
                        <p class="text-gray-600">Clear documentation.</p>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-lg">
                        <div class="text-indigo-500 mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Secure</h3>
                        <p class="text-gray-600">Built with security best practices to protect your data.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12 bg-yellow-50 border-t border-b border-yellow-200 my-12">
            <div class="container mx-auto px-6 text-center">
                <div class="flex justify-center items-center mb-4">
                    <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h2 class="text-2xl font-semibold text-yellow-700">Project Under Development</h2>
                </div>
                <p class="text-yellow-600 max-w-2xl mx-auto">
                    Please note: DevTrack API is currently an ongoing project. Some features might be incomplete or subject to change. We appreciate your understanding.
                    To add feedback, please <a href="https://github.com/JjayFabor/devtrack-api/issues/new" class="text-yellow-700 hover:text-yellow-800 underline font-semibold" target="_blank" rel="noopener noreferrer">create an issue on GitHub</a>.
                </p>
            </div>
        </section>

        <footer class="text-center py-10 mt-12 border-t border-gray-200">
            <p class="text-gray-600">&copy; {{ date('Y') }} DevTrack API. All rights reserved.</p>
            <p class="text-sm text-gray-500 mt-2">
                <a href="/privacy" class="hover:text-indigo-600">Privacy Policy</a> |
                <a href="/terms" class="hover:text-indigo-600">Terms of Service</a>
            </p>
        </footer>

    </div>
</body>
</html>
