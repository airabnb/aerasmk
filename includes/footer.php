</main>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="brand" style="margin-bottom:16px;">
                    <div class="brand-logo" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <div class="brand-text">
                        <strong style="color:#fff;">SMK Bangun Nusa Bangsa</strong>
                        <span style="color:#93c5fd;">Vokasi Modern & Berkarakter</span>
                    </div>
                </div>
                <p style="font-size:.92rem;color:#94a3b8;max-width:340px;">
                    Mencetak lulusan kompeten, siap kerja di dunia industri, berjiwa wirausaha, serta berakhlak mulia menuju Indonesia Emas.
                </p>
            </div>

            <div>
                <h4>Menu Utama</h4>
                <ul class="footer-links">
                    <li><a href="<?= e(url('index.php')) ?>">Beranda</a></li>
                    <li><a href="<?= e(url('jurusan.php')) ?>">Kompetensi Keahlian</a></li>
                    <li><a href="<?= e(url('informasi.php')) ?>">Informasi & Pengumuman</a></li>
                    <li><a href="<?= e(url('artikel.php')) ?>">Artikel & Berita</a></li>
                </ul>
            </div>

            <div>
                <h4>Kompetensi Keahlian</h4>
                <ul class="footer-links">
                    <li><a href="<?= e(url('jurusan.php')) ?>">PPLG (Software & Gim)</a></li>
                    <li><a href="<?= e(url('jurusan.php')) ?>">TJKT (Jaringan & Telekomunikasi)</a></li>
                    <li><a href="<?= e(url('jurusan.php')) ?>">DKV (Desain Komunikasi Visual)</a></li>
                    <li><a href="<?= e(url('jurusan.php')) ?>">AKL (Akuntansi & Keuangan)</a></li>
                </ul>
            </div>

            <div>
                <h4>Kontak Sekolah</h4>
                <ul class="footer-links" style="color:#94a3b8;">
                    <li>Jl. Pendidikan Raya No. 88, Nusantara</li>
                    <li>Telp: (021) 8899-4567</li>
                    <li>Email: info@smkbangunnusabangsa.sch.id</li>
                    <li>Senin - Jumat: 07.00 - 15.30 WIB</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> SMK Bangun Nusa Bangsa. Seluruh hak cipta dilindungi.</span>
            <span>Dibangun dengan PHP &amp; MySQL</span>
        </div>
    </div>
</footer>
<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
