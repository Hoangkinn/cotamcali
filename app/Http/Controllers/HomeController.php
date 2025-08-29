<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')
                ->withErrors('Tài khoản của bạn đã hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }

        // --- Notification ---
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => $token,
        ])->get('https://localhost:44390/api/Notification/SelectAll');

        $order = $response->json();
        $notiList = $order['Data'] ?? [];

        usort($notiList, fn($a, $b) => $b['Id'] <=> $a['Id']);

        $todayCount = collect($notiList)
            ->filter(fn($noti) => Carbon::parse($noti['CreatedDate'])->isToday())
            ->count();

        // --- User info + dashboard ---
        try {
            $resUser = Http::withoutVerifying()->withHeaders([
                'Authorization' => $token,
            ])->get('https://localhost:44390/api/Common/getuserinfo');

            $userData  = $resUser->json();
            $userInfo  = $userData['Data']['User'] ?? [];
            $dashboard = $userData['Data']['dashboard'] ?? [];
        } catch (\Exception $e) {
            $userInfo  = [];
            $dashboard = [];
        }

        // --- GetTodayDeliveryOrders ---
        try {
            $resGetToday = Http::withoutVerifying()->withHeaders([
                'Authorization' => $token,
            ])->get('https://localhost:44390/api/Common/GetTodayDeliveryOrders');

            $GetTodayData = $resGetToday->json();
            $GetTodayInfo = $GetTodayData['Data'] ?? [];
            // dd($GetTodayInfo);

            // --- Lọc dữ liệu ---
            $name   = $request->input('name');
            $status = $request->input('status');

            $filtered = array_filter($GetTodayInfo, function ($item) use ($name, $status) {
                $match = true;

                if (!empty($name)) {
                    $customerName = $item['CustomerName'] ?? '';
                    $match = $match && stripos($customerName, $name) !== false;
                }

                if ($status !== null && $status !== '') {
                    $itemStatus = $item['Status'] ?? null;
                    $match = $match && (string)$itemStatus === (string)$status;
                }

                return $match;
            });

            // --- Phân trang ---
            $filteredCollection = collect($filtered)->sortByDesc('STT')->values();

            $perPage     = 10;
            $currentPage = $request->get('page', 1);
            $offset      = ($currentPage - 1) * $perPage;

            $GetTodayPaginated = new LengthAwarePaginator(
                $filteredCollection->slice($offset, $perPage)->values(),
                $filteredCollection->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } catch (\Exception $e) {
            $GetTodayPaginated = new LengthAwarePaginator([], 0, 10);
        }

        return view('welcome', [
            'notiList'   => $notiList,
            'todayCount' => $todayCount,
            'userInfo'   => $userInfo,
            'dashboard'  => $dashboard,
            'GetTodayInfo' => $GetTodayPaginated,
        ]);
    }
}
