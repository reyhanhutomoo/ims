@extends('layouts.app')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Dashboard MOA/IA</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Dashboard Admin</a></li>
                    <li class="breadcrumb-item active">Dashboard MOA/IA</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ array_sum($moaSeries) }}</h3>
                        <p>Total MOA ({{ $year }})</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ array_sum($iaSeries) }}</h3>
                        <p>Total IA ({{ $year }})</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ array_sum($totalSeries) }}</h3>
                        <p>Total MoA/IA ({{ $year }})</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title">Grafik Total MoA/IA Per Bulan - {{ $year }}</h3>
                        <form method="GET" action="{{ route('admin.moa.dashboard') }}" class="form-inline">
                            <label for="year" class="mr-2 mb-0">Tahun</label>
                            <select id="year" name="year" class="form-control" onchange="this.form.submit()">
                                @for($y = now()->year - 5; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
                    @php($hasData = array_sum($totalSeries) > 0)
                    <div class="card-body" style="height: 420px;">
                        @if($hasData)
                            <canvas id="moaIaMonthlyChart" style="min-height:380px; height:380px; max-height:380px; width:100%;"></canvas>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                Belum ada data MOA/IA pada tahun {{ $year }}.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="/plugins/chart.js/Chart.min.js"></script>
<script>
window.addEventListener('load', function() {
    var hasData = @json($hasData);
    if (!hasData) {
        // Tidak ada data, jangan inisialisasi chart
        return;
    }

    var labels = @json($labels);
    var moaSeries = @json($moaSeries);
    var iaSeries = @json($iaSeries);
    var totalSeries = @json($totalSeries);

    var canvas = document.getElementById('moaIaMonthlyChart');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'MOA',
                    data: moaSeries,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                },
                {
                    label: 'IA',
                    data: iaSeries,
                    backgroundColor: 'rgba(255, 159, 64, 0.5)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1,
                },
                {
                    type: 'line',
                    label: 'Total',
                    data: totalSeries,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0.3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 0,
                        stepSize: 1
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    footer: function(items, data) {
                        var idx = items[0].index;
                        return 'Total: ' + totalSeries[idx];
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
        }
    });
});
</script>
@endsection
