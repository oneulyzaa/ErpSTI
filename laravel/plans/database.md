Tabel Quotation
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
nomor_quotation,VARCHAR(50),NOT NULL,Primary Key. Nomor unik dokumen penawaran.
id_staff,INT / BIGINT,NOT NULL,Foreign Key (ke Staff). Pembuat penawaran.
id_client,INT / BIGINT,NOT NULL,Foreign Key (ke Customer). Klien yang dituju.
nama_project,VARCHAR(255),NOT NULL,Judul/nama proyek yang ditawarkan.
tanggal_pembuatan,DATE,NOT NULL,Tanggal dokumen penawaran dibuat.
valid_sampai,DATE,NOT NULL,Batas waktu berlakunya harga penawaran.
subtotal_produksi,"DECIMAL(15,2)",NOT NULL,Total nilai dari item produksi.
subtotal_material,"DECIMAL(15,2)",NOT NULL,Total nilai dari item material.
subtotal_labor,"DECIMAL(15,2)",NOT NULL,Total nilai jasa tenaga kerja.
subtotal_lainlain,"DECIMAL(15,2)",NOT NULL,Total nilai dari biaya tambahan lainnya.
grandtotal,"DECIMAL(15,2)",NOT NULL,Total akhir nilai penawaran.
diskon,"DECIMAL(15,2)",NOT NULL,Nominal potongan harga yang diberikan.
pajak,"DECIMAL(15,2)",NOT NULL,Nominal pajak yang dikenakan.
termin,TEXT,NULLABLE,Syarat termin pembayaran (misal: DP 50%).
keterangan,TEXT,NULLABLE,Catatan tambahan dokumen penawaran.
lampiran,VARCHAR(255),NULLABLE,Path/URL dokumen lampiran.
status,VARCHAR(50),NOT NULL,"Status dokumen"

Tabel Quotation_Item
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_item,INT / BIGINT,NOT NULL,Primary Key (Auto Increment). ID baris item.
nomor_quotation,VARCHAR(50),NOT NULL,Foreign Key (ke Quotation).
nama_item,VARCHAR(255),NOT NULL,Nama barang/jasa utama yang ditawarkan.
deskripsi_item,TEXT,NULLABLE,Spesifikasi teknis item tersebut.
jumlah_item,INT,NOT NULL,Kuantitas item yang ditawarkan.
satuan,VARCHAR(50),NOT NULL,"Satuan ukuran item (misal: Unit, Set)."
harga_item,"DECIMAL(15,2)",NOT NULL,Harga per satuan item utama.

Tabel Quotation_ItemMaterial
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_itemMaterial,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
id_item,INT / BIGINT,NOT NULL,Foreign Key (ke Quotation_Item).
id_material,INT / BIGINT,NOT NULL,Foreign Key (ke Material).
nama_material,VARCHAR(255),NOT NULL,Nama rincian material penyusun item.
satuan_material,VARCHAR(50),NOT NULL,Satuan ukuran rincian material.
jumlah_material,INT,NOT NULL,Kuantitas rincian material yang dibutuhkan.
harga_material,"DECIMAL(15,2)",NOT NULL,Harga per satuan material penyusun.

Tabel Quotation_Labor
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_labor,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_quotation,VARCHAR(50),NOT NULL,Foreign Key (ke Quotation).
nama_labor,VARCHAR(255),NOT NULL,Posisi/peran tenaga kerja.
jumlah_sdm,INT,NOT NULL,Jumlah orang yang dikerahkan.
jumlah_hari,INT,NOT NULL,Estimasi hari kerja.
rate_hari,"DECIMAL(15,2)",NOT NULL,Tarif per hari tenaga kerja.

Tabel Quotation_OtherCost
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_biaya,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_quotation,VARCHAR(50),NOT NULL,Foreign Key (ke Quotation).
nama_biaya,VARCHAR(255),NOT NULL,"Nama biaya tambahan (misal: Ongkir, Akomodasi)."
jumlah_biaya,"DECIMAL(15,2)",NOT NULL,Nominal biaya tambahan tersebut.

