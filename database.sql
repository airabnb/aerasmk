-- Database: db_smk_bnb
-- Website SMK Bangun Nusa Bangsa

CREATE DATABASE IF NOT EXISTS `db_smk_bnb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_smk_bnb`;

-- Tabel Admin / Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'editor') DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Jurusan (Kompetensi Keahlian)
CREATE TABLE IF NOT EXISTS `jurusan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode` VARCHAR(20) NOT NULL,
  `nama` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(140) NOT NULL UNIQUE,
  `deskripsi_singkat` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `prospek_karir` TEXT NOT NULL,
  `kepala_jurusan` VARCHAR(100) DEFAULT NULL,
  `kuota` INT DEFAULT 72,
  `ikon` VARCHAR(50) DEFAULT 'cpu',
  `gambar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Artikel & Berita
CREATE TABLE IF NOT EXISTS `artikel` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `kategori` VARCHAR(60) NOT NULL DEFAULT 'Berita',
  `ringkasan` VARCHAR(300) NOT NULL,
  `konten` LONGTEXT NOT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `penulis` VARCHAR(100) DEFAULT 'Admin SMK BNB',
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Informasi & Pengumuman Sekolah
CREATE TABLE IF NOT EXISTS `informasi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(180) NOT NULL,
  `kategori` ENUM('Pengumuman', 'Agenda', 'PPDB', 'Prestasi', 'Umum') DEFAULT 'Pengumuman',
  `isi` TEXT NOT NULL,
  `tanggal_mulai` DATE DEFAULT NULL,
  `lokasi` VARCHAR(150) DEFAULT NULL,
  `is_pinned` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed Default Admin (password: admin123)
INSERT INTO `users` (`nama`, `username`, `password`, `role`) VALUES
('Administrator SMK BNB', 'admin', '$2y$10$Q04mhCW6u4xmiHOmr1ZL8.Z1aJ7BBV0laG5yQIeB.fgT2gTpE0aZa', 'admin')
ON DUPLICATE KEY UPDATE `nama` = VALUES(`nama`), `password` = VALUES(`password`);

-- Seed Jurusan
INSERT INTO `jurusan` (`kode`, `nama`, `slug`, `deskripsi_singkat`, `deskripsi`, `prospek_karir`, `kepala_jurusan`, `kuota`, `ikon`) VALUES
('PPLG', 'Pengembangan Perangkat Lunak & Gim', 'pengembangan-perangkat-lunak-dan-gim', 'Mempelajari pemrograman web, mobile apps, basis data, dan pengembangan gim modern.', 'Kompetensi Keahlian Pengembangan Perangkat Lunak dan Gim (PPLG) membekali peserta didik dengan keterampilan rekayasa perangkat lunak modern, mulai dari perancangan UI/UX, pemrograman web full-stack, aplikasi mobile Android/iOS, hingga manajemen basis data dan cloud deployment.', 'Full-Stack Web Developer, Mobile App Developer, UI/UX Designer, Software QA Engineer, Game Developer, Database Administrator', 'Budi Santoso, S.Kom., M.T.', 72, 'code'),
('TJKT', 'Teknik Jaringan Komputer & Telekomunikasi', 'teknik-jaringan-komputer-dan-telekomunikasi', 'Fokus pada infrastruktur jaringan, administrasi server, fiber optik, dan keamanan siber.', 'Kompetensi Keahlian TJKT mempersiapkan siswa menjadi tenaga ahli di bidang infrastruktur jaringan komputer, konfigurasi router & switch (MikroTik/Cisco), administrasi server Linux, instalasi fiber optik, serta dasar-dasar keamanan siber.', 'Network Engineer, System Administrator, Fiber Optic Technician, Cyber Security Analyst, IT Support Specialist', 'Andi Wijaya, S.T., Gr.', 72, 'network'),
('DKV', 'Desain Komunikasi Visual', 'desain-komunikasi-visual', 'Menguasai desain grafis, branding, fotografi, videografi, dan produksi konten kreatif.', 'Jurusan Desain Komunikasi Visual (DKV) menggabungkan kreativitas seni dan teknologi digital. Siswa mempelajari ilustrasi digital, desain identitas merek, tipografi, fotografi komersial, motion graphics, dan produksi video profesional.', 'Graphic Designer, Brand Strategist, Video Editor, Motion Designer, Social Media Creative, Illustrator', 'Siti Rahmawati, S.Sn.', 64, 'palette'),
('AKL', 'Akuntansi & Keuangan Lembaga', 'akuntansi-dan-keuangan-lembaga', 'Mengelola pembukuan digital, perpajakan, audit dasar, dan sistem informasi akuntansi.', 'Kompetensi Keahlian Akuntansi dan Keuangan Lembaga melatih ketelitian dan profesionalisme dalam penyusunan laporan keuangan, pengelolaan pajak berbasis aplikasi, akuntansi perusahaan dagang/jasa, serta penggunaan software akuntansi modern.', 'Junior Accountant, Tax Staff, Finance Officer, Payroll Administrator, Teller / Staf Perbankan', 'Dra. Hj. Ratna Dewi, M.M.', 72, 'calculator')
ON DUPLICATE KEY UPDATE `nama` = VALUES(`nama`);

