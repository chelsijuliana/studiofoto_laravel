<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rikoza Studio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-ink font-body-md antialiased min-h-screen selection:bg-surface-light selection:text-primary-on">
    
    <x-nav-bar />
    
    <x-hero-stripe>
        <x-slot:title>
            Photography reimagined
        </x-slot>
        <x-slot:subtitle>
            Available today
        </x-slot>
        
        <p>
            Rikoza Studio is a premium photography service that captures your best moments with stunning clarity and composition. Built for those who appreciate the art of visual storytelling.
        </p>
        
        <x-slot:actions>
            <x-button-primary href="{{ route('admin.login') }}">Get started</x-button-primary>
            <x-button-ghost href="#features">Explore features</x-button-ghost>
        </x-slot:actions>
    </x-hero-stripe>

    <!-- Feature Grid Section -->
    <section class="max-w-6xl mx-auto px-8 py-section grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <x-feature-card>
            <h3 class="font-heading-md text-heading-md mb-2">Stunning Quality</h3>
            <p class="text-charcoal font-body-sm text-body-sm">Every shot is meticulously edited to ensure the highest standard of visual quality. We never compromise on the final result.</p>
        </x-feature-card>
        <x-feature-card>
            <h3 class="font-heading-md text-heading-md mb-2">Fast Delivery</h3>
            <p class="text-charcoal font-body-sm text-body-sm">Get your raw files the next day, and fully edited masterpieces within a week. Time is of the essence.</p>
        </x-feature-card>
        <x-feature-card>
            <h3 class="font-heading-md text-heading-md mb-2">Modern Aesthetic</h3>
            <p class="text-charcoal font-body-sm text-body-sm">Our style is heavily inspired by editorial layouts and modern design trends, making your photos pop.</p>
        </x-feature-card>
    </section>

    <!-- Code Section / Story Split -->
    <section class="max-w-6xl mx-auto px-8 py-section grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="font-display-xl text-display-xl tracking-display-xl leading-[1.0] mb-6" style="font-feature-settings: 'ss01', 'ss04', 'ss11';">
                Integrate this weekend.
            </h2>
            <p class="text-body font-body-lg text-body-lg mb-8">
                Booking a session with us is as easy as writing a few lines of code. Our API-first approach ensures you get your digital assets directly to your storage of choice.
            </p>
        </div>
        <div>
            <x-code-window>
                <pre class="whitespace-pre-wrap font-code-md text-code-md text-body"><code class="text-accent-blue">const</code> <code class="text-primary">rikoza</code> <code class="text-charcoal">=</code> <code class="text-accent-blue">new</code> <code class="text-accent-yellow">Rikoza</code><code class="text-charcoal">({</code>
  <code class="text-accent-orange">apiKey</code><code class="text-charcoal">:</code> <code class="text-accent-green">'rz_123456789'</code>
<code class="text-charcoal">});</code>

<code class="text-charcoal">await</code> <code class="text-primary">rikoza.booking</code><code class="text-charcoal">.</code><code class="text-accent-yellow">create</code><code class="text-charcoal">({</code>
  <code class="text-accent-orange">type</code><code class="text-charcoal">:</code> <code class="text-accent-green">'wedding_premium'</code><code class="text-charcoal">,</code>
  <code class="text-accent-orange">date</code><code class="text-charcoal">:</code> <code class="text-accent-green">'2026-10-24'</code><code class="text-charcoal">,</code>
  <code class="text-accent-orange">deliverTo</code><code class="text-charcoal">:</code> <code class="text-accent-green">'s3://my-bucket/wedding'</code>
<code class="text-charcoal">});</code></pre>
            </x-code-window>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-canvas text-charcoal font-body-sm w-full py-[64px] px-[32px] mt-band border-t border-hairline-strong text-center">
        <p>&copy; {{ date('Y') }} Rikoza Studio. All rights reserved.</p>
    </footer>

</body>
</html>
