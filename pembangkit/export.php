<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

require 'vendor/autoload.php'; // Load PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

try {
    require_once 'koneksi.php';
    $db = new Database();
    $conn = $db->getConnection();

    if ($role === 'admin'||$role === 'superadmin') {
        $stmt = $conn->prepare("SELECT * FROM pembangkit");
    } else {
        $stmt = $conn->prepare("SELECT * FROM pembangkit WHERE id_user = :id_user");
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul
    $sheet->setCellValue('A1', 'DATA PEMBANGKIT LIST');
    $sheet->mergeCells('A1:O1'); // Gabungkan sel untuk judul
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Header
    $headers = [
        'No.', 'Nama Perusahaan', 'Alamat', 'Longitude', 'Latitude', 'Jenis Pembangkit', 'Fungsi',
        'Kapasitas Terpasang (MW)', 'Daya Mampu Netto (MW)', 'Jumlah Unit', 'No. Unit',
        'Tahun Operasi', 'Status Operasi', 'Jenis Bahan Bakar', 'Satuan Bahan Bakar'
    ];
    
    $column = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($column . '3', $header);
        $column++;
    }

    // Style Header
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A3:O3')->applyFromArray($headerStyle);

    // Isi Data
    $rowNum = 4;
    $no = 1;
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rowNum, $no++);
        $sheet->setCellValue('B' . $rowNum, $row['nama_perusahaan']);
        $sheet->setCellValue('C' . $rowNum, $row['alamat']);
        $sheet->setCellValue('D' . $rowNum, $row['longitude']);
        $sheet->setCellValue('E' . $rowNum, $row['latitude']);
        $sheet->setCellValue('F' . $rowNum, $row['jenis_pembangkit']);
        $sheet->setCellValue('G' . $rowNum, $row['fungsi']);
        $sheet->setCellValue('H' . $rowNum, $row['kapasitas_terpasang']);
        $sheet->setCellValue('I' . $rowNum, $row['daya_mampu_netto']);
        $sheet->setCellValue('J' . $rowNum, $row['jumlah_unit']);
        $sheet->setCellValue('K' . $rowNum, $row['no_unit']);
        $sheet->setCellValue('L' . $rowNum, $row['tahun_operasi']);
        $sheet->setCellValue('M' . $rowNum, $row['status_operasi']);
        $sheet->setCellValue('N' . $rowNum, $row['bahan_bakar_jenis']);
        $sheet->setCellValue('O' . $rowNum, $row['bahan_bakar_satuan']);

        $rowNum++;
    }

    // Border untuk seluruh data
    $sheet->getStyle('A3:O' . ($rowNum - 1))->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);

    // Auto-fit kolom
    foreach (range('A', 'O') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Nama file
    $fileName = 'data_pembangkit_' . time() . '.xlsx';
    $filePath = 'exports/' . $fileName;

    // Pastikan folder exports ada
    if (!file_exists('exports')) {
        mkdir('exports', 0777, true);
    }

    // Hapus file lama (lebih dari 24 jam)
    foreach (glob('exports/*.xlsx') as $file) {
        if (filemtime($file) < time() - 30) { // 86400 detik = 24 jam
            unlink($file);
        }
    }

    // Simpan file
    $fileName = 'exports/data_pembangkit_' . time() . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);

    // Tampilkan loading dan mulai pengunduhan
    echo "
    <html>
    <head>
        <script>
            function startDownload() {
                document.getElementById('loading').style.display = 'block';
                setTimeout(() => {
                    window.location.href = '$filePath';
                    setTimeout(() => {
                        document.getElementById('loading').innerHTML = 'Download selesai, kembali ke halaman...';
                        setTimeout(() => {
                            window.location.href = '?page=pembangkit';
                        }, 2000);
                    }, 2000);
                }, 1000);
            }
        </script>
        <style>
            #loading {
                display: none;
                width: 100%;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                font-size: 20px;
                text-align: center;
                line-height: 100vh;
                z-index: 9999;
            }
        </style>
    </head>
    <body onload='startDownload()'>
        <div id='loading'>Sedang mengunduh, harap tunggu...</div>
    </body>
    </html>";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
