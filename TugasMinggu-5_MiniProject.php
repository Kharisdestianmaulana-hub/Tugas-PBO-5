<?php
/**
 * TUGAS RUMAH MINGGU 5 — MINI PROJECT ADVANCED OOP
 * Mata Kuliah: Pemrograman Berorientasi Objek (TI033015)
 * Dosen: Hilman Lesmana, M.S.F.
 * 
 * NAMA: Kharis Destian Maulana
 * NIM : 20251020018
 */

// 1. Interface: Kontrak kemampuan meminjam
interface BisaDipinjam {
    public function pinjam(string $peminjam, string $keperluan): void;
    public function kembalikan(): void;
    public function isTersedia(): bool;
    public function getNamaRuangan(): string;
}

// 2. Abstract Parent Class: Ruangan
abstract class Ruangan implements BisaDipinjam {
    protected string $kodeRuangan;
    protected string $namaRuangan;
    protected int $kapasitas;
    protected string $status;
    protected ?string $peminjamSaatIni = null;
    protected ?string $keperluan = null;

    public function __construct(string $kodeRuangan, string $namaRuangan, int $kapasitas) {
        if ($kapasitas <= 0) {
            throw new InvalidArgumentException("Kapasitas ruangan '{$namaRuangan}' harus lebih dari 0.");
        }
        $this->kodeRuangan = $kodeRuangan;
        $this->namaRuangan = $namaRuangan;
        $this->kapasitas   = $kapasitas;
        $this->status      = "Tersedia";
    }

    public function getKodeRuangan(): string {
        return $this->kodeRuangan;
    }

    public function getNamaRuangan(): string {
        return $this->namaRuangan;
    }

    public function getKapasitas(): int {
        return $this->kapasitas;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function isTersedia(): bool {
        return $this->status === "Tersedia";
    }

    public function pinjam(string $peminjam, string $keperluan): void {
        if (!$this->isTersedia()) {
            throw new RuntimeException("Ruangan '{$this->namaRuangan}' sedang tidak tersedia (sudah digunakan).");
        }
        $this->status = "Digunakan";
        $this->peminjamSaatIni = $peminjam;
        $this->keperluan = $keperluan;
    }

    public function kembalikan(): void {
        if ($this->status !== "Digunakan") {
            throw new RuntimeException("Ruangan '{$this->namaRuangan}' belum dipinjam.");
        }
        $this->status = "Tersedia";
        $this->peminjamSaatIni = null;
        $this->keperluan = null;
    }

    // Abstract methods wajib diimplementasikan oleh subclass
    abstract public function getTipeRuangan(): string;
    abstract public function getFasilitasKhusus(): string;
    abstract public function hitungBiayaOperasional(int $durasiJam): float;
}

// 3. Subclass: RuanganTeori
class RuanganTeori extends Ruangan {
    private bool $memilikiProyektor;

    public function __construct(string $kode, string $nama, int $kapasitas, bool $memilikiProyektor = true) {
        parent::__construct($kode, $nama, $kapasitas);
        $this->memilikiProyektor = $memilikiProyektor;
    }

    public function getTipeRuangan(): string {
        return "Kelas Teori";
    }

    public function getFasilitasKhusus(): string {
        $proyektor = $this->memilikiProyektor ? "LCD Proyektor & Layar" : "Tanpa Proyektor";
        return "AC, Whiteboard, Meja-Kursi, {$proyektor}";
    }

    public function hitungBiayaOperasional(int $durasiJam): float {
        return $durasiJam * 25000.0;
    }
}

// 4. Subclass: RuanganLaboratorium
class RuanganLaboratorium extends Ruangan {
    private int $jumlahPC;
    private string $spesifikasi;

    public function __construct(string $kode, string $nama, int $kapasitas, int $jumlahPC, string $spesifikasi) {
        parent::__construct($kode, $nama, $kapasitas);
        $this->jumlahPC = $jumlahPC;
        $this->spesifikasi = $spesifikasi;
    }

    public function getTipeRuangan(): string {
        return "Laboratorium";
    }

    public function getFasilitasKhusus(): string {
        return "{$this->jumlahPC} Unit PC ({$this->spesifikasi}), LAN, Dual AC";
    }

    public function hitungBiayaOperasional(int $durasiJam): float {
        $tarifPerJam = 80000.0;
        $biayaMaintenance = 20000.0;
        return ($durasiJam * $tarifPerJam) + $biayaMaintenance;
    }
}

// 5. Subclass: RuanganAuditorium
class RuanganAuditorium extends Ruangan {
    private bool $memilikiPanggung;

