<?php
// =====================================================
// Tugas 2 - Dashboard Nilai Mahasiswa
// =====================================================

// ---------- PENGATURAN (konstanta) ----------
const BOBOT = ['tugas' => 0.3, 'uts' => 0.3, 'uas' => 0.4];
const LABEL = ['tugas' => 'Tugas', 'uts' => 'UTS', 'uas' => 'UAS'];
const NILAI_LULUS = 65;

// Tabel grade: [batas bawah, grade, predikat]. Diurutkan dari tertinggi.
const TABEL_GRADE = [
    [85, 'A', 'Sangat Baik'],
    [75, 'B', 'Baik'],
    [65, 'C', 'Cukup'],
    [50, 'D', 'Kurang'],
    [0,  'E', 'Sangat Kurang'],
];

// Warna tiap grade untuk grafik
const WARNA_GRADE = ['A' => '#6D3FD6', 'B' => '#22A55A', 'C' => '#2F5FE3', 'D' => '#F28C1E', 'E' => '#E5483D'];

// Ikon (path SVG gaya garis)
const IKON = [
    'beranda' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
    'input'   => '<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>',
    'grafik'  => '<path d="M12 20V10M18 20V4M6 20v-4"/>',
    'piala'   => '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22M18 2H6v7a6 6 0 0 0 12 0V2Z"/>',
    'tabel'   => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>',
    'buku'    => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
    'topi'    => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
    'pengguna'=> '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    'medali'  => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>',
    'papan'   => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2M12 11h4M12 16h4M8 11h.01M8 16h.01"/>',
    'persen'  => '<path d="M19 5 5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
    'cetak'   => '<path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>',
];

// ---------- FUNCTION UTAMA ----------

function hitungNilaiAkhir(float $tugas, float $uts, float $uas): float
{
    $total = ($tugas * BOBOT['tugas']) + ($uts * BOBOT['uts']) + ($uas * BOBOT['uas']);
    return round($total, 2);
}

function tentukanGrade(float $nilaiAkhir): string
{
    foreach (TABEL_GRADE as [$batas, $grade]) {
        if ($nilaiAkhir >= $batas) {
            return $grade;
        }
    }
    return 'E';
}

function tentukanStatus(float $nilaiAkhir): string
{
    return $nilaiAkhir >= NILAI_LULUS ? 'Lulus' : 'Tidak lulus';
}

function tentukanPredikat(float $nilaiAkhir): string
{
    foreach (TABEL_GRADE as [$batas, , $predikat]) {
        if ($nilaiAkhir >= $batas) {
            return $predikat;
        }
    }
    return 'Sangat Kurang';
}

function nilaiValid(string $isi): bool
{
    return $isi !== '' && is_numeric($isi) && $isi >= 0 && $isi <= 100;
}

// Memproses satu baris isian: validasi, lalu hitung jika benar
function prosesBaris(array $baris): array
{
    $masalah = [];
    if ($baris['nama'] === '' || $baris['nim'] === '') {
        $masalah[] = 'Nama dan NIM wajib diisi.';
    }
    foreach (['tugas', 'uts', 'uas'] as $k) {
        if (!nilaiValid($baris[$k])) {
            $masalah[] = 'Nilai ' . LABEL[$k] . ' harus berupa angka 0 sampai 100.';
        }
    }

    if (empty($masalah)) {
        $akhir = hitungNilaiAkhir((float) $baris['tugas'], (float) $baris['uts'], (float) $baris['uas']);
        $baris['akhir']    = $akhir;
        $baris['grade']    = tentukanGrade($akhir);
        $baris['predikat'] = tentukanPredikat($akhir);
        $baris['status']   = tentukanStatus($akhir);
    }
    $baris['masalah'] = $masalah;
    return $baris;
}

// ---------- FUNCTION PEMBANTU TAMPILAN ----------

function koma(float $angka): string
{
    return str_replace('.', ',', (string) round($angka, 2));
}

function persen(float $bagian, float $total): float
{
    return $total > 0 ? round($bagian / $total * 100, 1) : 0.0;
}

function tampilPersen(float $angka): string
{
    return number_format($angka, 1, ',', '.') . '%';
}

