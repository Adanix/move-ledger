<x-layouts.public title="MoveLedger Feature Documentation &amp; User Guide">

    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
            <div class="max-w-3xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-orange-800 text-xs font-semibold">
                    <span>MoveLedger Operational Reference</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Product Feature Guide &amp; Operational Manual
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Comprehensive overview of all implemented workflows, role responsibilities, cryptographic Bill of Lading standards, and field surveying procedures.
                </p>
                <div class="pt-2">
                    <a href="{{ route('demo.login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs shadow-xs transition-colors">
                        <span>Launch Demo with 1-Click Login</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN DOCS CONTAINER (Sidebar TOC + Content) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="flex flex-col lg:flex-row gap-10 items-start">

            <!-- STICKY TABLE OF CONTENTS -->
            <aside class="w-full lg:w-64 lg:sticky lg:top-28 shrink-0 bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 font-mono block">On This Page</span>
                <nav class="space-y-1.5 text-xs font-medium">
                    <a href="#overview" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">1. Platform Overview</a>
                    <a href="#roles-permissions" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">2. User Roles &amp; Permissions</a>
                    <a href="#move-lifecycle" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">3. Relocation Lifecycle States</a>
                    <a href="#survey-workspace" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">4. On-Site Survey Workspace</a>
                    <a href="#condition-photos" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">5. Pre-Existing Damage &amp; Photos</a>
                    <a href="#manifest-signing" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">6. Cryptographic Manifest Sign-Off</a>
                    <a href="#customer-portal" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">7. Customer Portal &amp; PIN Gate</a>
                    <a href="#delivery-checkoff" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">8. Destination Delivery Check-Off</a>
                    <a href="#customization" class="block text-slate-700 hover:text-orange-600 py-1 transition-colors">9. Customization &amp; Inquiries</a>
                </nav>
            </aside>

            <!-- DOCUMENTATION ARTICLES -->
            <div class="flex-1 space-y-12 max-w-4xl text-slate-800 leading-relaxed text-sm">

                <!-- SECTION 1: PLATFORM OVERVIEW -->
                <section id="overview" class="space-y-4 pt-2">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 01</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Platform Overview &amp; Purpose</h2>
                    </div>
                    <p>
                        MoveLedger is a mobile-first digital Bill of Lading (BOL) and relocation inventory field management system. In traditional household moving operations, crews rely on multi-page carbon copy paper sheets to record furniture items, tag numbers, and pre-existing scratches. This results in illegible handwriting, transcription disputes, misplaced pages, and contested insurance claims.
                    </p>
                    <p>
                        MoveLedger resolves these liabilities by digitizing the entire relocation workflow into touch-optimized mobile screens with automatic cubic foot and weight benchmarks, mandatory damage notes, on-site photographic evidence, and cryptographic SHA-256 digital signature manifest locking.
                    </p>
                </section>

                <!-- SECTION 2: ROLES & PERMISSIONS -->
                <section id="roles-permissions" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 02</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">User Personas &amp; System Permissions</h2>
                    </div>
                    <p>
                        The application implements strict role-based access control (RBAC) across three operational personas:
                    </p>
                    <div class="space-y-4 pt-2">
                        <div class="p-4 rounded-xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-800">Administrator</span>
                                <span class="text-xs text-slate-400 font-mono">Role: admin</span>
                            </div>
                            <p class="text-xs text-slate-600">
                                <strong>Responsibilities:</strong> Executive oversight, creating staff accounts, assigning roles, deactivating departing crew members, inspecting system audit logs, and unlocking locked signed manifests for revisions with an audit trail reason.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">Move Coordinator</span>
                                <span class="text-xs text-slate-400 font-mono">Role: coordinator</span>
                            </div>
                            <p class="text-xs text-slate-600">
                                <strong>Responsibilities:</strong> Relocation dispatch, customer shipper records, job scheduling, assigning field surveyors, selecting property room templates (Studio, 1BR, 2BR, 3BR House, 4BR House, Office), generating secure customer share links with PIN security gates, and supervising move status transitions.
                            </p>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 bg-white space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800">Field Surveyor &amp; Driver</span>
                                <span class="text-xs text-slate-400 font-mono">Role: surveyor</span>
                            </div>
                            <p class="text-xs text-slate-600">
                                <strong>Responsibilities:</strong> On-site field operations using phone or tablet. Tapping items into room tabs from the standard catalog, uploading pre-existing damage photos, presenting the touch signature pad to the customer at loading, and conducting item-by-item check-off at the delivery destination.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- SECTION 3: RELOCATION LIFECYCLE -->
                <section id="move-lifecycle" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 03</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Relocation State Machine &amp; Guardrails</h2>
                    </div>
                    <p>
                        A move advances through strict, non-reversible lifecycle statuses to ensure manifest integrity:
                    </p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                            <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700">
                                <tr>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3">Description</th>
                                    <th class="py-2.5 px-3">Allowed Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold font-mono text-slate-800">draft</td>
                                    <td class="py-2.5 px-3 text-slate-600">Initial job record created by Coordinator.</td>
                                    <td class="py-2.5 px-3">Edit job meta, seed room templates, cancel job.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold font-mono text-blue-700">in_survey</td>
                                    <td class="py-2.5 px-3 text-slate-600">Surveyor actively inventorying goods on-site.</td>
                                    <td class="py-2.5 px-3">Add items, adjust quantities, upload damage photos.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold font-mono text-amber-700">pending_signoff</td>
                                    <td class="py-2.5 px-3 text-slate-600">Survey finished; awaiting shipper signature.</td>
                                    <td class="py-2.5 px-3">Review manifest, generate customer share link.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold font-mono text-indigo-700">origin_signed</td>
                                    <td class="py-2.5 px-3 text-slate-600">Manifest signed; cryptographically locked.</td>
                                    <td class="py-2.5 px-3">Start transit, supervisory unlock for revision.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold font-mono text-purple-700">in_transit</td>
                                    <td class="py-2.5 px-3 text-slate-600">Shipment loaded and moving to destination.</td>
                                    <td class="py-2.5 px-3">Access delivery check-off workspace.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold font-mono text-emerald-700">delivered</td>
                                    <td class="py-2.5 px-3 text-slate-600">All items accounted for; delivery receipt signed.</td>
                                    <td class="py-2.5 px-3">Read-only print, export manifest PDF.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- SECTION 4: SURVEY WORKSPACE -->
                <section id="survey-workspace" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 04</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">On-Site Survey Workspace</h2>
                    </div>
                    <p>
                        The survey interface (<code class="bg-slate-100 px-1 py-0.5 rounded font-mono text-xs">/moves/{id}/survey</code>) is optimized for touch tablets and smartphones:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-xs sm:text-sm text-slate-700">
                        <li><strong>Horizontal Room Tabs:</strong> Switch between rooms (Living Room, Kitchen, Master Bedroom) with 1 tap. Room headers show live item count and cumulative cubic feet.</li>
                        <li><strong>Standard Catalog Quick-Add:</strong> Searchable drawer containing 45+ items categorized by Living Room, Bedroom, Dining, Appliances, and Boxes. Tapping an item instantly adds it with standard industry dimensions.</li>
                        <li><strong>Sequential Tag Generator:</strong> Every item receives a sequential tag number formatted as <code class="font-mono">#001</code>, <code class="font-mono">#002</code>, avoiding human duplicates.</li>
                        <li><strong>Quantity Steppers:</strong> Quick <code class="font-mono font-bold">+</code> and <code class="font-mono font-bold">-</code> steppers allow rapid counting of multiple chairs, boxes, or lamps.</li>
                    </ul>
                </section>

                <!-- SECTION 5: CONDITION & PHOTOS -->
                <section id="condition-photos" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 05</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Pre-Existing Damage Documentation &amp; Photos</h2>
                    </div>
                    <p>
                        To protect the moving company from false damage claims at the destination, MoveLedger enforces strict condition rules:
                    </p>
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 space-y-2 text-xs text-amber-900">
                        <p class="font-bold">Mandatory Damage Rule:</p>
                        <p>
                            Whenever an item's condition is marked as <strong>Pre-Existing Damage</strong>, the surveyor cannot save the record without providing specific descriptive notes (e.g., <em>"Deep scratch on left mahogany armrest"</em>).
                        </p>
                    </div>
                    <p>
                        Surveyors can upload up to 5 on-site camera photos per inventory item. Thumbnail previews appear on the item card, and clicking any thumbnail opens an enlarged lightbox view for inspection by the shipper or claims adjuster.
                    </p>
                </section>

                <!-- SECTION 6: MANIFEST SIGNING -->
                <section id="manifest-signing" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 06</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Cryptographic Manifest Sign-Off &amp; Locking</h2>
                    </div>
                    <p>
                        When the origin survey is complete, the shipper signs the digital Bill of Lading on-device or remotely:
                    </p>
                    <ol class="list-decimal pl-5 space-y-1.5 text-xs sm:text-sm text-slate-700">
                        <li>The surveyor navigates to <code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded">Sign Origin Manifest</code>.</li>
                        <li>The customer draws their legal signature on the HTML5 canvas, verifies their printed name, selects their authority role, and checks the legal acknowledgment box.</li>
                        <li>Upon submission, the server computes a canonical JSON snapshot of all rooms, items, conditions, and tag numbers, generating a unique <strong>SHA-256 fingerprint</strong>.</li>
                        <li>The signature image is saved to secure storage and linked to the cryptographic digest.</li>
                        <li>The move transitions to <code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded">origin_signed</code>, locking all item quantities and descriptions against alteration.</li>
                    </ol>
                    <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-700 space-y-1">
                        <p class="font-bold text-slate-900">Unlock for Revision Policy:</p>
                        <p>
                            If the customer requests adding another room after signing, only an Administrator or Coordinator can execute an <em>Unlock for Revision</em>. This reverts the job to <code class="font-mono text-xs">in_survey</code>, invalidates the previous origin signature, and records an immutable entry in the audit log.
                        </p>
                    </div>
                </section>

                <!-- SECTION 7: CUSTOMER PORTAL -->
                <section id="customer-portal" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 07</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Customer Share Portal &amp; PIN Gate</h2>
                    </div>
                    <p>
                        Customers do not need to download an app or create a password to inspect their inventory:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-xs sm:text-sm text-slate-700">
                        <li>The Coordinator generates a unique 64-character token link (<code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded">/share/{token}</code>) with an optional 4-6 digit numeric PIN.</li>
                        <li>When the customer taps the link, they enter their PIN to unlock the manifest portal.</li>
                        <li>The portal displays room breakdowns, item tags, declared values, condition notes, and photo thumbnails.</li>
                        <li>Customers can review their cryptographic signature certificate or execute their loading sign-off remotely from their personal phone.</li>
                        <li>A dedicated print stylesheet (<code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded">@media print</code>) formats the screen into a printable Bill of Lading with official headers.</li>
                    </ul>
                </section>

                <!-- SECTION 8: DELIVERY CHECK-OFF -->
                <section id="delivery-checkoff" class="space-y-4">
                    <div class="border-b border-slate-200 pb-3">
                        <span class="text-xs font-mono font-bold text-orange-600 uppercase">Section 08</span>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight mt-1">Destination Delivery Check-Off</h2>
                    </div>
                    <p>
                        When the transport truck arrives at the destination address:
                    </p>
                    <ol class="list-decimal pl-5 space-y-1.5 text-xs sm:text-sm text-slate-700">
                        <li>The crew opens the Delivery Check-Off workspace (<code class="font-mono text-xs bg-slate-100 px-1 py-0.5 rounded">/moves/{id}/delivery</code>).</li>
                        <li>Items are checked off as they are unloaded into the house.</li>
                        <li>Entire rooms can be marked delivered with a single tap, or items can be toggled individually.</li>
                        <li>If an item is damaged during transit, it is flagged as <strong>Damaged in Transit</strong>, requiring transit exception notes.</li>
                        <li>Once all items are accounted for, the customer inspects the exceptions and signs the destination delivery receipt, marking the relocation as <strong>Completed</strong>.</li>
                    </ol>
                </section>

                <!-- SECTION 9: CUSTOMIZATION NOTICE (EXACT TEXT REQUIRED) -->
                <section id="customization" class="p-6 sm:p-8 rounded-2xl bg-orange-50 border border-orange-200 space-y-4">
                    <div class="flex items-center gap-2 text-orange-900 font-bold text-base sm:text-lg">
                        <svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Need something more tailored?</span>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-800 leading-relaxed">
                        This demo showcases the current application features and workflows. The web application can be further developed, extended, and customized to fit your business processes and specific requirements. Features, workflows, interface, and integrations can be discussed and tailored to your needs.
                    </p>

                    <div class="pt-2 text-xs sm:text-sm font-semibold text-slate-900 flex flex-wrap items-center gap-2">
                        <span>Contact:</span>
                        <a href="mailto:nixresponding@gmail.com" class="text-orange-700 hover:text-orange-900 font-mono underline font-bold">
                            nixresponding@gmail.com
                        </a>
                    </div>
                </section>

            </div>

        </div>
    </div>

</x-layouts.public>