Tabel SalesOrder
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
nomor_salesorder,VARCHAR(50),NOT NULL,Primary Key. Nomor seri dokumen SO.
id_staff,INT / BIGINT,NOT NULL,Foreign Key (ke Staff). Staf pemroses SO.
id_client,INT / BIGINT,NOT NULL,Foreign Key (ke Customer). Klien pemesan.
nama_project,VARCHAR(255),NOT NULL,Nama proyek terkait pesanan.
referensi_po,VARCHAR(100),NULLABLE,Nomor PO dari pihak klien.
tanggal_pembuatan,DATE,NOT NULL,Tanggal SO disahkan.
subtotal_produksi,"DECIMAL(15,2)",NOT NULL,Total nilai dari item produksi di SO.
subtotal_material,"DECIMAL(15,2)",NOT NULL,Total nilai dari item material di SO.
subtotal_labor,"DECIMAL(15,2)",NOT NULL,Total nilai jasa tenaga kerja di SO.
subtotal_lainlain,"DECIMAL(15,2)",NOT NULL,Total nilai biaya tambahan di SO.
diskon,"DECIMAL(15,2)",NOT NULL,Potongan harga akhir SO.
pajak,"DECIMAL(15,2)",NOT NULL,Nominal pajak SO.
grandtotal,"DECIMAL(15,2)",NOT NULL,Total nilai kesepakatan akhir di SO.
termin,TEXT,NULLABLE,Syarat termin pembayaran yang disepakati.
status,VARCHAR(50),NOT NULL,"Status SO (misal: Open, Progress, Closed)."
lampiran,VARCHAR(255),NULLABLE,Path file PO/kontrak kerja pendukung.
keterangan,TEXT,NULLABLE,Catatan khusus SO.

Tabel SalesOrder_Item
Atribut	Tipe Data & Ukuran	Sifat	Keterangan
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_item,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_salesorder,VARCHAR(50),NOT NULL,Foreign Key (ke SalesOrder).
nama_item,VARCHAR(255),NOT NULL,Nama barang/jasa disepakati.
deskripsi_item,TEXT,NULLABLE,Spesifikasi final item.
jumlah_item,INT,NOT NULL,Kuantitas dipesan.
satuan,VARCHAR(50),NOT NULL,"Satuan pesanan (Unit, Box, dll)."
harga_item,"DECIMAL(15,2)",NOT NULL,Harga satuan yang disepakati di SO.

Tabel SalesOrder_ItemMaterial
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_itemMaterial,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
id_item,INT / BIGINT,NOT NULL,Foreign Key (ke SalesOrder_Item).
id_material,INT / BIGINT,NOT NULL,Foreign Key (ke Material).
nama_material,VARCHAR(255),NOT NULL,Nama rincian material di SO.
satuan_material,VARCHAR(50),NOT NULL,Satuan ukuran material.
jumlah_material,INT,NOT NULL,Kuantitas material untuk item ini di SO.
harga_material,"DECIMAL(15,2)",NOT NULL,Harga material acuan SO.

Tabel SalesOrder_Labor
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_labor,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_salesorder,VARCHAR(50),NOT NULL,Foreign Key (ke SalesOrder).
nama_labor,VARCHAR(255),NOT NULL,Peran tenaga kerja disepakati.
jumlah_sdm,INT,NOT NULL,Jumlah pekerja dialokasikan.
jumlah_hari,INT,NOT NULL,Durasi hari kerja.
rate_hari,"DECIMAL(15,2)",NOT NULL,Tarif harian disepakati di SO.

Tabel SalesOrder_OtherCost
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_biaya,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_salesorder,VARCHAR(50),NOT NULL,Foreign Key (ke SalesOrder).
nama_biaya,VARCHAR(255),NOT NULL,Komponen biaya lain di SO.
jumlah_biaya,"DECIMAL(15,2)",NOT NULL,Nominal biaya disepakati.

Tabel Produksi
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
nomor_produksi,VARCHAR(50),NOT NULL,Primary Key. Nomor seri instruksi produksi.
nomor_salesorder,VARCHAR(50),NOT NULL,Foreign Key (ke SalesOrder).
PIC,VARCHAR(255),NOT NULL,Penanggung jawab lapangan produksi.
tanggal_mulai,DATE,NOT NULL,Tanggal pengerjaan dimulai.
estimasi_selesai,DATE,NULLABLE,Perkiraan tanggal selesai produksi.
status_produksi,VARCHAR(50),NOT NULL,"Status saat ini"
keterangan,TEXT,NULLABLE,Laporan atau kendala produksi.

Tabel Produksi_Item
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_item,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_produksi,VARCHAR(50),NOT NULL,Foreign Key (ke Produksi).
nama_item,VARCHAR(255),NOT NULL,Nama item yang sedang diproduksi.
deskripsi_item,TEXT,NULLABLE,Panduan perakitan/produksi item.
jumlah_item,INT,NOT NULL,Target kuantitas produksi untuk item ini.
satuan,VARCHAR(50),NOT NULL,Satuan ukuran produksi.
harga_item,"DECIMAL(15,2)",NOT NULL,Harga Cost of Goods Manufactured (HPP) item.

