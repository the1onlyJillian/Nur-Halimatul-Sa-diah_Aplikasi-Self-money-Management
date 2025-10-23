<?php
include 'config.php';

// Hitung total pemasukan dan pengeluaran
$total_pemasukan = 0;
$total_pengeluaran = 0;

$result = $conn->query("SELECT jenis, SUM(jumlah) as total FROM transaksi GROUP BY jenis");
while ($row = $result->fetch_assoc()) {
    if ($row['jenis'] == 'Pemasukan') {
        $total_pemasukan = $row['total'];
    } else {
        $total_pengeluaran = $row['total'];
    }
}

$saldo = $total_pemasukan - $total_pengeluaran;

// Hitung persentase pengeluaran
$persentase_pengeluaran = $total_pemasukan > 0 ? ($total_pengeluaran / $total_pemasukan) * 100 : 0;

// Ambil data untuk grafik
$chart_data = array();
$result_chart = $conn->query("SELECT kategori, SUM(jumlah) as total FROM transaksi WHERE jenis='Pengeluaran' GROUP BY kategori");
while ($row = $result_chart->fetch_assoc()) {
    $chart_data[] = $row;
}

// Ambil data transaksi terbaru
$transaksi = $conn->query("SELECT * FROM transaksi ORDER BY tanggal DESC LIMIT 10");

// Ambil data bulan ini
$bulan_ini = date('Y-m');
$pemasukan_bulan_ini = $conn->query("SELECT SUM(jumlah) as total FROM transaksi WHERE jenis='Pemasukan' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'")->fetch_assoc()['total'] ?? 0;
$pengeluaran_bulan_ini = $conn->query("SELECT SUM(jumlah) as total FROM transaksi WHERE jenis='Pengeluaran' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self-money Management</title>

    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3135/3135706.png" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            --warning-gradient: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        
        .summary-card {
            color: white;
            border-radius: 20px;
            padding: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .summary-card .card-content {
            position: relative;
            z-index: 2;
        }
        
        .stats-number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        
        .btn-modern {
            border-radius: 15px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .form-control-modern {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.1);
        }
        
        .table-modern {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table-modern thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table-modern tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f1f3f4;
        }
        
        .table-modern tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .badge-modern {
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 600;
        }
        
        .progress-modern {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
        }
        
        .progress-bar-modern {
            border-radius: 10px;
        }
        
        .theme-toggle {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            transform: rotate(15deg);
        }

        /* === TARGET KEUNGAN STYLES === */
.target-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
    background: #fff;
}

.target-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #667eea !important;
    background: #f8f9ff;
}

.target-progress {
    border-radius: 10px;
    overflow: hidden;
    background: #e9ecef;
}

.target-progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
    height: 8px;
}

.target-completed {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%) !important;
}

.target-warning {
    background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%) !important;
}

.target-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%) !important;
}