    public function __construct(string $kode, string $nama, int $kapasitas, bool $memilikiPanggung = true) {
        parent::__construct($kode, $nama, $kapasitas);
        $this->memilikiPanggung = $memilikiPanggung;
    }

    public function getTipeRuangan(): string {
        return "Auditorium";
    }

    public function getFasilitasKhusus(): string {
        $panggung = $this->memilikiPanggung ? "Panggung Utama & Lighting" : "Standard Floor";
        return "Sound System, 4x Mic Wireless, {$panggung}";
    }

    public function hitungBiayaOperasional(int $durasiJam): float {
        return $durasiJam * 150000.0;
    }
}

// 6. Entitas Mahasiswa & Petugas
class Mahasiswa {
    private string $nim;
    private string $nama;
    private string $prodi;

    public function __construct(string $nim, string $nama, string $prodi) {
        $this->nim   = $nim;
        $this->nama  = $nama;
        $this->prodi = $prodi;
    }

    public function getNim(): string {
        return $this->nim;
    }

    public function getNama(): string {
        return $this->nama;
    }

    public function getProdi(): string {
        return $this->prodi;
    }
}

class Petugas {
    private string $idPetugas;
    private string $nama;
    private string $jabatan;

    public function __construct(string $idPetugas, string $nama, string $jabatan) {
        $this->idPetugas = $idPetugas;
        $this->nama      = $nama;
        $this->jabatan   = $jabatan;
    }

    public function getNama(): string {
        return $this->nama;
    }

    public function getJabatan(): string {
        return $this->jabatan;
    }
}

// 7. Composition: Class Peminjaman (has-a Mahasiswa & Ruangan)
class Peminjaman {
    private string $idPeminjaman;
    private Mahasiswa $mahasiswa;
    /** @var Ruangan[] */
    private array $daftarRuangan = [];
    private string $tanggal;
    private int $durasiJam;
    private string $keperluan;
    private string $status;
    private ?string $catatanPenolakan = null;
    private ?Petugas $verifikator = null;

    public function __construct(string $idPeminjaman, Mahasiswa $mahasiswa, string $tanggal, int $durasiJam, string $keperluan) {
        if ($durasiJam <= 0) {
            throw new InvalidArgumentException("Durasi peminjaman harus minimal 1 jam.");
        }
        $this->idPeminjaman = $idPeminjaman;
        $this->mahasiswa     = $mahasiswa;
        $this->tanggal       = $tanggal;
        $this->durasiJam     = $durasiJam;
        $this->keperluan     = $keperluan;
        $this->status        = "Menunggu";
    }

    public function tambahRuangan(Ruangan $ruangan): void {
        $this->daftarRuangan[] = $ruangan;
    }

    public function prosesPersetujuan(Petugas $petugas, bool $setujui): void {
        $this->verifikator = $petugas;

        if (!$setujui) {
            $this->status = "Ditolak";
            $this->catatanPenolakan = "Ditolak oleh petugas.";
            return;
        }

        foreach ($this->daftarRuangan as $ruangan) {
            if (!$ruangan->isTersedia()) {
                $this->status = "Ditolak";
                $this->catatanPenolakan = "Ruangan '{$ruangan->getNamaRuangan()}' sedang digunakan pada jadwal tersebut.";
                return;
            }
        }

        foreach ($this->daftarRuangan as $ruangan) {
            $ruangan->pinjam($this->mahasiswa->getNama(), $this->keperluan);
        }
        $this->status = "Disetujui";
    }

    public function hitungTotalBiaya(): float {
        $total = 0.0;
        foreach ($this->daftarRuangan as $ruangan) {
            $total += $ruangan->hitungBiayaOperasional($this->durasiJam);
        }
        return $total;
    }

