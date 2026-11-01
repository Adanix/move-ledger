<x-layouts.guest title="Staff Sign In">
    <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                Staff Email Address
            </label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                       placeholder="admin@moveledger.test"
                       class="w-full px-3.5 py-3 rounded-lg border border-slate-300 text-base focus:ring-2 focus:ring-blue-600 focus:border-blue-600 shadow-2xs">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                Password
            </label>
            <div class="mt-1">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                       class="w-full px-3.5 py-3 rounded-lg border border-slate-300 text-base focus:ring-2 focus:ring-blue-600 focus:border-blue-600 shadow-2xs">
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span class="text-xs text-slate-600 font-medium select-none">Remember this device</span>
            </label>
        </div>

        <div>
            <button type="submit"
                    class="w-full min-h-12 flex items-center justify-center py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md active:scale-[0.99] transition-all">
                Sign In to MoveLedger
            </button>
        </div>

        @if(config('app.demo_mode', true))
        <div class="pt-2">
            <a href="{{ route('demo.login') }}"
               class="w-full min-h-12 flex items-center justify-center py-3 px-4 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm shadow-xs transition-colors">
                <span>Go to 1-Click Demo Personas &rarr;</span>
            </a>
        </div>
        @endif

        <div class="pt-4 border-t border-slate-100 text-center">
            <a href="{{ route('public.home') }}" class="text-xs text-slate-500 hover:text-slate-800">
                &larr; Back to Public Overview
            </a>
        </div>
    </form>
</x-layouts.guest>
