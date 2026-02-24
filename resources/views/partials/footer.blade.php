<footer class="bg-gray-900 text-gray-200 mt-10">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- ABOUT --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">Home Pearls</h3>
                <p class="text-sm text-gray-400">
                    We build quality furniture and metal works.
                    From beds to chairs, custom designs available.
                </p>
            </div>

            {{-- LINKS --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="hover:text-yellow-400">Home</a></li>
                    <li><a href="/products" class="hover:text-yellow-400">Products</a></li>
                    <li><a href="/about" class="hover:text-yellow-400">About Us</a></li>
                    <li><a href="/contact" class="hover:text-yellow-400">Contact</a></li>
                </ul>
            </div>

            {{-- CONTACT --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">Contact</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>Email: support@homepearls.com</li>
                    <li>Phone: +256 XXX XXX XXX</li>
                    <li>Location: Kampala, Uganda</li>
                </ul>
            </div>

        </div>

        {{-- BOTTOM --}}
        <div class="mt-10 border-t border-gray-700 pt-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Home Pearls. All rights reserved.
        </div>

    </div>

</footer>