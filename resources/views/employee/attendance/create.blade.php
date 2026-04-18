@extends('layouts.app')        

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Register Absensi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employee.index') }}">Dashboard Karyawan</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Register Absensi
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-11 mx-auto">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Absensi Hari ini <?php $time=date("H:i:s"); $dt=date("d-M-Y"); echo $dt." ".$time;?>                
                            </h3>
                        </div>
                        @include('messages.alerts')
                        @if (!$attendance)
                        <form role="form" method="post" action="{{ route('employee.attendance.store', $employee->id) }}" >
                        @else
                        <form role="form" method="post" action="{{ route('employee.attendance.update', $attendance->id) }}" >
                            @method('PUT')
                        @endif
                            @csrf
                            <div class="card-body">
                                <?php if(date('h')>=17) { echo "Absensi Ditutup"; } else { ?>
                                @if (!$attendance)
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="entry_time">Waktu Absensi</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="entry_time"
                                            id="entry_time"
                                            placeholder="--:--:--"
                                            disabled
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="entry_location">Lokasi Absensi</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            id="entry_loc"
                                            placeholder="Locaton Loading..."
                                            disabled
                                            />
                                            <input type="text" name="entry_location" name="entry_location"
                                            id="entry_location" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="entry_ip">IP Address</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            id="entry_ip"
                                            name="entry_ip"
                                            placeholder="X.X.X.X"
                                            disabled
                                            />
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="entry_time">Waktu Absensi</label>
                                            <input
                                            type="text"
                                            value="{{ $attendance->created_at->format('d-m-Y,  H:i:s') }}"
                                            class="form-control text-center"
                                            name="entry_time"
                                            id="entry_time"
                                            placeholder="--:--:--"
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="entry_location">Lokasi Absensi</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="entry_location"
                                            value="{{ $attendance->lokasi_masuk }}"
                                            placeholder="..."
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="entry_ip">IP Address</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            id="entry_ip"
                                            value="{{ $attendance->ip_masuk }}"
                                            name="entry_ip"
                                            placeholder="X.X.X.X"
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if ($attendance && is_null($attendance->waktu_keluar))
                                <div id="daily-report-section">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exit_time">Waktu Selesai</label>
                                                <input
                                                type="text"
                                                class="form-control text-center"
                                                name="exit_time"
                                                id="exit_time"
                                                placeholder="--:--:--"
                                                disabled
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exit_location">Lokasi Selesai</label>
                                                <input
                                                type="text"
                                                class="form-control text-center"
                                                id="exit_loc"
                                                @if ($attendance)
                                                placeholder="Loading location..."
                                                    
                                                @else
                                                placeholder="..."
                                                    
                                                @endif
                                                disabled
                                                />
                                                <input type="text" name="exit_location"
                                                id="exit_location" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exit_ip">IP Address</label>
                                                <input
                                                type="text"
                                                class="form-control text-center"
                                                id="exit_ip"
                                                name="exit_ip"
                                                placeholder="X.X.X.X"
                                                disabled
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="form-group">
                                            <label for="daily_report" class="d-block text-center">Laporan Harian</label>
                                            <textarea class="form-control text-center"  name="daily_report" id="daily_report" cols="28" rows="3" placeholder="Laporan harian hanya akan tersimpan ketika melakukan Absen Keluar."></textarea>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                @if (!$attendance)
                                    <button type="submit" class="btn btn-primary p-3" style="font-size:1.2rem">
                                        Absen Masuk
                                    </button>
                                @elseif ($attendance && is_null($attendance->waktu_keluar))
                                    <button type="button" id="btn-exit" class="btn btn-primary pull-right p-3" style="font-size:1.2rem" disabled>
                                        Absen Keluar/Selesai
                                    </button>
                                @endif
                            </div>
                            <?php } ?>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        const hasAttendance = {{ $attendance ? 'true' : 'false' }};
        const awaitingCheckout = {{ ($attendance && is_null($attendance->waktu_keluar)) ? 'true' : 'false' }};

        // Show daily report section only when awaiting checkout
        $('#daily-report-section').toggle(awaitingCheckout);

        // Require daily report before enabling Absen Keluar
        const exitBtn = $('#btn-exit');
        const dailyReport = $('#daily_report');

        function updateExitButtonState() {
            if (awaitingCheckout) {
                const filled = dailyReport.val() && dailyReport.val().trim().length > 0;
                exitBtn.prop('disabled', !filled);
            }
        }
        dailyReport.on('input', updateExitButtonState);
        updateExitButtonState();

        // Populate entry location only when the user has not checked in yet
        function setEntryLocation(addressText) {
            $('#entry_loc').val(addressText);
            $('#entry_location').val(addressText);
        }

        function resolveAddress(position, onSuccess, onFail) {
            $.post("{{ route('employee.attendance.get-location') }}", {
                lat: position.coords.latitude,
                lon: position.coords.longitude,
                _token: $('meta[name=csrf-token]').attr('content'),
            })
            .done(onSuccess)
            .fail(function(xhr) {
                const msg = 'Gagal mendapatkan alamat: ' + (xhr.responseText || xhr.statusText || 'Tidak diketahui');
                onFail(msg);
            });
        }

        if (!hasAttendance) {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    resolveAddress(position, function(data) {
                        setEntryLocation(data);
                    }, function(msg) {
                        setEntryLocation(msg);
                    });
                }, function(err) {
                    const msg = 'Lokasi tidak tersedia: ' + (err && err.message ? err.message : 'izin ditolak atau perangkat tidak mendukung');
                    setEntryLocation(msg);
                }, { enableHighAccuracy: true, timeout: 15000 });
            } else {
                setEntryLocation('Perangkat tidak mendukung geolocation');
            }
        }

        // Capture exit location only when user clicks Absen Keluar
        exitBtn.on('click', function(e) {
            e.preventDefault();
            // Guard: require daily report
            if (!dailyReport.val() || dailyReport.val().trim().length === 0) {
                exitBtn.prop('disabled', true);
                return;
            }

            function submitForm() {
                const form = exitBtn.closest('form');
                if (form && form.length) {
                    form[0].submit();
                }
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    resolveAddress(position, function(data) {
                        $('#exit_loc').val(data);
                        $('#exit_location').val(data);
                        submitForm();
                    }, function(msg) {
                        $('#exit_loc').val(msg);
                        $('#exit_location').val(msg);
                        submitForm();
                    });
                }, function(err) {
                    const msg = 'Lokasi tidak tersedia: ' + (err && err.message ? err.message : 'izin ditolak atau perangkat tidak mendukung');
                    $('#exit_loc').val(msg);
                    $('#exit_location').val(msg);
                    submitForm();
                }, { enableHighAccuracy: true, timeout: 15000 });
            } else {
                $('#exit_loc').val('Perangkat tidak mendukung geolocation');
                $('#exit_location').val('Perangkat tidak mendukung geolocation');
                submitForm();
            }
        });
    });
</script>
@endsection
