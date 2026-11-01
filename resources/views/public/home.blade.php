<x-layouts.public title="MoveLedger — Mobile-First Relocation Inventory Platform">

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-28">
            <div class="max-w-3xl space-y-6">

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-50 border border-orange-200 text-orange-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-orange-600"></span>
                    <span>Field Operations v1.0 &bull; Single-Tenant Relocation Platform</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.1]">
                    Mobile-First Relocation Inventory &amp; Cryptographic Manifests
                </h1>

                <!-- Subheading -->
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl">
                    Replace illegible carbon-copy clipboards with 1-tap touch surveys, automated cube &amp; weight calculations, mandatory pre-existing damage photos, and tamper-evident SHA-256 digital Bills of Lading.
                </p>

                <!-- CTA Row -->
                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a href="{{ route('demo.login') }}"
                       class="px-6 py-3.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm sm:text-base shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 min-h-12">
                        <span>Explore Live Demo (1-Click Login)</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>

                    <a href="{{ route('public.docs') }}"
                       class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-semibold text-sm sm:text-base transition-colors flex items-center justify-center min-h-12">
                        <span>Read Feature Guide</span>
                    </a>
                </div>

                <!-- Demo Notice Pill -->
                <div class="pt-2 text-xs text-slate-500 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Instant access without passwords &bull; Preloaded with realistic relocation jobs</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE CAPABILITIES GRID -->
    <section class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600 font-mono">In-Scope Modules</span>
                <h2 class="text-2xl sm:text-4xl font-bold tracking-tight text-slate-900">
                    Engineered for Rigorous Field Operations
                </h2>
                <p class="text-sm text-slate-600">
                    Built specifically for moving crews, dispatchers, and shippers who demand accuracy and liability protection.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Feature 1: 1-Tap Room Inventory -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">1-Tap Standard Catalog Quick-Add</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Surveyors select from 45+ pre-benchmarked standard household goods across 8 room categories. Unit cubic feet, estimated weight, and box conversion factors are applied instantly.
                    </p>
                </div>

                <!-- Feature 2: Damage Documentation & Photos -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Condition Tracking &amp; Photo Capture</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pre-existing wear (scratched, chipped, gouged, torn) strictly enforces descriptive notes and up to 5 on-site camera photos per item before items can be loaded into the truck.
                    </p>
                </div>

                <!-- Feature 3: Cryptographic SHA-256 Signatures -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">SHA-256 Manifest Lock &amp; Signatures</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        HTML5 canvas captures touch signatures. Upon submission, a canonical snapshot of all items is hashed (SHA-256) and cryptographically locked against post-sign alterations.
                    </p>
                </div>

                <!-- Feature 4: Real-Time Destination Delivery Check-Off -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Destination Delivery Check-Off</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        On-device delivery verification. Tagged items are checked off individually or by room as Delivered, Damaged in Transit, or Missing, with mandatory transit exception notes.
                    </p>
                </div>

                <!-- Feature 5: PIN-Guarded Customer Portal -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Customer Share Portal &amp; PIN Gate</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Customers view itemized manifests, photos, and sign off via secure 64-character encrypted tokens. Optional 4-6 digit numeric PIN gates prevent unauthorized viewing.
                    </p>
                </div>

                <!-- Feature 6: Role-Based Field Governance -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Multi-Role Governance</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Granular permissions for Administrators, Dispatch Coordinators, and Field Surveyors. Signed manifests can only be unlocked for revisions by authorized supervisory personnel.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- END-TO-END WORKFLOW SECTION -->
    <section class="py-16 sm:py-24 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="max-w-2xl space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600 font-mono">Process Flow</span>
                <h2 class="text-2xl sm:text-4xl font-bold tracking-tight text-slate-900">
                    Relocation Lifecycle from Dispatch to Final Delivery
                </h2>
                <p class="text-sm text-slate-600">
                    How MoveLedger coordinates personnel and enforces accountability at each step of the relocation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Step 1 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50 space-y-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-600 text-white font-mono font-bold text-sm flex items-center justify-center">
                        01
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Dispatch &amp; Room Seeding</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Coordinator creates customer move file and selects property type (Studio, 1BR, 2BR, 3BR House, Office). Standard room templates and sequential tag series are initialized automatically.
                    </p>
                    <span class="inline-block text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                        Role: Move Coordinator
                    </span>
                </div>

                <!-- Step 2 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50 space-y-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-600 text-white font-mono font-bold text-sm flex items-center justify-center">
                        02
                    </div>
                    <h3 class="text-base font-bold text-slate-900">On-Site Survey &amp; Condition</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Surveyor taps catalog items into active room tabs. Quantities are adjusted with steppers. Pre-existing scratches or tears require descriptive notes and photos before origin signing.
                    </p>
                    <span class="inline-block text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-green-100 text-green-800">
                        Role: Field Surveyor
                    </span>
                </div>

                <!-- Step 3 -->
                <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50 space-y-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-600 text-white font-mono font-bold text-sm flex items-center justify-center">
                        03
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Manifest Lock &amp; Delivery</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Customer reviews manifest on tablet or via share link, draws signature, and locks the inventory for transit. At destination, crew checks off items and captures final delivery sign-off.
                    </p>
                    <span class="inline-block text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-purple-100 text-purple-800">
                        Role: Surveyor &amp; Customer
                    </span>
                </div>

            </div>
        </div>
    </section>

    <!-- DEMO PERSONAS INTRO SECTION -->
    <section class="py-16 sm:py-24 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="max-w-2xl space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-400 font-mono">Live Demo Access</span>
                <h2 class="text-2xl sm:text-4xl font-bold tracking-tight text-white">
                    Experience MoveLedger Through 3 Real Personas
                </h2>
                <p class="text-sm text-slate-300">
                    No passwords or registration required. Choose any operational role to test the actual application features and authorization rules.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Persona 1 -->
                <div class="bg-slate-800 border border-slate-700 p-6 rounded-2xl space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Administrator
                        </span>
                        <h3 class="text-lg font-bold text-white">Executive &amp; Ops Controller</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Full administrative governance. Manage staff accounts, inspect global activity metrics, and authorize manifest revision unlocks.
                        </p>
                    </div>
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="admin">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-colors cursor-pointer">
                            Continue as Administrator &rarr;
                        </button>
                    </form>
                </div>

                <!-- Persona 2 -->
                <div class="bg-slate-800 border border-slate-700 p-6 rounded-2xl space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                            Move Coordinator
                        </span>
                        <h3 class="text-lg font-bold text-white">Relocation Dispatcher</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Job dispatch &amp; client management. Create move jobs, configure property templates, and generate PIN-protected customer share links.
                        </p>
                    </div>
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="coordinator">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-colors cursor-pointer">
                            Continue as Move Coordinator &rarr;
                        </button>
                    </form>
                </div>

                <!-- Persona 3 -->
                <div class="bg-slate-800 border border-slate-700 p-6 rounded-2xl space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Field Surveyor
                        </span>
                        <h3 class="text-lg font-bold text-white">Mobile Field Crew</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            On-site mobile survey workspace. 1-tap item quick adds, condition photo uploads, touch Bill of Lading sign-off, and delivery check-off.
                        </p>
                    </div>
                    <form action="{{ route('demo.quick-login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona" value="surveyor">
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-colors cursor-pointer">
                            Continue as Field Surveyor &rarr;
                        </button>
                    </form>
                </div>

            </div>

            <!-- Demo Banner -->
            <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-slate-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                    <span>Demo mode is active on this deployment. All personas reset to test databases safely.</span>
                </div>
                <a href="{{ route('demo.login') }}" class="text-orange-400 hover:text-orange-300 font-semibold underline whitespace-nowrap">
                    View Persona Selection Screen &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- CUSTOMIZATION & CONTACT BANNER -->
    <section class="py-16 bg-orange-50 border-t border-orange-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                Need Something More Tailored for Your Moving Enterprise?
            </h2>
            <p class="text-xs sm:text-sm text-slate-700 max-w-2xl mx-auto leading-relaxed">
                This demo showcases the current application features and workflows. The web application can be further developed, extended, and customized to fit your business processes, custom rate cards, ERP integrations, and specific field requirements.
            </p>
            <div class="pt-3">
                <a href="mailto:nixresponding@gmail.com"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Contact: nixresponding@gmail.com</span>
                </a>
            </div>
        </div>
    </section>

</x-layouts.public>
