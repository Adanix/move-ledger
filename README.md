# MoveLedger

**Mobile-First Relocation Inventory & Cryptographic Digital Bill of Lading (BOL) Platform**

MoveLedger is a modern web application engineered for relocation enterprises, dispatch coordinators, and field survey crews. It replaces paper clipboards and handwritten carbon-copy inventory lists with touch-optimized mobile workflows, automatic cubic feet and weight calculations, mandatory pre-existing damage photo documentation, and tamper-evident SHA-256 digital signature manifest locking.

---

## Key Features

### 1. 1-Tap Room Inventory & Standard Catalog Quick-Add
- Preloaded with 45+ standard household goods across 8 categories (Living Room, Bedroom, Dining, Appliances, Office, Outdoor, Boxes, Fragile).
- Tapping an item applies industry-standard cubic feet (`cu ft`), weight (`lbs`), and box conversion factors.
- Auto-generates sequential tag identifiers (`#001`, `#002`, `#003`, ...) atomically to prevent duplicate tags across concurrent crew devices.
- Touch-friendly stepper controls allow rapid quantity adjustments on smartphones and tablets.

### 2. Pre-Existing Damage Tracking & Photo Evidence
- Categorizes item condition as *Good / Normal Wear*, *Fair / Aged Wear*, or *Pre-Existing Damage*.
- Enforces mandatory condition notes whenever pre-existing damage is flagged before the record can be saved.
- Supports up to 5 on-site camera photo uploads per item with instant lightbox inspection.

### 3. Cryptographic Manifest Lock & Digital Signatures
- HTML5 canvas signature pad captures customer and crew signatures directly on touch screens.
- Generates a deterministic canonical JSON snapshot of all rooms, items, condition notes, and tags.
- Hashes the manifest payload using **SHA-256**, permanently binding the signature image to the exact state of goods at loading.
- Cryptographically locks inventory records against modification once signed.
- Unlocking for revisions requires Administrator or Coordinator authorization with an audited justification reason.

### 4. Real-Time Destination Delivery Check-Off
- Dedicated on-site delivery workspace for unloading crews and drivers.
- Toggle item status between *Delivered*, *Damaged in Transit*, and *Missing*.
- Enforces transit exception notes for damaged or missing items.
- Supports 1-tap room-level bulk check-off when all room items arrive intact.
- Destination delivery receipt signature captures customer confirmation and marks the move as completed.

### 5. Secure Customer Manifest Portal & PIN Gate
- Shippers view their manifest remotely via a 64-character encrypted token URL (`/share/{token}`).
- Optional 4-6 digit numeric PIN gate prevents unauthorized access.
- Shippers can inspect room-by-room items, declared values, damage photos, and execute remote origin or delivery sign-offs.
- Dedicated print stylesheet (`@media print`) formats the portal into an official printable Bill of Lading document.

### 6. Role-Based Field Governance
- **Administrator (`admin`)**: Full operational oversight, staff account management, system configuration, audit trail logs, and manifest revision unlocking.
- **Move Coordinator (`coordinator`)**: Relocation dispatch, customer shipper records, job scheduling, property room templates, and PIN-protected share link generation.
- **Field Surveyor & Driver (`surveyor`)**: On-site mobile survey workspace, 1-tap item entry, condition photo capture, touch signature collection, and destination delivery check-off.

### 7. Public Presentation & 1-Click Demo Login
- Public landing page (`/`) with product value proposition, core modules, and workflow overview.
- Interactive feature documentation manual (`/docs`) with table of contents and anchor navigation.
- Demo persona selection page (`/demo/login`) with 1-click login buttons for all 3 roles with automatic session regeneration and no password entry required.

---

## Technology Stack

- **Framework**: Laravel 13, PHP 8.5
- **Database**: MySQL 8.0+ / SQLite (Testing)
- **Frontend**: Blade Templates, Tailwind CSS 4, Alpine.js 3.x, Vite
- **Typing & Quality**: Strict typing (`declare(strict_types=1);`), exact `DECIMAL` precision for all physical and monetary calculations
- **Containerization**: Docker, Docker Compose, Nginx

---

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher (PHP 8.5 recommended)
- Composer 2.x
- Node.js 20+ & npm
- MySQL 8.0+ or compatible database server

### 1. Clone the Repository
```bash
git clone https://github.com/Adanix/move-ledger.git
cd move-ledger
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
Copy the example environment file and generate the application encryption key:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moveledger
DB_USERNAME=root
DB_PASSWORD=secret

# Enable button-based demo persona logins
DEMO_MODE=true
```

