<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/DeliveryAssignments/SelectAll');
        $order = $response->json();

        // Lấy mảng Data
        $transportList = $order['Data'] ?? [];

        usort($transportList, function ($a, $b) {
            return $b['Id'] <=> $a['Id']; 
        });

        // --- Lọc dữ liệu ---
        $name      = $request->input('name');        // tên khách hàng
        $status    = $request->input('status');      

        $filtered = array_filter($transportList, function ($item) use ($name, $status) {
            $match = true;

            if (!empty($name)) {
                $customerName = $item['CustomerName'] ?? '';
                $match = $match && stripos($customerName, $name) !== false;
            }

            if ($status !== null && $status !== '') {
                $deliveryStatus = $item['DeliveryStatus'] ?? null;
                $match = $match && (string)$deliveryStatus === (string)$status;
            }

            return $match;
        });

        // --- Phân trang thủ công ---
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $items = new \Illuminate\Support\Collection($filtered);

        $paginatedTransport = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // --- Gọi API Notification ---
        $resNoti = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/Notification/SelectAll');
        $notiData = $resNoti->json();
        $notiList = $notiData['Data'] ?? [];

        // Sắp xếp Id giảm dần
        usort($notiList, function ($a, $b) {
            return $b['Id'] <=> $a['Id']; 
        });

        // Tính số thông báo hôm nay
        $today = \Carbon\Carbon::today();
        $todayCount = collect($notiList)
            ->filter(function ($noti) use ($today) {
                return \Carbon\Carbon::parse($noti['CreatedDate'])->isToday();
            })
            ->count();

        return view('vanchuyen', ['transportList' => $paginatedTransport,
            'notiList'    => $notiList,
            'todayCount'  => $todayCount,]);
    }
    
    public function updateStatus(Request $request)
    {
        // dd('vao roi', $request->all());
        $assignId = (int) $request->input('assignment_id'); // Id của DeliveryAssignments
        $status   = (int) $request->input('status');       // 2=Giao hàng, 3=Đã giao, 4=Khách hủy
        $now      = Carbon::now()->toIso8601String();

        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $baseUrl = env('API_BASE_URL', 'https://localhost:44390');

            // Gọi API SelectAll để lấy record cũ
            $listUrl = $baseUrl . '/api/DeliveryAssignments/SelectAll';
            $resp = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($listUrl);
            $assignments = $resp->json()['Data'] ?? [];
            $assignment = collect($assignments)->firstWhere('Id', $assignId);

        // dd($assignment);
            if (!$assignment) {
                return back()->withErrors(['Không tìm thấy DeliveryAssignment']);
            }

            // Cập nhật payload
            $payload = [
                'Id'             => $assignment['Id'],
                'CreatedDate'    => $assignment['CreatedDate'] ?? $now,
                'CreatedBy'      => $assignment['CreatedBy'] ?? 'system',
                'ModifiedDate'   => $now,
                'ModifiedBy'     => auth()->user()->name ?? 'system',
                'EditMode'       => 2,
                'OrderId'        => $assignment['OrderId'] ?? 0,
                'OrderCode'      => $assignment['OrderCode'] ?? '',
                'AccountId'      => $assignment['AccountId'] ?? '',
                'AccountName'    => $assignment['AccountName'] ?? '',
                'CustomerName'   => $assignment['CustomerName'] ?? '',
                'DistrictName'   => $assignment['DistrictName'] ?? '',
                'ProvinceName'   => $assignment['ProvinceName'] ?? '',
                'CommuneName'    => $assignment['CommuneName'] ?? '',
                // 'PhoneNumber'    => $assignment['PhoneNumber'],
                'Address'        => $assignment['Address'] ?? '',
                'Quantity'       => $assignment['Quantity'] ?? '',
                'Price'          => $assignment['Price'] ?? 0,
                'DeliveryStatus' => $status,  
            ];
            // dd($payload);
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $save = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])
                ->asJson()
                ->post($baseUrl . '/api/DeliveryAssignments/SaveData', [$payload]);

            $body = $save->json();

            if ($save->ok() && ($body['Success'] ?? false) === true) {
                return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
            }

            return back()->withErrors(['API lỗi khi cập nhật trạng thái.']);

        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception: ' . $e->getMessage()]);
        }
    }
}
