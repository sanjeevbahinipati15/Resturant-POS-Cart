# Fullstack Developer Assessment — CodeIgniter 4 POS & Orders API

This repository contains the completed fullstack assessment built using **PHP 8+** and **CodeIgniter 4**. The project demonstrates clean database normalization, a secure and performant REST API for orders with split payments, and a responsive frontend cart module with dynamic tax calculations.

---

## 1. Data Analysis & Architecture Findings (Task 1)

Following an analysis of the raw restaurant assessment sheets (`Menu`, `Order History`, `Payments`), key structural challenges were addressed during the database redesign:

* In `Order History` combined item references and sizing into single text values (e.g., `1 Small`, `6 Large`). In this implementation, entities are normalized into separate tables (`items` and `item_variants`).

* Orders frequently split totals across payment methods (e.g., Order 11 split between Cash and Card). it implements a single order have many relationship between `orders` and `payments`.

* The schema accounts for discounts, tips, and refunded statuses (e.g., Order 15) to maintain balance between gross order totals and net settlements.

---

## 2. Database Schema Overview

The database (`resturant_db`) consists of normalized tables indexed for optimal query execution:

* `menus` — Primary menu groupings (Food, Drinks).
* `categories` — Category hierarchy linked to menus.
* `items` & `item_variants` — Product definitions and size-specific price tiers.
* `orders` & `order_items` — Master order records and itemized line items.
* `payments` — Individual transactions linked to orders by foreign key.

---

## 3. Local Installation & Setup

### Setup Steps

1. git clone https://github.com/sanjeevbahinipati15/Resturant-POS-Cart.git
   cd Resturant-POS-Cart