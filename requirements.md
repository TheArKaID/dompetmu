# System Requirements Prompt for AI Agent

**Project Name:** Card Inventory System - Bank Syariah Indonesia (BSI)
**Objective:** To build a physical card inventory management system that tracks the lifecycle of cards from external vendors, the central warehouse, the personalization (Perso) process, down to branch distribution and stock management.

## 1. Core Architecture & Pattern Requirements

* **Application Architecture (Monolith):** The application must be built as a **Monolith**. All functionalities (ledger, order validation, webhook handling) must reside within a single, unified codebase. Do not separate them into independent microservices. Maintain clean code by organizing the logic modularly within the monolithic structure.
* **Authentication & Authorization:** The system utilizes a Single-Realm architecture (e.g., via Keycloak) for access management (Roles: Central Admin, Warehouse Staff, Branch Admin). All authentication processes must funnel into one single realm to streamline user and client management.
* **Inventory Tracking (Flat Ledger Pattern):** To accommodate complex stock movements (additions from vendors, deductions for Perso, replacements, and branch transfers), stock calculations **must** use the *Flat Ledger* pattern. Do not use a static `current_stock` column that is directly updated, as this is prone to race conditions. Instead, use a `stock_ledger` table to record every *in/out* mutation, and perform a `SUM` aggregation to retrieve the current stock.

## 2. Database Entities & Models

Design the database with the following core entities:

1. **Card_Product (Dynamic):**
* Stores card product types (CRUD enabled).
* *Initial Data:* GPN Silver, GPN Simpel, GPN Platinum.


2. **Card_Type:**
* Stores card service types.
* *Enum/Data:* Instant, Reguler, Reguler Byond/Credit Card.


3. **Stock_Ledger (Flat Ledger):**
* `id`, `location_type` (Warehouse/Branch), `location_id` (Branch ID, null if Central Warehouse), `product_id`, `type_id`, `qty_change` (+ or -), `reference_type` (Vendor_In, Perso_Out, Cabang_In, etc.), `reference_no`, `created_at`.


4. **Disposition_Order (DO):**
* `do_no` (Auto-generated, e.g., DO00001), `branch_id`, `status` (Pending, Approved, Rejected, Processing, Shipped, Completed), `notes` (for rejection reasons).


5. **Order_Item:**
* `do_id`, `product_id`, `type_id`, `qty`, `status_perso` (Pending, Good, Bad).



## 3. Business Logic & Process Flow (State Machine)

The AI Agent must implement the system's logic flow according to the following states:

**A. Warehouse Inbound (From External Vendor)**

* **Trigger:** QR Scan from the Vendor.
* **Action:** Insert data into `Stock_Ledger` with `location_type` = 'Warehouse', `qty_change` = +[QR Quantity], `reference_type` = 'Vendor_In'.

**B. Branch Card Request**

* **Validation Rule:** The minimum request is 250 cards for 1 combination of *Type* and *Product*. The requested quantity **must** be a multiple of 250 (e.g., 250, 500, 750). Branches can mix multiple types/products in a single Order, provided each item strictly adheres to this rule.
* **Action:** Generate a new `do_no` (Disposition Order) with a `Pending` status.

**C. Approval Process (Central Admin)**

* The Admin reviews the DO.
* **If Reject:** DO status changes to `Rejected`. The system must enforce mandatory `notes` detailing the rejection reason. The flow ends here.
* **If Approve:** DO status changes to `Approved`.

**D. Warehouse Processing & Perso (External API)**

* **Warehouse Check:** The system verifies stock availability via `Stock_Ledger` aggregation.
* **Physical Distribution & Stock Deduction:** If stock is sufficient, insert a record into `Stock_Ledger` (`location_type` = 'Warehouse', `qty_change` = -[Requested Quantity], `reference_type` = 'Perso_Out').
* **Perso Process:** The system exposes a REST API Endpoint (open or token-based) to receive *callbacks/webhooks* from the External System (Perso Machine) regarding the *Reporting* results.

**E. Perso Reporting Logic (API Webhook)**
The system receives a payload from the external API: `{ "do_no": "...", "item_id": "...", "is_good": boolean }`

* **If `is_good` == true (Yes):**
The card was successfully personalized. This data is pushed to a Reporting Dashboard equipped with *Sort & Filter* capabilities.
* **If `is_good` == false (No):**
The card was damaged during the perso process. **This does NOT trigger automatic stock deduction.** Instead, the damaged report is sent to the Reporting Dashboard. The Central Admin must review this report and perform a manual authorization to replace the damaged cards. Once authorized by the Admin, the system inserts a new record into `Stock_Ledger` (`qty_change` = -[Damaged Quantity], `reference_type` = 'Perso_Replacement'), and the physical distribution process is repeated for the replacement cards.

**F. Delivery & Branch Inbound**

* **Filter Check:** Ensure that only **Reguler** and **Instant** card types are physically shipped and recorded in the Branch's inventory. (Reguler Byond/Credit Card types have a separate distribution route bypassing branch stock).
* **Generate Delivery QR:** The system generates a physical QR Code for the delivery package dispatched to the branch.
* **Branch QR Scan:** The Branch Admin scans the QR code upon physical arrival.
* **Update Branch Stock:** Insert a record into `Stock_Ledger` (`location_type` = 'Branch', `location_id` = [Branch ID], `qty_change` = +[Quantity], `reference_type` = 'Branch_In'). DO status changes to `Completed`.

## 4. API & QR Data Specification

**QR Code Payload Format (Stringified JSON):**
When generated for branch delivery, the QR code content must follow this format:

```json
{
  "product": "GPN SILVER",
  "chip": "SLJ26 INFINEON", 
  "tipe": "Reguler",
  "qty": 250,
  "do_no": "DO00001"
}

```

*(Note: The "chip" field is optional and can be null or empty).*

**Additional Instructions for the Agent:**
Ensure that the codebase utilizes robust error handling and database transaction blocks, especially when updating the ledger. As this is a Monolith, maintain strict logical separation using namespaces, controllers, or service classes for Ledger Calculations, Order Validations, and Perso Webhook Handling to ensure long-term maintainability.