
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="{{ asset('/') }}css/style.css" rel="stylesheet" />
</head>
    <body>
        <section class="login-block">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">    
                        <form class="md-float-material form-material" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="text-center">
                                <img src="{{ asset('/') }}images/divhub.png" style="width: 3cm; height: 3.5cm;" alt="logo.png">
                            </div>
                            <div class="auth-box card">
                                <div class="card-block">
                                    <div class="row m-b-20">
                                        <div class="col-md-12">
                                            <h3 class="text-center font-weight-bold">INTERNSHIP MANAGEMENT SISTEM</h3>
                                        </div>
                                    </div>
                                    <div class="form-group form-primary">
                                        <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        <span class="form-bar"></span>
                                        <label class="float-label">Email</label>
                                    </div>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                    <div class="form-group form-primary">
                                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                                        <span class="form-bar"></span>
                                        <label class="float-label">Password</label>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="row m-t-30">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20">Login</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>                
                    </div>
                </div>
            </div>
        </section>
        @include('sweetalert::alert')
        @if ($errors->any())
            <script>
                var errorMessages = [];
                @foreach ($errors->all() as $error)
                    errorMessages.push("{{ $error }}");
                @endforeach
            </script>
        @endif

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            // Pastikan array errorMessages telah didefinisikan sebelumnya
            if (errorMessages.length > 0) {
                var errorMessage = "<ul>";
                errorMessages.forEach(function(message) {
                    errorMessage +="<li>Email/Password yang Anda masukkan salah!</li>" + "<br>";
                });
                errorMessage += "</ul>";
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: errorMessage,
                });
            }
        </script> 
    </body>
</html>
