<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DeliveryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')
                ->withErrors('Tài khoản của bạn đã hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }

        // --- Gọi API DeliveryHistory ---
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => $token,
        ])->get('https://localhost:44390/api/DeliveryHistory/SelectAll');

        $order = $response->json();
        $deliveryHistoryList = $order['Data'] ?? [];

        // Sắp xếp Id giảm dần
        usort($deliveryHistoryList, function ($a, $b) {
            return $b['Id'] <=> $a['Id'];
        });

        // --- Lọc dữ liệu ---
        $name      = $request->input('name');        
        $status    = $request->input('status');      
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');

        $filtered = array_filter($deliveryHistoryList, function ($item) use ($name, $status, $from_date, $to_date) {
            $match = true;

            // Lọc theo tên
            if (!empty($name)) {
                $customerName = $item['CustomerName'] ?? '';
                $match = $match && stripos($customerName, $name) !== false;
            }

            // Lọc theo trạng thái
            if ($status !== null && $status !== '') {
                $itemStatus = $item['Status'] ?? null;
                $match = $match && (string)$itemStatus === (string)$status;
            }

            // Lọc theo ngày
            if (!empty($from_date) || !empty($to_date)) {
                $deliveryDate = isset($item['DeliveryDate']) ? Carbon::parse($item['DeliveryDate']) : null;

                if ($deliveryDate) {
                    if (!empty($from_date)) {
                        $match = $match && $deliveryDate->gte(Carbon::parse($from_date));
                    }
                    if (!empty($to_date)) {
                        $match = $match && $deliveryDate->lte(Carbon::parse($to_date));
                    }
                }
            }

            return $match;
        });

        // --- Phân trang thủ công ---
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $items = new Collection($filtered);

        $paginateddeliveryHistory = new LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // --- Gọi API Notification ---
        $resNoti = Http::withoutVerifying()->withHeaders([
            'Authorization' => $token,
        ])->get('https://localhost:44390/api/Notification/SelectAll');
        $notiData = $resNoti->json();
        $notiList = $notiData['Data'] ?? [];

        usort($notiList, function ($a, $b) {
            return $b['Id'] <=> $a['Id'];
        });

        $todayCount = collect($notiList)
            ->filter(fn($noti) => Carbon::parse($noti['CreatedDate'])->isToday())
            ->count();

        return view('lichsu', [
            'deliveryHistoryList' => $paginateddeliveryHistory,
            'notiList'    => $notiList,
            'todayCount'  => $todayCount,
        ]);
    }
}