Tabel Produksi_ItemMaterial
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_itemMaterial,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
id_item,INT / BIGINT,NOT NULL,Foreign Key (ke Produksi_Item).
id_material,INT / BIGINT,NOT NULL,Foreign Key (ke Material).
nama_material,VARCHAR(255),NOT NULL,Nama material yang diambil dari gudang.
satuan_material,VARCHAR(50),NOT NULL,Satuan ukur material yang dipakai.
jumlah_material,INT,NOT NULL,Kuantitas material aktual yang digunakan.
harga_material,"DECIMAL(15,2)",NOT NULL,Harga pokok material saat digunakan.

Tabel DeliveryOrder
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
nomor_deliveryorder,VARCHAR(50),NOT NULL,Primary Key. Nomor Surat Jalan / DO.
id_staff,INT / BIGINT,NOT NULL,Foreign Key (ke Staff). Pembuat DO.
id_client,INT / BIGINT,NOT NULL,Foreign Key (ke Customer). Tujuan DO.
nomor_salesorder,VARCHAR(50),NOT NULL,Foreign Key (ke SalesOrder). SO acuan pengiriman.
nama_project,VARCHAR(255),NOT NULL,Referensi proyek pengiriman.
referensi_po,VARCHAR(100),NULLABLE,Nomor PO terkait barang yang dikirim.
tanggal_pembuatan,DATE,NOT NULL,Tanggal dokumen cetak DO.
tanggal_pengiriman,DATE,NOT NULL,Tanggal fisik barang dibawa kurir/armada.
status,VARCHAR(50),NOT NULL,"Status pengiriman (Shipped, Delivered)."
keterangan,TEXT,NULLABLE,"Plat nomor armada, resi, atau instruksi supir."

Tabel DeliveryOrder_Item
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_item,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
nomor_deliveryorder,VARCHAR(50),NOT NULL,Foreign Key (ke DeliveryOrder).
nama_item,VARCHAR(255),NOT NULL,Nama barang fisik yang dikirim.
deskripsi_item,TEXT,NULLABLE,Keterangan kondisi barang saat dikirim.
jumlah_item,INT,NOT NULL,Kuantitas fisik yang dimasukkan ke armada.
satuan,VARCHAR(50),NOT NULL,"Satuan kemasan (misal: Koli, Pallet, Pcs)."
harga_item,"DECIMAL(15,2)",NOT NULL,Nilai barang untuk keperluan asuransi/DO.

Tabel DeliveryOrder_ItemMaterial
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_itemMaterial,INT / BIGINT,NOT NULL,Primary Key (Auto Increment).
id_item,INT / BIGINT,NOT NULL,Foreign Key (ke DeliveryOrder_Item).
id_material,INT / BIGINT,NOT NULL,Foreign Key (ke Material).
nama_material,VARCHAR(255),NOT NULL,Nama kelengkapan material bawaan dalam item.
satuan_material,VARCHAR(50),NOT NULL,Satuan kelengkapan material.
jumlah_material,INT,NOT NULL,Jumlah kelengkapan material tersebut.
harga_material,"DECIMAL(15,2)",NOT NULL,Harga kelengkapan tersebut.

Tabel Invoice
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
nomor_invoice,VARCHAR(50),NOT NULL,Primary Key. Nomor faktur tagihan resmi.
nomor_salesorder,VARCHAR(50),NOT NULL,Foreign Key (ke SalesOrder). SO acuan penagihan.
nama_project,VARCHAR(255),NOT NULL,Nama proyek yang ditagihkan.
referensi_po,VARCHAR(100),NULLABLE,Nomor PO untuk diletakkan pada cetakan Invoice.
tanggal_invoice,DATE,NOT NULL,Tanggal faktur diterbitkan.
jatuh_tempo,DATE,NOT NULL,Batas waktu bayar yang diberikan ke klien.
subtotal_produksi,"DECIMAL(15,2)",NOT NULL,Rincian total tagihan item produksi.
subtotal_material,"DECIMAL(15,2)",NOT NULL,Rincian total tagihan item material.
subtotal_labor,"DECIMAL(15,2)",NOT NULL,Rincian total tagihan jasa.
subtotal_lainlain,"DECIMAL(15,2)",NOT NULL,Rincian total tagihan biaya tambahan.
grandtotal,"DECIMAL(15,2)",NOT NULL,Total nilai yang wajib ditransfer/dibayar klien.
diskon,"DECIMAL(15,2)",NOT NULL,Potongan harga di tagihan.
pajak,"DECIMAL(15,2)",NOT NULL,PPN yang ditagihkan.
status_pembayaran,VARCHAR(50),NOT NULL,"Status lunas (Unpaid, Partial, Paid)."
keterangan,TEXT,NULLABLE,Rekening tujuan atau catatan denda keterlambatan.