    public function cetakInfoPeminjaman(): void {
        echo "ID Peminjaman : {$this->idPeminjaman}\n";
        echo "Peminjam      : {$this->mahasiswa->getNama()} ({$this->mahasiswa->getNim()}) - {$this->mahasiswa->getProdi()}\n";
        echo "Jadwal Pinjam : {$this->tanggal} ({$this->durasiJam} Jam)\n";
        echo "Keperluan     : {$this->keperluan}\n";
        if ($this->verifikator) {
            echo "Verifikator   : {$this->verifikator->getNama()} ({$this->verifikator->getJabatan()})\n";
        }
        echo "Status        : {$this->status}\n";
        if ($this->catatanPenolakan) {
            echo "Keterangan    : {$this->catatanPenolakan}\n";
        }
        echo "Ruangan yang Dipinjam:\n";
        foreach ($this->daftarRuangan as $idx => $ruang) {
            $no = $idx + 1;
            $biaya = number_format($ruang->hitungBiayaOperasional($this->durasiJam), 0, ',', '.');
            echo "  {$no}. [{$ruang->getKodeRuangan()}] {$ruang->getNamaRuangan()} ({$ruang->getTipeRuangan()}) - Status: {$ruang->getStatus()}\n";
            echo "     Fasilitas: {$ruang->getFasilitasKhusus()}\n";
            echo "     Estimasi Biaya: Rp {$biaya}\n";
        }
        echo "Total Biaya Operasional: Rp " . number_format($this->hitungTotalBiaya(), 0, ',', '.') . "\n";
    }
}

// 8. Fungsi Polymorphism
function tampilkanKatalog(Ruangan $ruangan, int $durasiJam): void {
    $biaya = number_format($ruangan->hitungBiayaOperasional($durasiJam), 0, ',', '.');
    echo "- [{$ruangan->getKodeRuangan()}] {$ruangan->getNamaRuangan()} ({$ruangan->getTipeRuangan()})\n";
    echo "  Kapasitas: {$ruangan->getKapasitas()} orang | Status: {$ruangan->getStatus()}\n";
    echo "  Fasilitas: {$ruangan->getFasilitasKhusus()}\n";
    echo "  Biaya ({$durasiJam} jam): Rp {$biaya}\n\n";
}

// ==========================================
// PROGRAM UTAMA / PENGUJIAN
// ==========================================
echo "=== SISTEM PEMINJAMAN RUANGAN (ADVANCED OOP) ===\n\n";

// Inisialisasi Objek Ruangan
$ruang1 = new RuanganTeori("R-201", "Ruang Kuliah Teori 201", 45, true);
$ruang2 = new RuanganLaboratorium("LAB-01", "Lab Software Engineering", 35, 35, "Core i7 / 16GB RAM");
$ruang3 = new RuanganAuditorium("AUD-01", "Auditorium Kampus", 300, true);

// 1. Uji Polymorphism
echo "--- 1. DAFTAR RUANGAN KAMPUS (POLYMORPHISM) ---\n";
$daftar = [$ruang1, $ruang2, $ruang3];
foreach ($daftar as $ruang) {
    tampilkanKatalog($ruang, 4);
}

// Inisialisasi Mahasiswa & Petugas
$mhs1    = new Mahasiswa("20251020018", "Kharis Destian Maulana", "Teknik Informatika");
$mhs2    = new Mahasiswa("20251020099", "Budi Santoso", "Sistem Informasi");
$petugas = new Petugas("PET-01", "Pak Dani", "Staf Sarpras");

// 2. Transaksi 1: Peminjaman Disetujui (Composition)
echo "--- 2. PROSES PEMINJAMAN 1 (SUKSES) ---\n";
$pinjam1 = new Peminjaman("PJ-001", $mhs1, "25 Oktober 2026", 4, "Workshop Himpunan Mahasiswa TI");
$pinjam1->tambahRuangan($ruang2);
$pinjam1->tambahRuangan($ruang1);
$pinjam1->prosesPersetujuan($petugas, true);
$pinjam1->cetakInfoPeminjaman();
echo "\n";

// 3. Transaksi 2: Peminjaman Bentrok (Aturan Bisnis)
echo "--- 3. PROSES PEMINJAMAN 2 (BENTROK JADWAL) ---\n";
$pinjam2 = new Peminjaman("PJ-002", $mhs2, "25 Oktober 2026", 2, "Rapat Kerja BEM");
$pinjam2->tambahRuangan($ruang2); // Lab sedang dipakai Kharis
$pinjam2->prosesPersetujuan($petugas, true);
$pinjam2->cetakInfoPeminjaman();
echo "\n";

// 4. Uji Exception & Validasi
echo "--- 4. PENGUJIAN VALIDASI & EXCEPTION ---\n";
try {
    echo "[Uji 1] Input kapasitas negatif (-10):\n";
    $ruangInvalid = new RuanganTeori("R-ERR", "Ruang Salah", -10);
} catch (InvalidArgumentException $e) {
    echo "Pesan Error: " . $e->getMessage() . "\n\n";
}

try {
    echo "[Uji 2] Pinjam langsung ruangan yang sedang digunakan:\n";
    $ruang2->pinjam("Anonim", "Kegiatan");
} catch (RuntimeException $e) {
    echo "Pesan Error: " . $e->getMessage() . "\n";
}
