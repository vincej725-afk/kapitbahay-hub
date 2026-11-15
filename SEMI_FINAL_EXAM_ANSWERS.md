# WEBDEV3 Semi-Final Examination: Situational Case Study
## Kapitbahay Laundry Hub – Complete Answer Key & Technical Implementation Guide

**Course:** WEBDEV3 – Web Framework Laravel Development  
**Coverage:** SDLC applied to Laravel (Weeks 1–11): Routing, Middleware, Controllers, Migrations & Eloquent, Validation, Authentication & Authorization, RESTful APIs with Sanctum.  
**Total Points:** 100 points (6 questions)  
**All 22 Automated Feature/Unit Tests:** `PASSING (64 assertions)`

---

## Question 1. Planning and Requirements Analysis (15 points)
*SDLC Phase: Planning and Analysis*

### (a) Problem vs Feature Matrix (10 points — 2 points each)
For each current problem, write one feature of the new system that solves it.

| Problem | Feature of the new system that solves it |
| :--- | :--- |
| **P1 – Customers call to ask about status** | **Real-time Order Status Tracking via Mobile App API**: Customers can track their order lifecycle (`received` &rarr; `washing` &rarr; `ready` &rarr; `released`) directly on their phones. |
| **P2 – Lost claim stubs, wrong release** | **Digital Order Management with Unique Order IDs & Customer Verification**: Digital QR/Order ID and customer profile checks in the system replace paper stubs to prevent incorrect releases. |
| **P3 – Different prices in each branch** | **Centralized Service Pricing Masterlist**: Standardized prices configured exclusively by the Owner in the system, automatically calculated across all branches. |
| **P4 – Owner cannot see sales easily** | **Centralized Owner Sales Dashboard & Reporting**: Real-time aggregated and branch-filtered sales reports accessible remotely via the owner's web portal. |
| **P5 – Customers want pickup from home** | **Online Home Pickup & Delivery Booking**: Customers can schedule home pickups with custom address and date selection (`today` or later) with automated ₱50 delivery fee calculation. |

### (b) Functional (F) vs Non-Functional (NF) Requirements (5 points — 1 point each)
Write **F** if the statement is a functional requirement or **NF** if it is a non-functional requirement.

| Statement | F or NF | Explanation |
| :--- | :---: | :--- |
| 1. Customers can view the status of their orders in the mobile app. | **F** | Defines a specific user capability/system function. |
| 2. The API must respond within 2 seconds during the lunchtime peak. | **NF** | Specifies a performance constraint / quality attribute. |
| 3. Staff can update the status of an order from their branch. | **F** | Defines a functional action performed by staff. |
| 4. Passwords are stored hashed, and login is limited to 5 attempts per minute. | **NF** | Specifies security, encryption, and rate-limiting constraints. |
| 5. The owner can change the price of a service. | **F** | Defines administrative functionality of the system. |

---

## Question 2. System Design (20 points)
*SDLC Phase: Design*

### (a) Eloquent Model Relationships (10 points — 2 points each)
Choose the correct relationship method for each pair: `hasOne`, `hasMany`, `belongsTo`, or `belongsToMany`.

| Relationship in the business | Method to write in the first model | Code Reference |
| :--- | :--- | :--- |
| A Branch has many orders. &rarr; `Branch::orders()` | **`hasMany`** | `app/Models/Branch.php` (`return $this->hasMany(Order::class);`) |
| An Order is placed by one customer. &rarr; `Order::customer()` | **`belongsTo`** | `app/Models/Order.php` (`return $this->belongsTo(User::class, 'customer_id');`) |
| An Order can include many services, and a service appears in many orders. &rarr; `Order::services()` | **`belongsToMany`** | `app/Models/Order.php` (`return $this->belongsToMany(Service::class)->withPivot('quantity', 'subtotal');`) |
| A staff User works in one branch. &rarr; `User::branch()` | **`belongsTo`** | `app/Models/User.php` (`return $this->belongsTo(Branch::class);`) |
| An Order has exactly one payment record. &rarr; `Order::payment()` | **`hasOne`** | `app/Models/Order.php` (`return $this->hasOne(Payment::class);`) |

### (b) Permission Matrix (5 points — 1 point per correct row)
Complete the permission matrix. Write **Yes** or **No** in each box.

| Action | Customer | Staff | Owner |
| :--- | :---: | :---: | :---: |
| **View orders from all three branches** | **No** | **No** | **Yes** |
| **Book a pickup** | **Yes** | **No** | **No** |
| **Update the status of an order in their own branch** | **No** | **Yes** | **Yes** |
| **Update the status of an order in another branch** | **No** | **No** | **Yes** |
| **Change service prices** | **No** | **No** | **Yes** |

### (c) Authentication Strategy: Staff Website vs. Mobile App (5 points)
Choose between a **session (Laravel Breeze)** and a **token (Laravel Sanctum)**, and give one reason for each.