Tabel Tanda_Terima_Pembayaran
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
nomor_receipt,VARCHAR(50),NOT NULL,Primary Key. Nomor kwitansi/bukti bayar.
nomor_invoice,VARCHAR(50),NOT NULL,Foreign Key (ke Invoice). Faktur yang dilunasi.
nama_project,VARCHAR(255),NOT NULL,Proyek acuan pembayaran.
referensi_po,VARCHAR(100),NULLABLE,Nomor PO acuan pembayaran.
tanggal_bayar,DATE,NOT NULL,Tanggal uang masuk ke rekening perusahaan.
metode_bayar,VARCHAR(50),NOT NULL,"Cara pembayaran (misal: Transfer BCA, Tunai)."
jumlah_bayar,"DECIMAL(15,2)",NOT NULL,Nominal bersih dana yang diterima.
keterangan,TEXT,NULLABLE,Catatan rekonsiliasi atau mutasi bank.

Tabel Customer
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id, UNIQUE, NOT NULL, AUTO INCREMENT
id_customer,CHAR(5),NOT NULL,Primary Key. ID unik data pelanggan.
nama_perusahaan,VARCHAR(255),NOT NULL,Nama resmi perusahaan pelanggan.
nama_kontak,VARCHAR(100),NOT NULL,Nama Person In Charge (PIC) perwakilan klien.
email_perusahaan,VARCHAR(100),NULLABLE,Alamat email resmi milik perusahaan klien.
alamat_perusahaan,TEXT,NOT NULL,Alamat lengkap domisili kantor klien.
alamat_faktur,TEXT,NULLABLE,Alamat fisik pengiriman dokumen tagihan/faktur.
alamat_efaktur,VARCHAR(100),NULLABLE,Alamat/Email khusus untuk keperluan e-faktur pajak.
telepon_faktur,VARCHAR(20),NULLABLE,Nomor telepon khusus bagian keuangan klien.
telepon_efaktur,VARCHAR(20),NULLABLE,Nomor telepon khusus kontak pajak/e-faktur klien.
rekening_perusahaan,VARCHAR(50),NULLABLE,Informasi nomor rekening bank milik klien.
npwp_perusahaan,VARCHAR(50),NULLABLE,Nomor Pokok Wajib Pajak (NPWP) klien.

Tabel Staff
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_user,INT / BIGINT,NOT NULL,Primary Key (Auto Increment). ID unik pengguna sistem.
username,VARCHAR(50),NOT NULL,Nama pengguna untuk keperluan login.
password,VARCHAR(255),NOT NULL,Kata sandi pengguna (terenkripsi/hash).
namalengkap,VARCHAR(255),NOT NULL,Nama lengkap staf atau pengguna.
email,VARCHAR(100),NOT NULL,Alamat email aktif staf.
telepon,VARCHAR(20),NULLABLE,Nomor kontak staf yang bisa dihubungi.
akses,VARCHAR(50),NOT NULL,"Hak akses sistem (misal: Admin, Sales, atau Direktur di mana kelola laporan hanya sebatas melihat dan mengunduh)."

Tabel Material
Nama Kolom,Tipe Data & Batas,Sifat,Keterangan
id_material,INT / BIGINT,NOT NULL,Primary Key (Auto Increment). ID unik material di gudang.
nama_material,VARCHAR(255),NOT NULL,Nama atau jenis material baku.
harga_material,"DECIMAL(15,2)",NOT NULL,Harga beli standar material per satuan.
status_material,VARCHAR(50),NOT NULL,"Status kelayakan material (misal: Tersedia, Habis)."
stok,INT,NOT NULL,Kuantitas material fisik dalam inventaris.
supplier,VARCHAR(255),NULLABLE,Nama vendor/pemasok material tersebut.
satuan,VARCHAR(50),NOT NULL,"Satuan ukuran material (misal: Kg, Meter, Pcs)."