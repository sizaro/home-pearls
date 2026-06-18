<footer class="bg-[#5C3A21] text-[#F5F1ED] mt-0.5 w-screen">

    <div class="max-w-7xl mx-auto px-6 py-12">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            {{-- ABOUT --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">
                    Home Pearls
                </h3>

                <p class="text-sm text-[#EDE6DF] leading-relaxed">
                    We design and build quality furniture and metal works.
                    From beds, chairs, and custom pieces — crafted with care for your home.
                </p>
            </div>

            {{-- QUICK LINKS --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">
                    Quick Links
                </h3>

                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="hover:text-yellow-300 transition">Home</a></li>
                    <li><a href="/products" class="hover:text-yellow-300 transition">Products</a></li>
                    <li><a href="/about" class="hover:text-yellow-300 transition">About Us</a></li>
                    <li><a href="/contact" class="hover:text-yellow-300 transition">Contact</a></li>
                </ul>
            </div>

            {{-- CONTACT + SOCIAL --}}
            <div>
                <h3 class="text-lg font-semibold text-white mb-3">
                    Contact
                </h3>

                <ul class="space-y-2 text-sm text-[#EDE6DF] mb-4">
                    <li>Email: support@homepearls.com</li>
                    <li>Phone: +256 783 881 736</li>
                    <li>Location: Jinja, Uganda</li>
                </ul>

                {{-- SOCIAL --}}


 {{-- SOCIAL --}}
<div class="flex gap-5 mt-4 items-center">

    {{-- FACEBOOK --}}
    <a href="https://facebook.com"
       target="_blank"
       class="hover:-translate-y-1 transition duration-200">

        <img src="{{ asset('images/facebook_icon.png') }}"
             alt="Facebook"
             class="w-6 h-6 rounded-full">
    </a>

    {{-- INSTAGRAM --}}
    <a href="https://instagram.com"
       target="_blank"
       class="hover:-translate-y-1 transition duration-200">

        <img src="{{ asset('images/instagram_icon.png') }}"
             alt="Instagram"
             class="w-6 h-6 rounded-full">
    </a>

    {{-- TWITTER --}}
    <a href="https://twitter.com"
       target="_blank"
       class="hover:-translate-y-1 transition duration-200">

        <img src="{{ asset('images/twitter_icon.png') }}"
             alt="Twitter"
             class="w-6 h-6 rounded-full">
    </a>

    {{-- WHATSAPP --}}
    <a href="https://wa.me/256701234567"
       target="_blank"
       class="hover:-translate-y-1 transition duration-200">

        <img src="{{ asset('images/whatsapp_icon.png') }}"
             alt="WhatsApp"
             class="w-6 h-6 rounded-full">
    </a>

</div>

            </div>

        </div>

        {{-- BOTTOM --}}
        <div class="mt-10 border-t border-[#EDE6DF]/20 pt-5 text-center text-sm text-[#EDE6DF]">
            &copy; {{ date('Y') }} Home Pearls. All rights reserved.
        </div>

    </div>

</footer>