-- Seed Artikel
INSERT INTO `artikel` (`judul`, `slug`, `kategori`, `ringkasan`, `konten`, `penulis`, `status`, `views`) VALUES
('SMK Bangun Nusa Bangsa Raih Juara 1 LKS Tingkat Provinsi Bidang Web Technologies', 'smk-bnb-raih-juara-1-lks-provinsi-web-technologies', 'Prestasi', 'Tim siswa jurusan PPLG berhasil menyabet medali emas pada ajang Lomba Kompetensi Siswa (LKS) tingkat provinsi tahun ini.', 'Prestasi membanggakan kembali ditorehkan oleh siswa SMK Bangun Nusa Bangsa. Pada ajang Lomba Kompetensi Siswa (LKS) tingkat provinsi yang digelar minggu lalu, delegasi dari jurusan Pengembangan Perangkat Lunak dan Gim (PPLG) berhasil meraih Juara 1 pada bidang lomba Web Technologies.\n\nDalam kompetisi selama dua hari tersebut, peserta ditantang untuk membangun aplikasi web full-stack yang responsif, aman, dan memiliki performa tinggi dalam waktu terbatas. Berkat persiapan intensif selama tiga bulan bersama guru pembimbing dan praktisi industri, karya siswa SMK Bangun Nusa Bangsa mendapat nilai tertinggi dari dewan juri.\n\nKepala Sekolah menyampaikan apresiasi setinggi-tingginya dan berharap prestasi ini menjadi pemacu semangat bagi seluruh siswa untuk terus berinovasi dan mengasah kompetensi menuju tingkat nasional.', 'Humas SMK BNB', 'published', 148),
('Kunjungan Industri Jurusan TJKT dan PPLG ke Pusat Data Nasional', 'kunjungan-industri-tjkt-dan-pplg-ke-pusat-data', 'Kegiatan', 'Sebanyak 120 siswa kelas XI mengikuti kunjungan industri untuk melihat langsung tata kelola server dan infrastruktur cloud.', 'Sebagai bagian dari kurikulum berbasis industri (Link and Match), SMK Bangun Nusa Bangsa menyelenggarakan kegiatan kunjungan industri bagi siswa kelas XI jurusan TJKT dan PPLG. Kegiatan ini bertujuan memberikan gambaran nyata mengenai lingkungan kerja profesional di sektor teknologi informasi.\n\nSelama kunjungan, para siswa mendapatkan pemaparan langsung mengenai arsitektur data center, standar keamanan jaringan, sistem pendinginan server, serta praktik DevOps yang diterapkan oleh tim engineer profesional.\n\nMelalui pengalaman ini, siswa diharapkan semakin memahami standar kompetensi yang dibutuhkan dunia usaha dan dunia industri (DUDI) sebelum memasuki program Praktik Kerja Lapangan (PKL).', 'Tim Kurikulum', 'published', 95),
('Workshop Desain Branding & Digital Marketing Bersama Praktisi Kreatif', 'workshop-desain-branding-dan-digital-marketing', 'Akademik', 'Jurusan DKV menggelar workshop intensif pembuatan identitas visual UMKM dan strategi konten digital.', 'Jurusan Desain Komunikasi Visual (DKV) SMK Bangun Nusa Bangsa bekerja sama dengan agensi kreatif lokal mengadakan workshop bertajuk "Membangun Identitas Visual yang Kuat di Era Digital".\n\nPara siswa diajarkan langkah demi langkah merancang logo, menentukan palet warna, menyusun brand guideline, hingga memproduksi konten visual untuk media sosial. Di akhir sesi, setiap kelompok mempresentasikan hasil karya rebranding untuk pelaku UMKM mitra sekolah.\n\nKegiatan ini melatih kemampuan komunikasi visual sekaligus menumbuhkan jiwa kewirausahaan kreatif di kalangan siswa.', 'Redaksi DKV', 'published', 76),
('Pembukaan Penerimaan Peserta Didik Baru (PPDB) Tahun Ajaran 2026/2027', 'pembukaan-ppdb-tahun-ajaran-2026-2027', 'Pengumuman', 'Pendaftaran siswa baru SMK Bangun Nusa Bangsa gelombang pertama resmi dibuka dengan berbagai jalur prestasi dan beasiswa.', 'SMK Bangun Nusa Bangsa secara resmi membuka Penerimaan Peserta Didik Baru (PPDB) untuk Tahun Ajaran 2026/2027. Calon peserta didik dapat memilih empat kompetensi keahlian unggulan: PPLG, TJKT, DKV, dan AKL.\n\nTersedia jalur Prestasi Akademik/Non-Akademik dengan potongan biaya pendidikan hingga 50%, serta jalur Reguler. Seluruh proses pendaftaran dapat dilakukan secara daring maupun datang langsung ke sekretariat PPDB di kampus utama SMK Bangun Nusa Bangsa.\n\nSegera daftarkan diri Anda karena kuota setiap kompetensi keahlian terbatas.', 'Panitia PPDB', 'published', 210)
ON DUPLICATE KEY UPDATE `judul` = VALUES(`judul`);

