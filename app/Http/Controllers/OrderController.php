<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrderController extends Controller
{
    public function index(Request $request)
    {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/DailyOrder/SelectAll');
        $order = $response->json();

        // Lấy mảng Data
        $orderList = $order['Data'] ?? [];

        usort($orderList, function ($a, $b) {
            return $b['Id'] <=> $a['Id']; 
        });

        // --- Lọc dữ liệu ---
        $name      = $request->input('name');        // tên khách hàng
        $status    = $request->input('status');      

        $filtered = array_filter($orderList, function ($item) use ($name, $status) {
            $match = true;

            if (!empty($name)) {
                $customerName = $item['CustomerName'] ?? '';
                $match = $match && stripos($customerName, $name) !== false;
            }

            if ($status !== null && $status !== '') {
                $deliveryStatus = $item['Status'] ?? null;
                $match = $match && (string)$deliveryStatus === (string)$status;
            }

            return $match;
        });

        // --- Phân trang thủ công ---
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $items = new \Illuminate\Support\Collection($filtered);

        $paginatedOrder = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

         // --- Lấy danh sách shipper ---
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
        $shipperResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/Account/SelectAllAccountRoleShipper');
        $shipper = $shipperResponse->json();

        $shipperList = $shipper['Data'] ?? [];

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

        return view('donhang', ['orderList' => $paginatedOrder, 'shipperList' => $shipperList,
            'notiList'    => $notiList,
            'todayCount'  => $todayCount,]);
    }

    public function assignShipper(Request $request)
    {
        $orderId   = (int) $request->input('orderId');
        $accountId = (int) $request->input('AccountId'); // id shipper chọn

        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $baseUrl = env('API_BASE_URL', 'https://localhost:44390');

            // Lấy order từ API
            $listUrl = $baseUrl . '/api/DailyOrder/SelectAll';
            $resp = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($listUrl);
            $orders = $resp->json()['Data'] ?? [];
            $order = collect($orders)->firstWhere('Id', $orderId);

            if (!$order) {
                return back()->withErrors(['Không tìm thấy đơn hàng']);
            }

            $now = Carbon::now()->toIso8601String();

            /**
             * 1. Update DailyOrder -> Status = 2
             */
            $payloadOrder = [
                'Id'                  => $order['Id'],
                'CreatedDate'         => $order['CreatedDate'] ?? $now,
                'CreatedBy'           => $order['CreatedBy'] ?? 'system',
                'ModifiedDate'        => $now,
                'ModifiedBy'          => auth()->user()->name ?? 'system',
                'EditMode'            => 2,
                'OrderCode'           => $order['OrderCode'] ?? '',
                'CustomerId'          => $order['CustomerId'] ?? 0,
                'CustomerName'        => $order['CustomerName'] ?? '',
                'ComboId'             => $order['ComboId'] ?? 0,
                'ProvinceId'          => $order['ProvinceId'] ?? 0,
                'DistrictId'          => $order['DistrictId'] ?? 0,
                'CommuneId'           => $order['CommuneId'] ?? 0,
                'Address'             => $order['Address'] ?? '',
                'PhoneNumber'         => $order['PhoneNumber'] ?? '',
                'OrderType'           => $order['OrderType'] ?? 0,
                'ProductId'           => $order['ProductId'] ?? 0,
                'ProductName'         => $order['ProductName'] ?? '',
                'Quantity'            => $order['Quantity'] ?? 0,
                'Price'               => $order['Price'] ?? 0,
                'Note'                => $order['Note'] ?? '',
                'DeliveryDate'         => $order['DeliveryDate'] ?? $now,
                // cập nhật trạng thái
                'Status'              => 2,
                'CancelledByCustomer' => $order['CancelledByCustomer'] ?? 0,
            ];
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }

            $saveOrder = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])
                ->asJson()
                ->post($baseUrl . '/api/DailyOrder/SaveData', [$payloadOrder]);

            /**
             * 2. Tạo DeliveryAssignment record
             */
            $payloadAssign = [
                'Id'            => 0, // tạo mới
                'CreatedDate'   => $now,
                'CreatedBy'     => auth()->user()->name ?? 'system',
                'ModifiedDate'  => $now,
                'ModifiedBy'    => auth()->user()->name ?? 'system',
                'EditMode'      => 1,
                'OrderId'       => $order['Id'],
                'OrderCode'     => $order['OrderCode'] ?? '',
                'AccountId'     => $accountId, // shipper
                'AccountName'   => '',         // nếu muốn thì fetch từ danh sách shipper
                'CustomerName'  => $order['CustomerName'] ?? '',
                'DistrictName'  => $order['DistrictName'] ?? '',
                'ProvinceName'  => $order['ProvinceName'] ?? '',
                'CommuneName'   => $order['CommuneName'] ?? '',
                'PhoneNumber'   => $order['PhoneNumber'] ?? '',
                'Address'       => $order['Address'] ?? '',
                'Quantity'      => $order['Quantity'] ?? 0,
                'Price'         => $order['Price'] ?? 0,
                'AssignedAt'    => $now,
                'DeliveryStatus'=> 2, // trạng thái giao hàng
                'DeliveredAt'   => $now,
                'DeliveryNotes' => 'Phân công shipper',
            ];
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $saveAssign = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])
                ->asJson()
                ->post($baseUrl . '/api/DeliveryAssignments/SaveData', [$payloadAssign]);

            $bodyAssign = $saveAssign->json();

            if ($saveAssign->ok() && ($bodyAssign['Success'] ?? false) === true) {
                return redirect()->route('donhang')->with('success', 'Gán shipper thành công.');
            }

            return back()->withErrors(['Không thể gán shipper, API trả về lỗi.']);

        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception: ' . $e->getMessage()]);
        }
    }


}
