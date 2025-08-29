@extends('layouts.app')

@section('content')

<div class="auth-main relative">
      <div class="auth-wrapper v1 flex items-center w-full h-full min-h-screen">
        <div class="auth-form flex items-center justify-center grow flex-col min-h-screen relative p-6 ">
          <div class="w-full max-w-[350px] relative">
            <div class="auth-bg ">
              <span class="absolute top-[-100px] right-[-100px] w-[300px] h-[300px] block rounded-full bg-theme-bg-1 animate-[floating_7s_infinite]"></span>
              <span class="absolute top-[150px] right-[-150px] w-5 h-5 block rounded-full bg-primary-500 animate-[floating_9s_infinite]"></span>
              <span class="absolute left-[-150px] bottom-[150px] w-5 h-5 block rounded-full bg-theme-bg-1 animate-[floating_7s_infinite]"></span>
              <span class="absolute left-[-100px] bottom-[-100px] w-[300px] h-[300px] block rounded-full bg-theme-bg-2 animate-[floating_9s_infinite]"></span>
            </div>
            <div class="card sm:my-12  w-full shadow-none">
              <div class="card-body !p-10">
                <div class="text-center mb-8">
                  <a href="#"><img src="../assets/images/logo-dark.svg" alt="img" class="mx-auto auth-logo"/></a>
                </div>
                <h4 class="text-center font-medium mb-4">Login</h4>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                      <label for="username">Email / SĐT / Code</label>
                      <input type="text" name="Username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Mật khẩu</label>
                        <input type="password" name="Password" class="form-control" required>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button class="btn btn-primary mx-auto shadow-2xl" type="submit">Đăng nhập</button>
                    </div>
                </form>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
@endsection