.target-info {
    background: linear-gradient(135deg, #36A2EB 0%, #2e8bc0 100%) !important;
}

/* Modal target styles */
.modal-target-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.target-stats {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 15px;
}

.target-countdown {
    font-size: 0.8rem;
    font-weight: 600;
}

.target-countdown.urgent {
    color: #dc3545;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Progress bar animation */
.progress-bar {
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Badge styles untuk target */
.target-badge {
    font-size: 0.7rem;
    padding: 4px 8px;
    border-radius: 8px;
}

/* Empty state untuk target */
.target-empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: #6c757d;
}

.target-empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-wallet me-2"></i>Self-Money Management
            </a>
            
            <div class="d-flex align-items-center">
                <!-- Theme Toggle -->
                <div class="theme-toggle me-3 text-white" onclick="toggleTheme()">
                    <i class="fas fa-moon fa-lg"></i>
                </div>
                
                <!-- Current Date -->
                <span class="navbar-text text-white">
                    <i class="fas fa-calendar me-1"></i>
                    <?= date('d F Y') ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Summary Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="summary-card" style="background: var(--primary-gradient);">
                    <div class="card-content">
                        <div class="stats-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="stats-number">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
                        <div class="stats-label">SALDO TOTAL</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="summary-card" style="background: var(--success-gradient);">
                    <div class="card-content">
                        <div class="stats-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="stats-number">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></div>
                        <div class="stats-label">TOTAL PEMASUKAN</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="summary-card" style="background: var(--danger-gradient);">
                    <div class="card-content">
                        <div class="stats-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="stats-number">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
                        <div class="stats-label">TOTAL PENGELUARAN</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="summary-card" style="background: var(--warning-gradient);">
                    <div class="card-content">
                        <div class="stats-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stats-number"><?= number_format($persentase_pengeluaran, 1) ?>%</div>
                        <div class="stats-label">RASIO PENGELUARAN</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column - Form & Monthly Stats -->
            <div class="col-lg-4">
                <!-- Quick Add Form -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Transaksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="proses.php" method="POST" class="needs-validation" novalidate>
                            <!-- Transaction Type Toggle -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jenis Transaksi</label>
                                <div class="btn-group w-100 shadow-sm" role="group">
                                    <input type="radio" class="btn-check" name="jenis" id="pemasukan" value="Pemasukan" required checked>
                                    <label class="btn btn-outline-success" for="pemasukan">
                                        <i class="fas fa-arrow-down me-2"></i>Pemasukan
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="jenis" id="pengeluaran" value="Pengeluaran" required>
                                    <label class="btn btn-outline-danger" for="pengeluaran">
                                        <i class="fas fa-arrow-up me-2"></i>Pengeluaran
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Amount Input -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Transaksi</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-light border-end-0">Rp</span>
                                    <input type="number" class="form-control form-control-modern border-start-0" 
                                           name="jumlah" min="1" placeholder="0" required>
                                </div>
                            </div>
                            
                            <!-- Category Select -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select class="form-select form-control-modern shadow-sm" name="kategori" required>
                                    <option value="">Pilih Kategori...</option>
                                    <optgroup label="Pemasukan">
                                        <option value="Gaji">💰 Gaji</option>
                                        <option value="Freelance">💻 Freelance</option>
                                        <option value="Investasi">📈 Investasi</option>
                                        <option value="Bonus">🎁 Bonus</option>
                                    </optgroup>
                                    <optgroup label="Pengeluaran">
                                        <option value="Makanan">🍔 Makanan</option>
                                        <option value="Transportasi">🚗 Transportasi</option>
                                        <option value="Belanja">🛍️ Belanja</option>
                                        <option value="Hiburan">🎬 Hiburan</option>
                                        <option value="Tagihan">📋 Tagihan</option>
                                        <option value="Kesehatan">🏥 Kesehatan</option>
                                    </optgroup>
                                </select>
                            </div>
                            
                            <!-- Description -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <textarea class="form-control form-control-modern shadow-sm" name="deskripsi" 
                                          rows="3" placeholder="Deskripsi transaksi..."></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success btn-modern w-100 shadow">
                                <i class="fas fa-paper-plane me-2"></i>Simpan Transaksi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Monthly Statistics -->
                <div class="card">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Statistik Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Pemasukan:</span>
                                <span class="text-success fw-bold">Rp <?= number_format($pemasukan_bulan_ini, 0, ',', '.') ?></span>
                            </div>
                            <div class="progress progress-modern">
                                <div class="progress-bar bg-success progress-bar-modern" 
                                     style="width: <?= $pemasukan_bulan_ini > 0 ? 100 : 0 ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Pengeluaran:</span>
                                <span class="text-danger fw-bold">Rp <?= number_format($pengeluaran_bulan_ini, 0, ',', '.') ?></span>
                            </div>
                            <div class="progress progress-modern">
                                <div class="progress-bar bg-danger progress-bar-modern" 
                                     style="width: <?= $pengeluaran_bulan_ini > 0 ? 100 : 0 ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Data untuk bulan <?= date('F Y') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Charts & Transactions -->
            <div class="col-lg-8">
                <!-- Charts Row -->
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header bg-warning text-dark py-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Distribusi Pengeluaran
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="expenseChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Target Keuangan Card -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-secondary text-white py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bullseye me-2"></i>Target Keuangan
                                </h5>
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalTarget">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="targetContainer">
                                    <!-- Target akan dimuat via AJAX -->
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary mb-3" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted">Memuat target...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-header bg-dark text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-receipt me-2"></i>Transaksi Terkini
                            </h5>
                            <span class="badge bg-light text-dark fs-6">
                                <?= $transaksi->num_rows ?> transaksi
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="120">Tanggal</th>
                                        <th width="100">Jenis</th>
                                        <th width="120">Kategori</th>
                                        <th width="120">Jumlah</th>
                                        <th>Keterangan</th>
                                        <th width="80" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($transaksi->num_rows > 0): ?>
                                        <?php while ($row = $transaksi->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?= date('d M', strtotime($row['tanggal'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($row['tanggal'])) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-modern <?= $row['jenis'] == 'Pemasukan' ? 'bg-success' : 'bg-danger' ?>">
                                                        <i class="fas fa-arrow-<?= $row['jenis'] == 'Pemasukan' ? 'down' : 'up' ?> me-1"></i>
                                                        <?= $row['jenis'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold"><?= $row['kategori'] ?></span>
                                                </td>
                                                <td class="fw-bold fs-6 <?= $row['jenis'] == 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                                                    Rp <?= number_format($row['jumlah'], 0, ',', '.') ?>
                                                </td>
                                                <td>
                                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                          data-bs-toggle="tooltip" title="<?= htmlspecialchars($row['deskripsi']) ?>">
                                                        <?= $row['deskripsi'] ?: '<span class="text-muted">-</span>' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="hapus.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Yakin ingin menghapus transaksi ini?')"
                                                       data-bs-toggle="tooltip" title="Hapus Transaksi">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-receipt fa-4x mb-3"></i>
                                                    <h5>Belum ada transaksi</h5>
                                                    <p class="mb-0">Mulai dengan menambahkan transaksi pertama Anda</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-wallet me-2"></i>MoneyManager Pro</h6>
                    <p class="mb-0 text-muted">Aplikasi manajemen keuangan modern untuk masa depan yang lebih baik</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted mb-0">
                        &copy; 2024 SCM Project - Kelompok Manajemen Keuangan
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Bootstrap Form Validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Theme Toggle Function
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.querySelector('.theme-toggle i');
            
            if (html.getAttribute('data-bs-theme') === 'dark') {
                html.setAttribute('data-bs-theme', 'light');
                themeIcon.className = 'fas fa-moon fa-lg';
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                themeIcon.className = 'fas fa-sun fa-lg';
            }
        }

        // Chart.js Implementation
        const chartData = <?= json_encode($chart_data) ?>;
        
        if (chartData.length > 0) {
            const labels = chartData.map(item => item.kategori);
            const amounts = chartData.map(item => parseFloat(item.total));

            const ctx = document.getElementById('expenseChart').getContext('2d');
            const expenseChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: amounts,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                            '#9966FF', '#FF9F40', '#8AC926', '#1982C4',
                            '#6A4C93', '#FF595E', '#1982C4', '#8AC926'
                        ],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('expenseChart').innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-chart-pie fa-3x mb-3"></i>
                    <p>Belum ada data pengeluaran untuk ditampilkan</p>
                </div>
            `;
        }

        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on load
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Fungsi untuk memuat target keuangan
function loadTargets() {
    fetch('target.php')
        .then(response => response.json())
        .then(targets => {
            const container = document.getElementById('targetContainer');
            
            if (targets.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-bullseye fa-3x mb-3"></i>
                        <p>Belum ada target keuangan</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTarget">
                            <i class="fas fa-plus me-1"></i>Buat Target Pertama
                        </button>
                    </div>
                `;
                return;
            }
            
            let html = '';
            targets.forEach(target => {
                const progressColor = target.persentase >= 100 ? 'bg-success' : 
                                   target.persentase >= 75 ? 'bg-primary' : 
                                   target.persentase >= 50 ? 'bg-warning' : 'bg-info';
                
                html += `
                    <div class="target-item mb-3 p-3 border rounded" onclick="editProgress(${target.id}, '${target.nama_target}', ${target.target_jumlah}, ${target.terkumpul})" style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0 fw-bold">${target.nama_target}</h6>
                            <span class="badge bg-light text-dark">${target.sisa_hari} hari</span>
                        </div>
                        
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar ${progressColor}" role="progressbar" 
                                 style="width: ${Math.min(target.persentase, 100)}%">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Rp ${parseInt(target.terkumpul).toLocaleString('id-ID')} / Rp ${parseInt(target.target_jumlah).toLocaleString('id-ID')}
                            </small>
                            <strong class="${target.persentase >= 100 ? 'text-success' : 'text-primary'}">
                                ${target.persentase}%
                            </strong>
                        </div>
                        
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-calendar me-1"></i>
                            ${new Date(target.tanggal_target).toLocaleDateString('id-ID')}
                        </small>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading targets:', error);
            document.getElementById('targetContainer').innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Gagal memuat target</p>
                </div>
            `;
        });
}

// Fungsi tambah target
function tambahTarget() {
    const form = document.getElementById('formTarget');
    const formData = new FormData(form);
    formData.append('action', 'tambah_target');
    
    fetch('target.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Target berhasil ditambahkan!');
            $('#modalTarget').modal('hide');
            form.reset();
            loadTargets();
        } else {
            alert('Gagal menambah target: ' + result.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Fungsi edit progress
function editProgress(id, nama, total, terkumpul) {
    document.getElementById('targetId').value = id;
    document.getElementById('targetNama').value = nama;
    document.getElementById('targetTotal').value = 'Rp ' + parseInt(total).toLocaleString('id-ID');
    document.getElementById('targetTerkumpul').value = terkumpul;
    document.getElementById('targetTerkumpul').max = total;
    
    updateProgressBar();
    $('#modalProgress').modal('show');
}

// Update progress bar real-time
function updateProgressBar() {
    const terkumpul = parseFloat(document.getElementById('targetTerkumpul').value) || 0;
    const total = parseFloat(document.getElementById('targetTotal').value.replace(/[^\d]/g, '')) || 1;
    const persentase = Math.min((terkumpul / total) * 100, 100);
    
    document.getElementById('progressBar').style.width = persentase + '%';
    document.getElementById('persentaseText').textContent = persentase.toFixed(1) + '%';
    
    // Update progress bar color
    const progressBar = document.getElementById('progressBar');
    progressBar.className = 'progress-bar ' + 
        (persentase >= 100 ? 'bg-success' : 
         persentase >= 75 ? 'bg-primary' : 
         persentase >= 50 ? 'bg-warning' : 'bg-info');
}

// Update progress target
function updateProgress() {
    const id = document.getElementById('targetId').value;
    const terkumpul = document.getElementById('targetTerkumpul').value;
    
    const formData = new FormData();
    formData.append('action', 'update_target');
    formData.append('id', id);
    formData.append('terkumpul', terkumpul);
    
    fetch('target.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Progress target berhasil diupdate!');
            $('#modalProgress').modal('hide');
            loadTargets();
        } else {
            alert('Gagal update target: ' + result.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Hapus target
function hapusTarget() {
    if (!confirm('Yakin ingin menghapus target ini?')) return;
    
    const id = document.getElementById('targetId').value;
    
    const formData = new FormData();
    formData.append('action', 'hapus_target');
    formData.append('id', id);
    
    fetch('target.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Target berhasil dihapus!');
            $('#modalProgress').modal('hide');
            loadTargets();
        } else {
            alert('Gagal menghapus target: ' + result.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Panggil loadTargets saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadTargets();
    
    // Event listener untuk real-time progress update
    document.getElementById('targetTerkumpul').addEventListener('input', updateProgressBar);
    
    // Set min date untuk input tanggal
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="tanggal_target"]').min = today;
});

    </script>



        <!-- Modal Tambah Target -->
<div class="modal fade" id="modalTarget" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-bullseye me-2"></i>Tambah Target Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTarget">
                    <div class="mb-3">
                        <label class="form-label">Nama Target</label>
                        <input type="text" class="form-control" name="nama_target" placeholder="Contoh: Tabungan Liburan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Jumlah (Rp)</label>
                        <input type="number" class="form-control" name="target_jumlah" min="1000" placeholder="1000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Target</label>
                        <input type="date" class="form-control" name="tanggal_target" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Tabungan">Tabungan</option>
                            <option value="Investasi">Investasi</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Kendaraan">Kendaraan</option>
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Kesehatan">Kesehatan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="tambahTarget()">
                    <i class="fas fa-save me-2"></i>Simpan Target
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Progress -->
<div class="modal fade" id="modalProgress" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>Update Progress Target
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProgress">
                    <input type="hidden" name="target_id" id="targetId">
                    <div class="mb-3">
                        <label class="form-label">Nama Target</label>
                        <input type="text" class="form-control" id="targetNama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Total</label>
                        <input type="text" class="form-control" id="targetTotal" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Terkumpul (Rp)</label>
                        <input type="number" class="form-control" name="terkumpul" id="targetTerkumpul" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Persentase</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="text-center mt-2">
                            <span id="persentaseText">0%</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="hapusTarget()">
                    <i class="fas fa-trash me-2"></i>Hapus
                </button>
                <button type="button" class="btn btn-primary" onclick="updateProgress()">
                    <i class="fas fa-save me-2"></i>Update Progress
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>