-- Seed Informasi
INSERT INTO `informasi` (`judul`, `kategori`, `isi`, `tanggal_mulai`, `lokasi`, `is_pinned`) VALUES
('Pendaftaran PPDB Gelombang 1 Dibuka', 'PPDB', 'Pendaftaran siswa baru gelombang pertama dibuka mulai 1 Desember hingga 28 Februari. Dapatkan bebas biaya formulir dan potongan uang gedung bagi 50 pendaftar pertama.', '2026-12-01', 'Kampus Utama & Online', 1),
('Jadwal Penilaian Akhir Semester (PAS) Ganjil', 'Agenda', 'Penilaian Akhir Semester Ganjil akan dilaksanakan serentak menggunakan sistem CBT (Computer Based Test). Siswa diwajibkan membawa kartu peserta ujian.', '2026-12-07', 'Ruang Lab & Kelas SMK BNB', 1),
('Sosialisasi Program Magang & Penyaluran Kerja BKK', 'Pengumuman', 'Bursa Kerja Khusus (BKK) SMK Bangun Nusa Bangsa mengundang siswa kelas XII untuk mengikuti pembekalan karier dan rekrutmen bersama 15 perusahaan mitra industri.', '2026-12-15', 'Aula Utama SMK BNB', 0),
('Bursa Beasiswa Prestasi Akademik & Tahfidz', 'Prestasi', 'Sekolah menyediakan beasiswa penuh dan parsial bagi siswa berprestasi di bidang akademik, olahraga, seni, serta penghafal Al-Qur''an minimal 3 juz.', '2026-11-20', 'Sekretariat Kesiswaan', 0);