function rata(array $data, string $kunci): float
{
    if (empty($data)) {
        return 0.0;
    }
    $jumlah = array_sum(array_map(fn($h) => (float) $h[$kunci], $data));
    return round($jumlah / count($data), 2);
}

function e(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}

function ikon(string $nama, int $ukuran = 22): string
{
    return '<svg width="' . $ukuran . '" height="' . $ukuran . '" viewBox="0 0 24 24" fill="none" '
         . 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" '
         . 'aria-hidden="true">' . IKON[$nama] . '</svg>';
}

// Membuat gradasi kerucut untuk grafik donat. $bagian = [[warna, jumlah], ...]
function donutCss(array $bagian): string
{
    $total = array_sum(array_column($bagian, 1));
    if ($total <= 0) {
        return '#E3E8F4';
    }
    $awal = 0;
    $stop = [];
    foreach ($bagian as [$warna, $jumlah]) {
        if ($jumlah <= 0) {
            continue;
        }
        $akhir  = $awal + ($jumlah / $total * 100);
        $stop[] = sprintf('%s %.3F%% %.3F%%', $warna, $awal, $akhir);
        $awal   = $akhir;
    }
    return 'conic-gradient(' . implode(', ', $stop) . ')';
}

// Teks rentang nilai tiap grade, contoh "75 - 84,99"
function rentangGrade(int $i): string
{
    $bawah = TABEL_GRADE[$i][0];
    if ($i === 0) {
        return $bawah . ' - 100';
    }
    return koma($bawah) . ' - ' . koma(TABEL_GRADE[$i - 1][0] - 0.01);
}

// ---------- PROSES FORM ----------
$sudahKirim = $_SERVER['REQUEST_METHOD'] === 'POST';
$masuk = [];
if ($sudahKirim) {
    $jumlahBaris = count($_POST['nama'] ?? []);
    for ($i = 0; $i < $jumlahBaris; $i++) {
        $baris = [];
        foreach (['nama', 'nim', 'tugas', 'uts', 'uas'] as $k) {
            $baris[$k] = trim($_POST[$k][$i] ?? '');
        }
        // Baris yang sama sekali kosong dilewati
        if (implode('', $baris) === '') {
            continue;
        }
        $masuk[] = $baris;
    }
}

// Kosong saat halaman pertama dibuka (belum ada input)
$hasil = array_map('prosesBaris', $masuk);

// Isi form: data yang dikirim, atau satu baris kosong
$barisForm = $hasil ?: [['nama' => '', 'nim' => '', 'tugas' => '', 'uts' => '', 'uas' => '']];
$valid = array_values(array_filter($hasil, fn($h) => empty($h['masalah'])));

// ---------- DATA UNTUK DASHBOARD ----------
$total = count($valid);
$lulus = count(array_filter($valid, fn($h) => $h['status'] === 'Lulus'));
$tidak = $total - $lulus;

$jumlahGrade = array_fill_keys(array_column(TABEL_GRADE, 1), 0);
foreach ($valid as $h) {
    $jumlahGrade[$h['grade']]++;
}

$rataAkhir = rata($valid, 'akhir');
$tertinggi = $total ? max(array_column($valid, 'akhir')) : 0;

$peringkat = $valid;
usort($peringkat, fn($a, $b) => $b['akhir'] <=> $a['akhir']);
$peringkat = array_slice($peringkat, 0, 5);

$batang = [
    ['Tugas',       rata($valid, 'tugas'), '#2F5FE3'],
    ['UTS',         rata($valid, 'uts'),   '#22A55A'],
    ['UAS',         rata($valid, 'uas'),   '#7B4DE0'],
    ['Nilai akhir', $rataAkhir,            '#F28C1E'],
];

