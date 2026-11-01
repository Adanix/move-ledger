<x-layouts.public title="Select Demo Persona — MoveLedger">

    <div class="py-12 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Title & Notice Header -->
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-orange-600"></span>
                    <span>1-Tap Demo Access &bull; No Passwords Required</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Select a Demonstration Persona
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Choose one of the three core operational roles below to explore MoveLedger with realistic data and role-specific permissions.
                </p>

                @auth
                    <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-center justify-between">
                        <span>Currently active: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role->label() }})</span>
                        <a href="{{ route('dashboard') }}" class="font-bold underline text-blue-700 hover:text-blue-900">
                            Go to Dashboard &rarr;
                        </a>
                    </div>
                @endauth
            </div>

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    <p class="font-bold mb-1">Unable to sign in as persona:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 3 PERSONA CARDS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($personas as $persona)
                    <div class="bg-white rounded-2xl border-2 border-slate-200 hover:border-orange-500 hover:shadow-md transition-all p-6 sm:p-7 flex flex-col justify-between space-y-6">

                        <div class="space-y-4">
                            <!-- Role Badge -->
                            <div class="flex items-center justify-between">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-mono font-semibold
                                    {{ $persona['key'] === 'admin' ? 'bg-rose-100 text-rose-800' : ($persona['key'] === 'coordinator' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $persona['badge'] }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono uppercase">
                                    {{ $persona['key'] }}
                                </span>
                            </div>

                            <!-- Name & Description -->
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                                    {{ $persona['name'] }}
                                </h2>
                                <p class="text-xs text-slate-600 leading-relaxed mt-2">
                                    {{ $persona['description'] }}
                                </p>
                            </div>

                            <!-- Feature Highlights Pill List -->
                            <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono block">Key Capabilities:</span>
                                @foreach($persona['highlights'] as $highlight)
                                    <div class="flex items-center gap-2 text-xs text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{{ $highlight }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 1-Click Submission Form -->
                        <form action="{{ route('demo.quick-login') }}" method="POST" class="pt-2">
                            @csrf
                            <input type="hidden" name="persona" value="{{ $persona['key'] }}">
                            <button type="submit"
                                    class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-orange-600 text-white text-xs sm:text-sm font-bold tracking-tight shadow-xs hover:shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer min-h-12 active:scale-[0.99]">
                                <span>{{ $persona['button_text'] }}</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>

                    </div>
                @endforeach
            </div>

            <!-- Alternative / Traditional Login Hint -->
            <div class="text-center pt-6 space-y-2">
                <p class="text-xs text-slate-500">
                    Are you an existing team member testing credential authentication?
                    <a href="{{ route('login') }}" class="font-semibold text-orange-600 hover:underline">
                        Standard Staff Login &rarr;
                    </a>
                </p>
                <p class="text-[11px] text-slate-400 font-mono">
                    Session IDs are securely regenerated upon persona authentication.
                </p>
            </div>

        </div>
    </div>

</x-layouts.public>
