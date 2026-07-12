Saya perlu refactor kode setiap model berdasarkan migration. Berikut adalah daftar migration sebagai berikut:

[text](../database/migrations/0001_01_01_000000_create_users_table.php) ✅(sudah)
[text](../database/migrations/2026_01_01_000001_create_master_tables.php) ✅(sudah)
[text](../database/migrations/2026_01_01_000002_create_quotations_tables.php) ✅(sudah)
[text](../database/migrations/2026_01_01_000003_create_sales_orders_tables.php) ❌(belum)
[text](../database/migrations/2026_01_01_000004_create_productions_tables.php) ❌(belum)
[text](../database/migrations/2026_01_01_000005_create_delivery_orders_tables.php) ❌(belum)
[text](../database/migrations/2026_01_01_000006_create_invoices_table.php) ❌(belum)
[text](../database/migrations/2026_01_01_000007_create_receipts_table.php) ❌(belum)

Target Model (hasil akhir yang diinginkan)

| # | Nama migration | Model yang sudah ada | Status |
|---|---|---|---|
| 1 | `create_users_table.php` | `User.php` | ✅ sudah dilakukan penyesuaian |
| 2 | `create_master_tables.php` | `ClientModel.php`, `AssetModel.php` | ✅ sudah dilakukan penyesuaian |
| 3 | `create_quotations_tables.php` | `Quotation.php`, `QuotationItem.php`, `QuotationItemMaterial.php`, `QuotationLabor.php`, `QuotationOtherCost.php` | ✅ sudah dilakukan penyesuaian |
| 4 | `create_sales_orders_tables.php` | `SalesOrder.php`, `SalesOrderItem.php`, `SalesOrderItemMaterial.php`, `SalesOrderLabor.php`, `SalesOrderOtherCost.php` | ❌ Belum|
| 5 | `create_productions_tables.php` | `Production.php`, `ProductionItem.php`, `ProductionItemMaterial.php` | ❌ Belum |
| 6 | `create_delivery_orders_tables.php` | `DeliveryOrder.php`, `DeliveryOrderItem.php`, `DeliveryOrderItemMaterial.php` | ❌ Belum |
| 7 | `create_invoices_table.php` | `Invoice.php` | ❌ Belum |
| 8 | `create_receipts_table.php` | `Receipt.php` | ❌ Belum |

## Instruksi untuk AI Agent

Kerjakan **hanya migration #4 sampai #8** (yang statusnya ❌ Belum), satu per satu, jangan lompat, dan jangan sentuh ulang model dari migration #1–#3 yang sudah ✅ kecuali ada relasi yang perlu ditambahkan di model tersebut karena keterkaitan dengan tabel baru (misalnya `SalesOrder` perlu `hasMany(DeliveryOrder::class)` setelah delivery orders dibuat — itu boleh diedit, tapi jangan ubah struktur field yang sudah ada).

Untuk setiap migration yang dikerjakan, lakukan urutan berikut:

1. **Baca isi migration** — catat semua tabel yang dibuat di file itu (biasanya satu migration bisa berisi beberapa tabel sekaligus, contoh: `create_productions_tables.php` kemungkinan membuat tabel `productions`, `production_items`, `production_materials`).
2. **Cocokkan setiap tabel dengan model yang sudah ada** di kolom "Model yang sudah ada" pada tabel di atas. Buka file model tersebut, cek apakah sudah ada isinya atau masih kosong/default.
3. **Sesuaikan model** dengan:
   - `protected $table` bila nama tabel tidak sesuai konvensi plural Laravel dari nama class
   - `protected $fillable` — semua kolom yang bisa diisi mass-assignment
   - `protected $casts` — untuk kolom `decimal`, `date`, `datetime`, `boolean`, `json`, `enum`
   - `use SoftDeletes;` jika migration memakai `softDeletes()`
   - Relasi Eloquent sesuai foreign key:
     - `belongsTo()` di model anak untuk setiap `foreignId(...)->constrained(...)`
     - `hasMany()` / `hasOne()` di model induk sebagai kebalikannya
   - Method relasi diberi return type hint (`: BelongsTo`, `: HasMany`, dst.) sesuai standar Laravel
4. **Cross-check relasi ke model migration sebelumnya (#1–#3)** yang sudah selesai, contoh:
   - `Production` kemungkinan berelasi ke `SalesOrder` (foreign key `sales_order_id`) → tambahkan `belongsTo(SalesOrder::class)` di `Production`, dan `hasMany(Production::class)` di `SalesOrder` jika belum ada.
   - `DeliveryOrder` kemungkinan berelasi ke `SalesOrder` dan/atau `Production`.
   - `Invoice` kemungkinan berelasi ke `SalesOrder`, `Client`/`Customer`.
   - `Receipt` kemungkinan berelasi ke `Invoice`.
   - Jika foreign key yang ditemukan tidak sesuai tebakan di atas, ikuti apa yang tertulis di migration, bukan tebakan ini.
5. Setelah satu migration selesai (semua model terkait sudah disesuaikan), **update status di file plan ini** dari ❌ Belum menjadi ✅ sudah dilakukan penyesuaian, baik di daftar link maupun di tabel, sebelum lanjut ke migration berikutnya.
6. Setelah migration #8 selesai, lakukan **review akhir**: pastikan semua relasi antar model (dari #1 sampai #8) bisa dipanggil dua arah tanpa error, dan laporkan ringkasan model apa saja yang diubah.

## Catatan
- Jangan mengubah struktur migration, hanya model.
- Jika ada ambiguitas nama kolom/relasi yang tidak jelas dari migration, tanyakan ke user, jangan menebak.
- Ikuti PSR-12 dan gaya penulisan Laravel standar.