$bagianGrade = [];
foreach ($jumlahGrade as $g => $n) {
    $bagianGrade[] = [WARNA_GRADE[$g], $n];
}
$cssDonutGrade = donutCss($bagianGrade);
$cssDonutLulus = donutCss([['#22A55A', $lulus], ['#E5483D', $tidak]]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Nilai Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0B1437;
            --navy-2: #16245C;
            --biru: #2F5FE3;
            --latar: #F3F6FD;
            --kertas: #FFFFFF;
            --teks: #131B3A;
            --abu: #6B7592;
            --garis: #E4E9F5;
            --hijau: #16934C;
            --merah: #D23B2F;
            --emas: #F5B82E;
            --font: "Poppins", "Segoe UI", system-ui, sans-serif;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: var(--font);
            font-size: 14px;
            line-height: 1.5;
            color: var(--teks);
            background: linear-gradient(135deg, #0A1330 0%, #14235A 100%);
            font-variant-numeric: tabular-nums;
        }
        :focus-visible { outline: 3px solid var(--emas); outline-offset: 2px; }

        .app {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            max-width: 1540px;
            margin: 0 auto;
            padding: 16px;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            position: sticky; top: 16px;
            height: calc(100vh - 32px);
            display: flex; flex-direction: column;
            color: #fff; padding: 10px 4px;
        }
        .logo { display: flex; align-items: center; gap: 12px; padding: 8px 12px 24px; }
        .logo svg { flex: none; }
        .logo b { display: block; font-size: 15px; letter-spacing: .02em; line-height: 1.15; }
        .logo span { display: block; font-size: 15px; font-weight: 700; color: #35A9FF; line-height: 1.15; }

        .menu { display: flex; flex-direction: column; gap: 4px; }
        .menu a {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 16px; border-radius: 12px;
            color: #D3DBF5; text-decoration: none; font-weight: 500;
        }
        .menu a:hover { background: rgba(255,255,255,.08); }
        .menu a.aktif { background: linear-gradient(90deg, #2F6BFF, #3F95FF); color: #fff; }

        .kutipan {
            margin-top: auto; padding: 22px 18px; text-align: center;
            border: 1px solid rgba(255,255,255,.14); border-radius: 18px;
            background: rgba(255,255,255,.05); font-weight: 500; font-size: 15px;
        }
        .kutipan svg { color: var(--emas); margin-bottom: 8px; }

        /* ---------- Panel utama ---------- */
        .panel {
            background: var(--latar);
            border-radius: 28px;
            padding: 26px;
            min-width: 0;
        }
        .kepala { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        h1 { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: .01em; display: inline; }
        .sub { color: var(--abu); margin-left: 10px; font-size: 15px; }
        .btn-cetak {
            display: inline-flex; align-items: center; gap: 8px;
            background: #5B3FD9; color: #fff; border: 0; border-radius: 12px;
            padding: 10px 16px; font: inherit; font-weight: 600; cursor: pointer;
        }
        .btn-cetak:hover { background: #4A2FC4; }

        /* ---------- Kartu statistik ---------- */
        .statistik { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 18px; }
        .stat {
            display: flex; align-items: center; gap: 14px;
            color: #fff; border-radius: 16px; padding: 18px;
            box-shadow: 0 10px 22px -12px rgba(11, 20, 55, .55);
        }
        .stat .lingkar {
            flex: none; width: 54px; height: 54px; border-radius: 50%;
            background: #fff; display: grid; place-items: center;
        }
        .stat small { display: block; font-size: 11px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; opacity: .95; }
        .stat strong { display: block; font-size: 28px; font-weight: 700; line-height: 1.15; }
        .stat span.ket { display: block; font-size: 11.5px; opacity: .92; }
        .s-biru   { background: linear-gradient(135deg, #3B73FF, #2447C9); } .s-biru .lingkar   { color: #2F5FE3; }
        .s-hijau  { background: linear-gradient(135deg, #2FC873, #16934C); } .s-hijau .lingkar  { color: #16934C; }
        .s-ungu   { background: linear-gradient(135deg, #8A5BEA, #5B2FC2); } .s-ungu .lingkar   { color: #6D3FD6; }
        .s-oranye { background: linear-gradient(135deg, #FFA93F, #EE7608); } .s-oranye .lingkar { color: #EE7608; }
        .s-teal   { background: linear-gradient(135deg, #1BC0D1, #0D88A3); } .s-teal .lingkar   { color: #0D88A3; }

        /* ---------- Kartu umum ---------- */
        .kartu {
            background: var(--kertas); border: 1px solid var(--garis);
            border-radius: 18px; padding: 22px; min-width: 0;
            box-shadow: 0 4px 14px -8px rgba(11, 20, 55, .25);
        }
        h2 { margin: 0 0 16px; font-size: 14px; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; }
        .kosong { color: var(--abu); margin: 0; }

        .baris-3 { display: grid; grid-template-columns: 1fr 1.25fr 1fr; gap: 16px; margin-bottom: 16px; }
        .baris-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; align-items: start; }
        .kolom-kanan { display: grid; gap: 16px; }
        .kartu-form { margin-bottom: 16px; }

        /* ---------- Form ---------- */
        .petunjuk { margin: -6px 0 14px; color: var(--abu); font-size: 13px; }
        .peringatan {
            background: #FDECEA; color: #B3261E; border-left: 4px solid var(--merah);
            border-radius: 10px; padding: 12px 16px; margin-bottom: 14px; font-size: 13.5px;
        }
        .peringatan ul { margin: 4px 0 0; padding-left: 18px; }
        .f-kepala, .f-baris { display: grid; grid-template-columns: 2fr 1.4fr 1fr 1fr 1fr 40px; gap: 10px; }
        .f-kepala { margin-bottom: 6px; }
        .f-kepala span { font-size: 11.5px; font-weight: 600; color: var(--abu); text-transform: uppercase; letter-spacing: .05em; }
        .f-baris { margin-bottom: 8px; }
        input {
            width: 100%; padding: 10px 12px; font: inherit; color: var(--teks);
            background: #fff; border: 1.5px solid #CDD6EC; border-radius: 10px; min-width: 0;
        }
        input:hover { border-color: var(--biru); }
        button { font: inherit; cursor: pointer; }
        .btn-x { border: 0; border-radius: 10px; background: #FDECEA; color: #B3261E; font-size: 18px; font-weight: 700; }
        .btn-x:hover { background: #F9D5D1; }
        .aksi { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .btn-utama { border: 0; border-radius: 10px; padding: 11px 22px; font-weight: 600; color: #fff; background: linear-gradient(90deg, #2F6BFF, #3F95FF); }
        .btn-utama:hover { filter: brightness(1.08); }
        .btn-tambah { border: 2px solid var(--biru); border-radius: 10px; padding: 9px 18px; font-weight: 600; color: var(--biru); background: #fff; }
        .btn-tambah:hover { background: #EEF3FF; }

        /* ---------- Donat ---------- */
        .donat-wadah { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .donat { position: relative; flex: none; width: 170px; aspect-ratio: 1; border-radius: 50%; }
        .donat::after { content: ""; position: absolute; inset: 27%; background: #fff; border-radius: 50%; }
        .donat-isi {
            position: absolute; inset: 0; z-index: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center; text-align: center; line-height: 1.1;
        }
        .donat-isi b { font-size: 24px; font-weight: 700; }
        .donat-isi small { font-size: 10.5px; font-weight: 600; letter-spacing: .06em; color: var(--abu); text-transform: uppercase; }
        .legenda { list-style: none; margin: 0; padding: 0; display: grid; gap: 9px; font-size: 12.5px; flex: 1; min-width: 150px; }
        .legenda li { display: grid; grid-template-columns: 12px 1fr; gap: 3px 9px; align-items: start; }
        .legenda i { width: 12px; height: 12px; border-radius: 50%; margin-top: 4px; }
        .legenda span { color: var(--abu); }
        .legenda strong { font-weight: 600; }

        /* ---------- Grafik batang ---------- */
        .grafik { display: grid; grid-template-columns: 34px 1fr; }
        .sumbu { position: relative; height: 210px; font-size: 11px; color: var(--abu); }
        .sumbu span { position: absolute; right: 8px; transform: translateY(-50%); }
        .plot {
            position: relative; height: 210px; display: flex; align-items: stretch; gap: 8%;
            padding: 0 6%;
            background-image: linear-gradient(var(--garis) 1px, transparent 1px);
            background-size: 100% 25%; border-bottom: 1px solid var(--garis);
        }
        .kolom { flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; }
        .batang {
            position: relative; width: 70%; max-width: 54px; border-radius: 8px 8px 0 0;
            transform-origin: bottom; animation: naik .8s cubic-bezier(.2, .7, .2, 1) both;
        }
        .batang b { position: absolute; left: 50%; bottom: 100%; transform: translateX(-50%); margin-bottom: 4px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .label-x { display: grid; grid-template-columns: repeat(4, 1fr); margin: 8px 0 0 34px; padding: 0 6%; text-align: center; font-size: 11.5px; color: var(--teks); gap: 8%; }

        /* ---------- Peringkat ---------- */
        .rank { list-style: none; margin: 0; padding: 0; }
        .rank li { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--garis); }
        .rank li:last-child { border-bottom: 0; }
        .no { flex: none; width: 26px; height: 26px; border-radius: 50%; display: grid; place-items: center; font-weight: 700; font-size: 12px; background: #EDF0F8; color: var(--abu); }
        .no-1 { background: var(--emas); color: #fff; }
        .no-2 { background: #A9B4CC; color: #fff; }
        .no-3 { background: #E2772E; color: #fff; }
        .rank .nm { flex: 1; font-weight: 600; color: var(--biru); }
        .rank .nl { font-weight: 700; color: var(--hijau); }

        /* ---------- Tabel ---------- */
        .gulir { overflow-x: auto; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: var(--navy); color: #fff; padding: 11px 12px; font-size: 11.5px; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
        th:first-child { border-radius: 10px 0 0 0; } th:last-child { border-radius: 0 10px 0 0; }
        td { padding: 11px 12px; text-align: center; border-bottom: 1px solid var(--garis); }
        tbody tr:nth-child(even) { background: #F6F8FD; }
        td.kiri { text-align: left; font-weight: 600; }
        .chip { display: inline-block; min-width: 28px; padding: 2px 8px; border-radius: 8px; font-weight: 700; color: #fff; }
        .pil { display: inline-block; padding: 2px 12px; border-radius: 999px; font-weight: 600; font-size: 12px; white-space: nowrap; }
        .pil-lulus { background: #E3F5EC; color: var(--hijau); }
        .pil-gagal { background: #FDECEA; color: var(--merah); }

        /* ---------- Aturan ---------- */
        .aturan { list-style: none; margin: 0 0 14px; padding: 0; display: grid; gap: 7px; font-size: 12.5px; }
        .aturan li { display: flex; align-items: center; gap: 10px; }
        .aturan .chip { min-width: 26px; text-align: center; font-size: 12px; }
        .aturan span { color: var(--abu); }
        .bobot { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
        .bobot span { background: #EEF3FF; color: var(--biru); font-weight: 600; font-size: 12px; padding: 4px 10px; border-radius: 8px; }
        .catatan { margin: 0; font-size: 12.5px; color: var(--abu); }

        .kaki { text-align: center; color: var(--abu); font-size: 12px; margin: 22px 0 0; }

        @keyframes naik { from { transform: scaleY(0); } to { transform: scaleY(1); } }
        @media (prefers-reduced-motion: reduce) { .batang { animation: none; } html { scroll-behavior: auto; } }

        /* ---------- Responsif ---------- */
        @media (max-width: 1240px) {
            .baris-3 { grid-template-columns: 1fr 1fr; }
            .baris-3 .kartu:last-child { grid-column: 1 / -1; }
            .baris-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 980px) {
            .app { grid-template-columns: 1fr; padding: 10px; gap: 12px; }
            .sidebar { position: static; height: auto; padding: 6px 4px 0; }
            .logo { padding-bottom: 12px; }
            .menu { flex-direction: row; overflow-x: auto; padding-bottom: 6px; }
            .menu a { white-space: nowrap; padding: 10px 14px; }
            .kutipan { display: none; }
            .panel { padding: 18px 14px; border-radius: 22px; }
        }
        @media (max-width: 700px) {
            .baris-3 { grid-template-columns: 1fr; }
            .f-kepala { display: none; }
            .f-baris { grid-template-columns: repeat(3, 1fr); }
            .f-baris input:nth-child(1), .f-baris input:nth-child(2) { grid-column: span 3; }
            .f-baris .btn-x { grid-column: span 3; height: 36px; font-size: 14px; }
            h1 { font-size: 22px; }
            .sub { display: block; margin: 2px 0 0; }
        }

        @media print {
            body { background: #fff; }
            .sidebar, .kartu-form, .btn-cetak { display: none; }
            .app { display: block; padding: 0; }
            .panel { background: #fff; padding: 0; }
        }
    </style>
</head>
<body>
<div class="app">

    <!-- ===== Sidebar ===== -->
    <aside class="sidebar">
        <div class="logo">
            <?= ikon('topi', 40) ?>
            <div><b>NILAI MAHASISWA</b><span>DASHBOARD</span></div>
        </div>

        <nav class="menu" id="menu">
            <a href="#ringkasan" class="aktif"><?= ikon('beranda', 20) ?>Overview</a>
            <a href="#input"><?= ikon('input', 20) ?>Input data</a>
            <a href="#grafik"><?= ikon('grafik', 20) ?>Grafik</a>
            <a href="#peringkat"><?= ikon('piala', 20) ?>Peringkat</a>
            <a href="#hasil"><?= ikon('tabel', 20) ?>Hasil</a>
            <a href="#aturan"><?= ikon('buku', 20) ?>Aturan nilai</a>
        </nav>

        <div class="kutipan">
            <?= ikon('piala', 38) ?>
            <div>&ldquo;Pendidikan adalah kunci sukses. Teruslah belajar, teruslah berkembang!&rdquo;</div>
        </div>
    </aside>

    <!-- ===== Panel utama ===== -->
    <main class="panel">

        <div class="kepala" id="ringkasan">
            <div><h1>OVERVIEW</h1><span class="sub">Ringkasan nilai mahasiswa</span></div>
            <button type="button" class="btn-cetak" onclick="window.print()"><?= ikon('cetak', 18) ?>Cetak</button>
        </div>

        <!-- Kartu statistik -->
        <section class="statistik" aria-label="Ringkasan statistik">
            <div class="stat s-biru">
                <div class="lingkar"><?= ikon('pengguna', 28) ?></div>
                <div><small>Total mahasiswa</small><strong><?= $total ?></strong><span class="ket">Data valid dihitung</span></div>
            </div>
            <div class="stat s-hijau">
                <div class="lingkar"><?= ikon('topi', 28) ?></div>
                <div><small>Lulus</small><strong><?= $lulus ?></strong><span class="ket"><?= tampilPersen(persen($lulus, $total)) ?> tingkat lulus</span></div>
            </div>
            <div class="stat s-ungu">
                <div class="lingkar"><?= ikon('medali', 28) ?></div>
                <div><small>Grade A</small><strong><?= $jumlahGrade['A'] ?></strong><span class="ket"><?= tampilPersen(persen($jumlahGrade['A'], $total)) ?> dari total</span></div>
            </div>
            <div class="stat s-oranye">
                <div class="lingkar"><?= ikon('papan', 28) ?></div>
                <div><small>Rata-rata nilai</small><strong><?= koma($rataAkhir) ?></strong><span class="ket">Tertinggi <?= koma((float) $tertinggi) ?></span></div>
            </div>
            <div class="stat s-teal">
                <div class="lingkar"><?= ikon('persen', 28) ?></div>
                <div><small>Tidak lulus</small><strong><?= $tidak ?></strong><span class="ket"><?= tampilPersen(persen($tidak, $total)) ?> dari total</span></div>
            </div>
        </section>

        <!-- Form input -->
        <section class="kartu kartu-form" id="input" aria-labelledby="j-input">
            <h2 id="j-input">Input data mahasiswa</h2>
            <p class="petunjuk">Isi nama, NIM, dan nilai (0 sampai 100), lalu klik &ldquo;Hitung nilai&rdquo;. Klik &ldquo;Tambah mahasiswa&rdquo; untuk memasukkan lebih dari satu orang.</p>

            <?php if ($sudahKirim && empty($masuk)): ?>
                <div class="peringatan" role="alert">Belum ada data yang diisi. Isi minimal satu mahasiswa.</div>
            <?php endif; ?>

            <?php $galat = array_filter($hasil, fn($h) => !empty($h['masalah'])); ?>
            <?php if (!empty($galat)): ?>
                <div class="peringatan" role="alert">
                    <b>Beberapa baris tidak dihitung:</b>
                    <ul>
                        <?php foreach ($galat as $urut => $h): ?>
                            <li>Baris <?= $urut + 1 ?>: <?= e(implode(' ', $h['masalah'])) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="f-kepala"><span>Nama</span><span>NIM</span><span>Tugas</span><span>UTS</span><span>UAS</span><span></span></div>
                <div id="daftarBaris">
                    <?php foreach ($barisForm as $d): ?>
                        <div class="f-baris">
                            <input type="text"   name="nama[]"  placeholder="Nama"  aria-label="Nama"  value="<?= e($d['nama']) ?>">
                            <input type="text"   name="nim[]"   placeholder="NIM"   aria-label="NIM"   value="<?= e($d['nim']) ?>">
                            <input type="number" name="tugas[]" placeholder="Tugas" aria-label="Nilai tugas" value="<?= e($d['tugas']) ?>" step="any" inputmode="decimal">
                            <input type="number" name="uts[]"   placeholder="UTS"   aria-label="Nilai UTS"   value="<?= e($d['uts']) ?>" step="any" inputmode="decimal">
                            <input type="number" name="uas[]"   placeholder="UAS"   aria-label="Nilai UAS"   value="<?= e($d['uas']) ?>" step="any" inputmode="decimal">
                            <button type="button" class="btn-x" onclick="hapusBaris(this)" aria-label="Hapus baris">&times;</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="aksi">
                    <button type="button" class="btn-tambah" onclick="tambahBaris()">+ Tambah mahasiswa</button>
                    <button type="submit" class="btn-utama">Hitung nilai</button>
                </div>
            </form>
        </section>

        <!-- Baris grafik -->
        <div class="baris-3" id="grafik">

            <section class="kartu" aria-labelledby="j-grade">
                <h2 id="j-grade">Ringkasan grade</h2>
                <div class="donat-wadah">
                    <div class="donat" style="background: <?= $cssDonutGrade ?>">
                        <div class="donat-isi"><b><?= $total ?></b><small>Total</small></div>
                    </div>
                    <ul class="legenda">
                        <?php foreach (TABEL_GRADE as $i => [$batas, $grade, $predikat]): ?>
                            <li>
                                <i style="background: <?= WARNA_GRADE[$grade] ?>"></i>
                                <div>
                                    <strong>Grade <?= $grade ?> (<?= rentangGrade($i) ?>)</strong><br>
                                    <span><?= $jumlahGrade[$grade] ?> (<?= tampilPersen(persen($jumlahGrade[$grade], $total)) ?>)</span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <section class="kartu" aria-labelledby="j-rata">
                <h2 id="j-rata">Rata-rata nilai per komponen</h2>
                <div class="grafik">
                    <div class="sumbu">
                        <?php foreach ([100, 75, 50, 25, 0] as $n): ?>
                            <span style="top: <?= 100 - $n ?>%"><?= $n ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="plot">
                        <?php foreach ($batang as [$nama, $nilai, $warna]): ?>
                            <div class="kolom">
                                <div class="batang" style="height: <?= $nilai ?>%; background: <?= $warna ?>">
                                    <b><?= koma($nilai) ?></b>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="label-x">
                    <?php foreach ($batang as [$nama]): ?><span><?= $nama ?></span><?php endforeach; ?>
                </div>
            </section>

            <section class="kartu" id="peringkat" aria-labelledby="j-top">
                <h2 id="j-top">Mahasiswa terbaik</h2>
                <?php if (empty($peringkat)): ?>
                    <p class="kosong">Belum ada data. Isi form lalu klik &ldquo;Hitung nilai&rdquo;.</p>
                <?php else: ?>
                    <ol class="rank">
                        <?php foreach ($peringkat as $urut => $h): ?>
                            <li>
                                <span class="no no-<?= $urut + 1 ?>"><?= $urut + 1 ?></span>
                                <span class="nm"><?= e($h['nama']) ?></span>
                                <span class="nl"><?= koma($h['akhir']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>
        </div>

        <!-- Baris tabel -->
        <div class="baris-2">
            <section class="kartu" id="hasil" aria-labelledby="j-hasil">
                <h2 id="j-hasil">Hasil perhitungan</h2>
                <div class="gulir">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th><th>Nama</th><th>NIM</th><th>Tugas</th><th>UTS</th><th>UAS</th>
                                <th>Nilai akhir</th><th>Grade</th><th>Predikat</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($valid)): ?>
                                <tr><td colspan="10">Belum ada data. Isi form lalu klik &ldquo;Hitung nilai&rdquo;.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($valid as $urut => $h): ?>
                                <tr>
                                    <td><?= $urut + 1 ?></td>
                                    <td class="kiri"><?= e($h['nama']) ?></td>
                                    <td><?= e($h['nim']) ?></td>
                                    <td><?= e($h['tugas']) ?></td>
                                    <td><?= e($h['uts']) ?></td>
                                    <td><?= e($h['uas']) ?></td>
                                    <td><b><?= koma($h['akhir']) ?></b></td>
                                    <td><span class="chip" style="background: <?= WARNA_GRADE[$h['grade']] ?>"><?= $h['grade'] ?></span></td>
                                    <td><?= $h['predikat'] ?></td>
                                    <td><span class="pil <?= $h['status'] === 'Lulus' ? 'pil-lulus' : 'pil-gagal' ?>"><?= $h['status'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="kolom-kanan">
                <section class="kartu" aria-labelledby="j-lulus">
                    <h2 id="j-lulus">Tingkat kelulusan</h2>
                    <div class="donat-wadah">
                        <div class="donat" style="background: <?= $cssDonutLulus ?>">
                            <div class="donat-isi"><b><?= tampilPersen(persen($lulus, $total)) ?></b><small>Lulus</small></div>
                        </div>
                        <ul class="legenda">
                            <li><i style="background:#22A55A"></i><div><strong>Lulus</strong><br><span><?= $lulus ?> mahasiswa</span></div></li>
                            <li><i style="background:#E5483D"></i><div><strong>Tidak lulus</strong><br><span><?= $tidak ?> mahasiswa</span></div></li>
                        </ul>
                    </div>
                </section>

                <section class="kartu" id="aturan" aria-labelledby="j-aturan">
                    <h2 id="j-aturan">Aturan penilaian</h2>
                    <div class="bobot">
                        <?php foreach (BOBOT as $k => $b): ?>
                            <span><?= LABEL[$k] ?> <?= $b * 100 ?>%</span>
                        <?php endforeach; ?>
                    </div>
                    <ul class="aturan">
                        <?php foreach (TABEL_GRADE as $i => [$batas, $grade, $predikat]): ?>
                            <li>
                                <span class="chip" style="background: <?= WARNA_GRADE[$grade] ?>"><?= $grade ?></span>
                                <b><?= rentangGrade($i) ?></b>
                                <span><?= $predikat ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="catatan">Lulus jika nilai akhir minimal <?= NILAI_LULUS ?>.</p>
                </section>
            </div>
        </div>

        <p class="kaki">Dihitung otomatis dengan PHP. Tugas 2 Pemrograman Web, D3 Manajemen Informatika, Universitas Lampung.</p>
    </main>
</div>

<script>
    // Menambah baris isian baru dengan menyalin baris pertama
    function tambahBaris() {
        const wadah = document.getElementById('daftarBaris');
        const salinan = wadah.firstElementChild.cloneNode(true);
        salinan.querySelectorAll('input').forEach(function (input) { input.value = ''; });
        wadah.appendChild(salinan);
        salinan.querySelector('input').focus();
    }

    // Menghapus baris, tetapi menyisakan minimal satu
    function hapusBaris(tombol) {
        const wadah = document.getElementById('daftarBaris');
        if (wadah.children.length > 1) {
            tombol.closest('.f-baris').remove();
        }
    }

    // Menandai menu yang sedang diklik
    document.querySelectorAll('#menu a').forEach(function (tautan) {
        tautan.addEventListener('click', function () {
            document.querySelectorAll('#menu a').forEach(function (a) { a.classList.remove('aktif'); });
            tautan.classList.add('aktif');
        });
    });
</script>

</body>
</html>