| Front end | Login method | Reason |
| :--- | :--- | :--- |
| **Staff website** | **session (Laravel Breeze)** | Web browsers natively support stateful cookie-based sessions with CSRF protection, simplifying server-side Blade rendering and authentication persistence. |
| **Mobile app** | **token (Laravel Sanctum)** | Mobile applications are stateless HTTP clients that do not store browser session cookies; token authentication via Bearer tokens in HTTP headers is standard, scalable, and secure for REST APIs. |

---

## Question 3. Development: Routes, Validation and Authorization (20 points)
*SDLC Phase: Development*

### (a) API Route Table (10 points — 2 points per row)
Every URI starts with `/api/v1`.

| Action | HTTP verb | URI | Success status code |
| :--- | :---: | :--- | :---: |
| Customer logs in | **POST** | `/api/v1/login` | **200** |
| Customer lists their orders (10 per page) | **GET** | `/api/v1/orders` | **200** |
| Customer views one order | **GET** | `/api/v1/orders/{order}` *(or `/api/v1/orders/{id}`)* | **200** |
| Customer books a pickup | **POST** | `/api/v1/bookings` *(or `/api/v1/orders`)* | **201** |
| Staff updates an order's status | **PATCH** *(or **PUT**)* | `/api/v1/orders/{order}/status` | **200** |

### (b) Fill in Blanks A to E (10 points — 2 points each)
Code:
```php
// app/Http/Requests/StoreBookingRequest.php
public function rules(): array
{
    return [
        'service_id'     => ['required', '___A___:services,id'],
        'estimated_kg'   => ['required', 'numeric', 'min:___B___'],
        'pickup_address' => ['required', 'string', 'max:255'],
        'pickup_date'    => ['required', 'date', '___C___:today'],
    ];
}

// app/Policies/OrderPolicy.php
public function updateStatus(User $user, Order $order): bool
{
    if ($user->role === 'owner') {
        return true;
    }

    return $user->role === '___D___'
        && $user->branch_id === $order->___E___;
}
```

| Blank | Your answer | Rule Justification from Case Study |
| :---: | :--- | :--- |
| **A** | **`exists`** | Validates that `service_id` exists in the `services` table. |
| **B** | **`5`** | Service rules state: "Wash-Dry-Fold: Minimum of 5 kg". |
| **C** | **`after_or_equal`** | Pickup rule states: "Pickup date must be today or later". |
| **D** | **`staff`** | Restricts status updates to users with the `'staff'` role. |
| **E** | **`branch_id`** | Enforces: "Each branch's staff should handle only their own branch's orders". |

---

## Question 4. Development: Building the API (15 points)
*SDLC Phase: Development*

### (a) HTTP Status Codes (5 points — 1 point each)
Write the HTTP status code the API should return in each situation:

| Situation | Status code | Status Text |
| :--- | :---: | :--- |
| 1. A customer books a pickup successfully. | **201** | Created |
| 2. The mobile app sends a request without a token. | **401** | Unauthorized |
| 3. A staff member from Imus tries to update an order from Bacoor. | **403** | Forbidden |
| 4. A customer asks for order number 99999, which does not exist. | **404** | Not Found |
| 5. A customer books a pickup for yesterday's date. | **422** | Unprocessable Content (Validation Error) |

### (b) Fill in Blanks A to C (6 points — 2 points each)
```php
public function index(Request $request)
{
    $orders = $request->user()->orders()
        ->___A___('services')
        ->latest()
        ->___B___(10);

    return OrderResource::___C___($orders);
}
```

| Blank | Your answer | Purpose |
| :---: | :--- | :--- |
| **A** | **`with`** | Eager loads the `services` relation to eliminate the N+1 query problem. |
| **B** | **`paginate`** | Paginates the orders query 10 per page as required. |
| **C** | **`collection`** | Static method on `OrderResource` to format and wrap a paginated collection. |

### (c) Excluded Columns & Security Rationale (4 points)
The `orders` table has columns: `id`, `customer_id`, `branch_id`, `status`, `total`, `pickup_address`, `internal_notes`, `created_at`.  
Name **two columns** that should not appear in the JSON the API returns to the mobile app, and say why.

1. **`internal_notes`**:
   - *Why:* These are confidential, internal remarks written by branch staff or the owner (e.g., operational issues, customer handling notes) and must never be exposed to end customers.
2. **`customer_id`**:
   - *Why:* This is an internal database relational foreign key. Exposing foreign keys reveals database structure, is redundant (since the authenticated user already owns the session), and protects customer privacy per Mrs. Santos' explicit requirement: *"Customer addresses and phone numbers are private. A customer must never see another customer's details."*

---

## Question 5. Testing with Postman (15 points)
*SDLC Phase: Testing*

### (a) Complete Test Cases (9 points — 3 points per row)