### 4. Run Migrations & Seeders
Run migrations to set up the 10 database tables and seed standard catalog benchmarks, staff accounts, and sample moves:
```bash
php artisan migrate --seed
```

### 5. Link Public Storage
Create the symbolic link to serve item photos and digital signatures:
```bash
php artisan storage:link
```

### 6. Compile Frontend Assets
Build Tailwind CSS 4 and Alpine.js assets:
```bash
npm run build
```

### 7. Start the Local Server
```bash
php artisan serve
```

The application will be accessible at `http://localhost:8000`.

---

## Seeded Demonstration Personas

When `DEMO_MODE=true` is enabled, navigate to `/demo/login` to log in with a single click. Alternatively, use standard credentials on the login screen (`/login`):

| Persona Role | Email | Password | Capabilities |
|---|---|---|---|
| **Administrator** | `admin@moveledger.test` | `password` | System configuration, staff management, manifest unlock revisions |
| **Move Coordinator** | `dispatch@moveledger.test` | `password` | Job dispatch, customer directory, share link generation |
| **Field Surveyor** | `surveyor@moveledger.test` | `password` | On-site surveys, photo capture, touch signatures, delivery check-off |

---

## Canonical Routes

| HTTP Method | URI | Route Name | Access Level | Description |
|---|---|---|---|---|
| `GET` | `/` | `public.home` | Public | Public landing page and product overview |
| `GET` | `/docs` | `public.docs` | Public | Complete feature guide and operational manual |
| `GET` | `/demo/login` | `demo.login` | Demo Mode | 1-Click persona selection cards |
| `POST` | `/demo/quick-login` | `demo.quick-login` | Demo Mode | Authenticates allowlisted persona key |
| `POST` | `/logout` | `logout` | Authenticated | Terminates session; redirects to `/demo/login` |
| `GET` | `/login` | `login` | Guest | Standard credential login screen |
| `GET` | `/dashboard` | `dashboard` | Authenticated | Operational field dashboard |
| `GET` | `/moves` | `moves.index` | Authenticated | Relocation job list and filtering |
| `GET` | `/moves/{id}/survey` | `moves.survey` | Authenticated | On-site touch room survey workspace |
| `GET` | `/moves/{id}/delivery`| `moves.delivery`| Authenticated | Destination delivery check-off workspace |
| `GET` | `/share/{token}` | `share.show` | Public / Token | Customer Bill of Lading portal |
| `GET` | `/share/{token}/pin` | `share.pin` | Public / Token | Customer PIN entry security gate |

---

## Database Architecture

MoveLedger utilizes 10 core tables with relational integrity and soft deletion safeguards:

1. `users`: Staff members with typed enum roles (`admin`, `coordinator`, `surveyor`) and active state checks.
2. `customers`: Shipper contact records, billing details, and historical relocation files.
3. `catalog_items`: Pre-benchmarked standard household goods with default volume, weight, and fragility flags.
4. `moves`: Central job records containing route addresses, property templates, scheduled dates, and exact decimal totals.
5. `move_rooms`: Room groupings (Living Room, Kitchen, Master Bedroom) with cumulative volume metrics.
6. `inventory_items`: Individual items with sequential tag numbers, quantities, packing types, conditions, and delivery statuses.
7. `inventory_item_photos`: Photographic evidence of pre-existing wear captured prior to vehicle loading.
8. `move_shares`: Cryptographic access tokens with expiration dates, access counters, and hashed security PINs.
9. `move_signatures`: Captured PNG signature data URIs bound to canonical manifest SHA-256 digests.
10. `audit_logs`: Immutable change history tracking status transitions, revision unlocks, and signature verifications.

---

## Automated Test Suite

MoveLedger includes comprehensive automated unit and feature tests covering all critical paths:

```bash
php artisan test
```

Test coverage includes:
- Exact decimal precision calculations for cubic feet, weights, box factors, and declared values.
- Role-based authorization policies and inactive user blocking.
- Move job provisioning, property room seeding, and sequential tag generation.
- 1-tap catalog item additions, quantity steppers, and condition notes enforcement.
- PIN-guarded customer share links and remote signature captures.
- Destination delivery check-offs and transit exception reporting.
- Public landing page, documentation anchors, demo persona quick-logins, and session regeneration.

---

## Inquiries & Customization

This demonstration showcases the out-of-the-box MoveLedger relocation inventory workflows. The system can be further tailored, extended, and customized to accommodate proprietary rate cards, custom ERP integrations, barcode/RFID scanning hardware, and enterprise logistics processes.

- **Inquiries & Customization**: [nixresponding@gmail.com](mailto:nixresponding@gmail.com)

---

## License

The MoveLedger application is open-sourced software licensed under the [MIT License](LICENSE).
