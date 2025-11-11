@extends('admin.layouts.master')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@push('styles')
<style>
    /* ====== CARD STATS ====== */
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        border: none;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #fff;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .stats-card.success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stats-card.warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stats-card.info    { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }

    .stat-icon { font-size: 2.5rem; opacity: 0.85; }
    .stat-value { font-size: 1.8rem; font-weight: bold; margin: 8px 0; }
    .stat-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

    .growth-indicator {
        font-size: 0.8rem;
        padding: 3px 10px;
        border-radius: 10px;
        font-weight: bold;
        display: inline-block;
        margin-top: 5px;
    }
    .growth-positive { background: rgba(40,167,69,0.15); color: #ffffff; }
    .growth-negative { background: rgba(220,53,69,0.15); color: #dc3545; }

    /* ====== CHART ====== */
    .chart-container {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
        margin-bottom: 25px;
        height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .chart-container canvas {
        flex-grow: 1;
        width: 100% !important;
        height: 100% !important;
    }

    /* ====== TABLE ====== */
    .recent-transactions {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
    }
    .recent-transactions h4 {
        font-weight: 600;
        margin-bottom: 1.2rem;
        color: #333;
    }
    .table th {
        background: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.85rem;
        color: #555;
    }
    .table td { vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Total Produk -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-box stat-icon me-3"></i>
                    <div>
                        <div class="stat-value">{{ number_format($totalProduk ?? 0) }}</div>
                        <div class="stat-label">Total Produk</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pelanggan -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fa fa-users stat-icon me-3"></i>
                    <div>
                        <div class="stat-value">{{ number_format($totalPelanggan ?? 0) }}</div>
                        <div class="stat-label">Total Pelanggan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Transaksi -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fa fa-shopping-cart stat-icon me-3"></i>
                    <div>
                        <div class="stat-value">{{ number_format($totalTransaksi ?? 0) }}</div>
                        <div class="stat-label">Total Transaksi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Penjualan -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fa fa-dollar-sign stat-icon me-3"></i>
                    <div>
                        <div class="stat-value">Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Penjualan</div>
                        @if(isset($salesGrowth))
                            <span class="growth-indicator {{ $salesGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fa fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs(round($salesGrowth, 1)) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="row">
    <div class="col-lg-8">
        <div class="chart-container">
            <h4 class="mb-3"><i class="fa fa-chart-line me-2 text-primary"></i>Trend Penjualan Bulanan</h4>
            <canvas id="sales-chart"></canvas>
        </div>
    </div>
</div>

<!-- Transaksi Terbaru -->
<div class="row">
    <div class="col-12">
        <div class="recent-transactions">
            <h4><i class="fa fa-history me-2 text-secondary"></i>Transaksi Terbaru</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions ?? [] as $transaction)
                        <tr>
                            <td><strong>#{{ $transaction->id_transaksi }}</strong></td>
                            <td>{{ $transaction->pelanggan->nama_pelanggan ?? 'N/A' }}</td>
                            <td><strong>Rp {{ number_format($transaction->total_setelah_diskon, 0, ',', '.') }}</strong></td>
                            <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td><span class="badge bg-success">Selesai</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
$(function() {
    const monthlySales = @json($monthlySales ?? []);
    const labels = monthlySales.map(i => {
        const d = new Date(i.month + '-01');
        return d.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
    });
    const data = monthlySales.map(i => i.total);

    new Chart(document.getElementById('sales-chart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102,126,234,0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });

    // SweetAlert2 Notification Example
    // Uncomment the following lines to test SweetAlert2
    // Swal.fire({
    //     title: 'Selamat Datang!',
    //     text: 'Dashboard Admin Toko Teknologi',
    //     icon: 'success',
    //     confirmButtonText: 'OK'
    // });
});
</script>
@endpush
