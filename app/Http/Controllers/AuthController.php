<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // trả về view login.blade.php
    }

    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'Username' => 'required|string',
            'Password' => 'required|string|min:6',
        ], [
            'Username.required' => 'Vui lòng nhập Email / SĐT / Username',
            'Password.required' => 'Vui lòng nhập mật khẩu',
            'Password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        $payload = [
            "Username" => $request->Username,
            "Password" => md5($request->Password),
        ];

        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';
        $apiUrl  = $baseUrl . '/api/Authen/login';

        try {
            $response = Http::withoutVerifying()
                ->asJson()
                ->post($apiUrl, $payload);

            $body = $response->json();

            if ($response->ok() && isset($body['Data']['User'])) {
                $user = $body['Data']['User'];
                $token = $body['Data']['Token'];
                // dd( $body);
                // Lưu user & token vào session
                session([
                    'user' => $user,
                    'token' => $token,
                ]);
                // $request->session()->save();
                return redirect()->route('welcome')->with('success', 'Đăng nhập thành công!');
            }

            return back()->withErrors([
                'login' => $body['Message'] ?? 'Sai tài khoản hoặc mật khẩu',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'login' => 'Không thể kết nối server: ' . $e->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        session()->forget(['user', 'token']);
        return redirect()->route('login')->with('success', 'Đã đăng xuất.');
    }


    public function callApi($endpoint, $method = 'GET', $data = [])
    {
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';
        $token   = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Bạn cần đăng nhập trước.');
        }

        $http = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => $token,
            ]);

        if ($method === 'POST') {
            $response = $http->post($baseUrl . $endpoint, $data);
        } else {
            $response = $http->get($baseUrl . $endpoint, $data);
        }

        return $response->json();
    }
}