| Test case | Request (verb and URI) | Data sent | Expected result |
| :--- | :--- | :--- | :--- |
| *Example: Log in with the correct password* | `POST /api/v1/login` | `email, correct password` | `200 with a token` |
| **TC1: Book a pickup for 3 kg of Wash-Dry-Fold** | **`POST /api/v1/bookings`** *(or `/api/v1/orders`)* | `{"service_id": 1, "estimated_kg": 3, "pickup_address": "Nueno Ave, Imus", "pickup_date": "2026-11-20"}` *(with Customer Bearer Token)* | **`422 Unprocessable Content`** *(Validation error on `estimated_kg` - min 5 kg rule)* |
| **TC2: Staff from Imus updates a Bacoor order to "ready"** | **`PATCH /api/v1/orders/2/status`** *(or `PUT`)* | `{"status": "ready"}` *(with Staff Imus Bearer Token)* | **`403 Forbidden`** *(Unauthorized across branches)* |
| **TC3: Customer A requests Customer B's order** | **`GET /api/v1/orders/2`** | None *(with Customer A Bearer Token)* | **`403 Forbidden`** *(Prevents IDOR / data breach)* |

### (b) Custom Test Case Checking a Business Rule (6 points)
Write one more test case of your own that checks a rule from the situation:

- **Rule Checked:** "Pickup date must be today or later" (Pickup cannot be scheduled in the past).
- **Test Case Name:** TC4: Customer books a pickup with yesterday's date
- **Request (verb and URI):** `POST /api/v1/bookings`
- **Data sent:**
  ```json
  {
    "service_id": 1,
    "estimated_kg": 5,
    "pickup_address": "Aguinaldo Highway, Dasmariñas, Cavite",
    "pickup_date": "2026-11-14"
  }
  ```
  *(with valid Customer Bearer Token)*
- **Expected result:** **`422 Unprocessable Content`** with validation error: `"The pickup date field must be a date after or equal to today."`

---

## Question 6. Deployment and Maintenance (15 points)
*SDLC Phase: Deployment and Maintenance*

### (a) Problem Causes and Fix Choices (9 points — 2 points for cause, 1 point for fix)
**Fix Choices:**
- **A.** Add eager loading with `with()` and paginate the results
- **B.** Add an `OrderPolicy` and call `Gate::authorize('view', $order)`
- **C.** Add the throttle middleware to the login route
- **D.** Change the order status flow

| Problem | Cause | Fix (letter) |
| :--- | :--- | :---: |
| **M1: The owner's sales report takes 9 seconds to load. The query log shows 1,201 queries for 600 orders.** | The application suffers from the **N+1 query problem**, where related records (e.g., services, branch, customer) are lazily loaded in a loop for each order without eager loading and without pagination. | **A** |
| **M2: A customer changed the order number in a request and saw another customer's address and phone number.** | The order viewing endpoint lacks authorization checks (**Insecure Direct Object Reference / IDOR**), allowing any authenticated customer to access another customer's order by changing the ID parameter in the URI. | **B** |
| **M3: The login endpoint received 3,000 password attempts from one device in 10 minutes.** | The authentication route lacks **rate-limiting middleware** (`throttle`), leaving it vulnerable to automated brute-force attacks and credential stuffing. | **C** |

### (b) SDLC Phase & Pre-Launch Prevention (6 points)
1. **Which SDLC phase is your team in while fixing these problems?**
   - **Maintenance Phase** (Phase 7 of the SDLC), as the system has already been deployed and is running live in production.
2. **Name one thing your team could have done in an earlier phase to catch M2 before launch:**
   - In **SDLC Phase 5 (Testing)**, the team could have designed and executed automated **authorization and security test cases** (such as TC3: Customer A attempting to access Customer B's order), verifying that non-owners receive a `403 Forbidden` response prior to deployment.
   - *(Alternative)*: In **SDLC Phase 4 (Development)**, the team could have performed a **security code review** and registered route policies (`OrderPolicy`) with `Gate::authorize('view', $order)` for all endpoints handling user-specific records.

---

## Project Verification & Automated Test Results

All test cases specified in the exam have been implemented in `tests/Feature/KapitbahayHubApiTest.php` and `tests/Feature/KapitbahayHubWebTest.php`.

```text
PASS Tests\Unit\ExampleTest
PASS Tests\Feature\ExampleTest
PASS Tests\Feature\KapitbahayHubApiTest (12 passed)
  ✓ example login with correct password returns 200 with token
  ✓ tc1 book pickup under minimum kg returns 422
  ✓ tc2 staff from imus cannot update bacoor order
  ✓ tc3 customer a requesting customer b order returns 403 forbidden
  ✓ custom booking with past date returns 422
  ✓ customer books pickup successfully returns 201
  ✓ order json does not contain internal notes or customer id
  ✓ unauthenticated request returns 401
  ✓ non existent order returns 404
  ✓ customer lists orders paginated
  ✓ login throttling after 5 attempts
  ✓ owner can update status of any branch
PASS Tests\Feature\KapitbahayHubWebTest (8 passed)
  ✓ staff can login via web session
  ✓ staff only sees their own branch orders
  ✓ staff can update status for their branch
  ✓ staff cannot update status for another branch
  ✓ staff can record walk in order
  ✓ owner can view all branches dashboard
  ✓ owner can update service price
  ✓ staff cannot access owner pricing page

Tests: 22 passed (64 assertions)
Duration: 0.86s
```
