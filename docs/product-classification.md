# Product Classification Rules

Reference for classifying `product` records (Category / UOM / Type / Parent Product) and
mapping the business's "Revenue MKM" product structure into the CRM.

## The three classification fields on `product`

| Field | Options | What it answers |
|---|---|---|
| `category_id` (Category) | Hardware, Software, Service | What *kind of thing* is this — a physical device, a software/content/license, or a labor/facility service? |
| `uom_id` (UOM) | License, Package, Unit, Subscription, Series | What's the natural unit this is counted/sold in? |
| `type` (Type) | Goods, Service, Subscription, Software | What's the billing/commercial nature — one-time sale, ongoing service, recurring billing, or a software asset? |

### Decision rules (in order of how we've been applying them)

**Category**
- **Hardware** — a physical device changes hands or is being rented out (STB unit itself, "Sewa STB", "Retail STB beli putus").
- **Software** — a certification/firmware/content-platform/license attached to hardware or delivered digitally (STB certifications, app/web access, content licensing deals).
- **Service** — a facility or labor service where nothing physical changes hands and the provider keeps operating the infrastructure (e.g. data center colocation — the client uses space/service, the servers stay at the facility).

**UOM**
- **License** — a certification/license tied to a specific unit or partner (e.g. "Google Certified" on an STB, a B2B content-licensing deal like KAI/Damri/IOH).
- **Package** — an umbrella/parent product line that groups several sellable variants underneath it (e.g. "Vision+ TV", "NextSys", intermediate grouping nodes).
- **Unit** — a countable physical item (STB hardware, data center rack/cabinet).
- **Subscription** — sold as one ongoing access grant, not tied to a specific device or license count (e.g. "Apps Only").
- **Series** — a named model/variant line, used sparingly (e.g. STB "Lite Series" firmware variant — though note Category there is Software, not Hardware, since it's firmware running on the same STB hardware).

**Type**
- **Goods** — one-time physical sale, no recurring relationship (retail STB sold outright / "beli putus", e-commerce marketplace hardware sales).
- **Subscription** — recurring or prepaid-term access to content/service, regardless of billing cadence (monthly recurring *and* annual-upfront "One Time (1 Tahun di Depan)" plans are both Subscription — the distinguishing factor is content/service access, not the payment schedule).
- **Service** — an ongoing labor/facility service being performed (reserved for cases where nothing above fits better; used sparingly so far).
- **Software** — rarely used as a Type in practice; Category already captures the software/hardware/service distinction, so Type on software items is usually still Subscription (the billing nature) rather than "Software" itself.

## Customer Type / Revenue Model (implemented)

The business's "Revenue MKM" BRD carries two more dimensions that cut across every
product line:

- **Customer Type** — B2B2C (ISP), B2C, Online-to-Offline, Hospitality, Internal, etc.
- **Revenue Model** — Recurring (Billing Bulanan), One Time (1 Tahun di Depan), Deal-based.

These are now real columns on `product` — `customer_type` and `revenue_model`
(migration `m260914_151934_add_customer_type_revenue_model_to_product_table`). Leaf
SKUs should carry both tags directly on themselves rather than relying only on
`parent_product_id` grouping nodes for this.

Both are plain `VARCHAR(50)` (not ENUM) — the set of values keeps growing as new
business lines show up (Online to Offline and Hospitality were both added after the
first classification pass), so a rigid ENUM would need a migration every time. The
create/edit Product form uses a Select2 in "tags" mode with a suggested list
(`Product::optsCustomerType()` / `Product::optsRevenueModel()`) but accepts free text.

Still worth noting: `parent_product_id` intermediate grouping nodes (e.g. "B2B2C ISP –
Recurring (Billing Bulanan)" as its own Product record purely for tree organization)
are still in use for the hierarchy itself — these two new fields don't replace that,
they just mean a leaf product no longer *has* to rely on its ancestor chain to know
its own customer type / revenue model.

The closest-but-different existing things in the app, for reference:
- `Account.account_type` (Prospect/Customer/Partner/Reseller/Vendor) — sales
  relationship stage, not customer segment/channel.
- `PriceList` (currently: "Retail Price", "Corporate Price", "Promotional Price") — a
  coarse pricing tier, not this taxonomy.

## Revenue MKM product hierarchy (as classified so far)

```
Vision+ TV (Package / Software / Subscription)
├── B2B2C ISP – Recurring (Billing Bulanan)          (Package / Software / Subscription)
│   ├── With STB                                      (Package / Software / Subscription)
│   │   ├── Google Certified                          (License / Software / Subscription)
│   │   ├── Lite Series                                (Series / Software / Subscription)  — firmware variant, same STB hardware
│   │   └── Google & Netflix Certified                 (License / Software / Subscription)
│   └── Apps Only                                      (Subscription / Software / Subscription)
├── B2B2C ISP – One Time (1 Tahun di Depan)          (Package / Software / Subscription)
│   ├── With STB                                      (Package / Software / Subscription)
│   │   ├── Google Certified / Lite Series / Google & Netflix Certified  (same as above)
│   ├── STB Only without recurring package             (Unit / Hardware / Goods)
│   └── Apps Only                                      (Subscription / Software / Subscription)
└── B2C – Retail STB (sales / beli putus)             (Unit / Hardware / Goods)
    ├── Via E-Commerce                                 (Unit / Hardware / Goods)
    │   ├── Shopee / Tokopedia / Blibli / TikTok Shop   (Unit / Hardware / Goods)
    └── Web Vision+ TV                                 (Unit / Hardware / Goods)  — own-site retail channel, parallel to Via E-Commerce

Multicast (Package / Software / Subscription)
└── B2B2C ISP – Recurring (Billing Bulanan)           (Package / Software / Subscription)

NextGO (Package / Software / Subscription)
└── Online to Offline – Recurring (Billing Bulanan)   (Package / Software / Subscription)
    ├── KAI                                            (License / Software / Subscription)
    └── Damri                                           (License / Software / Subscription)

NextSys (Package / Software / Subscription)
└── Hospitality – Recurring (Billing Bulanan)         (Package / Software / Subscription)
    ├── Sewa STB                                       (Unit / Hardware / Subscription)
    ├── NextSys Hospitality                             (Package / Software / Subscription)  — IPTV/entertainment solution for hotels
    ├── Vision+ / Multicast                             (Package / Software / Subscription)  — content component used within the hospitality package
    └── Combo Plus                                      (Package / Software / Subscription)  — bundled premium tier

IPTV IOH (License / Software / Subscription)          — B2B content-licensing deal with IOH, same pattern as KAI/Damri

ICT Business (Package / Service / Subscription)       — internal provisioning of software/internet/data center to MNC Group's own business units

Others
└── Data Center External                              (Unit / Service / Subscription)
    ├── Plaza MNC Group                                 (Unit / Service / Subscription)
    └── Sunter                                          (Unit / Service / Subscription)
```

Items marked with reasoning notes above were resolved by explicit user decision during
the classification session; anything not yet built in the `product` table as of this
writing is a plan, not a confirmed data state — re-check against `product` /
`parent_product_id` before assuming it exists.
