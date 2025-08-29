<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Bạn cần đăng nhập lại để lấy token.');
        }
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/ComboTypes/SelectAll');
        $combos = $response->json();

        // Lấy mảng Data
        $combosList = $combos['Data'] ?? [];
        
        usort($combosList, function ($a, $b) {
            return $b['Id'] <=> $a['Id']; 
        });
        // --- Lọc dữ liệu ---
        $name      = $request->input('name');     
        $status    = $request->input('status');     

        $filtered = array_filter($combosList, function ($item) use ($name, $status) {
            $match = true;

            if (!empty($name)) {
                $match = $match && stripos($item['ComboName'], $name) !== false;
            }

            if ($status !== null && $status !== '') {
                $match = $match && (string)$item['Status'] === (string)$status;
            }

            return $match;
        });

        // --- Phân trang thủ công ---
        $page = $request->input('page', 1); // Trang hiện tại
        $perPage = 5; // Số item mỗi trang
        $offset = ($page - 1) * $perPage;

        // Chuyển mảng thành Collection để phân trang
        $items = new \Illuminate\Support\Collection($filtered);

        // Tạo đối tượng LengthAwarePaginator
        $paginatedCombos = new \Illuminate\Pagination\LengthAwarePaginator(
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

        return view('combo.combo', ['combosList' => $paginatedCombos, 'notiList' => $notiList, 'todayCount' => $todayCount,]);
    }

    public function create()
    {
        try {
            $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/selectnewcode';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
        $body = $response->json();

        // Giả sử API trả về dạng { "Data": "COMBO123" }
        $newCode = $body['Data'] ?? '';

        } catch (\Exception $e) {
            $newCode = '';
        }

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

        return view('combo.addcombo', compact('newCode', 'notiList', 'todayCount'));
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'ComboCode'    => 'nullable|string|max:255',
            'ComboName'    => 'required|string|max:255',
            'TotalMeals'    => 'required|integer|min:1|gte:NumberOfDate',
            'NumberOfDate'  => 'required|integer|min:1|lte:TotalMeals',
            'Description'  => 'nullable|string',
            'Status'       => 'required|in:0,1',
            'CreatedBy'    => 'nullable|string|max:255', // optional
        ], [
            'TotalMeals.gte' => 'Tổng xuất ăn không được nhỏ hơn Số ngày sử dụng xuất ăn.',
        ]);

        $now = Carbon::now()->toIso8601String(); // ISO datetime

        $creator = $validated['CreatedBy'] ?? (auth()->check() ? auth()->user()->name : 'system');

        $payload = [
            'Id'            => 0,
            'CreatedDate'   => $now,
            'CreatedBy'     => $creator,
            'ModifiedDate'  => $now,
            'ModifiedBy'    => $creator,
            'EditMode'      => 1, // 1 = Thêm mới
            'ComboCode'     => $validated['ComboCode'] ?? '',
            'ComboName'     => $validated['ComboName'],
            'NumberOfDate'  => (int) ($validated['NumberOfDate']),
            'TotalMeals'    => (int) ($validated['TotalMeals']),
            'Description'   => $validated['Description'] ?? '',
            'Status'        => (int) $validated['Status'],
        ];
        // API url - tốt nhất đặt vào .env: API_BASE_URL=https://localhost:44390
        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/SaveData';
        try {
            // Khi test local với self-signed certificate, bỏ verify SSL (chỉ dev)
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])
            ->asJson()
            ->post($apiUrl, [$payload]);
            $body = $response->json();
            // dd($payload);
            if ($response->ok()) {
                $body = $response->json();
                // Backend của bạn trả { "Success": true, "Data": ..., ... }
                if (isset($body['Success']) && $body['Success'] === true) {
                    return redirect()->route('combo')->with('success', 'Thêm mới combo thành công.');
                }

                // Nếu API trả về nhưng báo lỗi, show message backend
                return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($body)]);
            }

            return back()->withErrors(['api' => 'Lỗi kết nối API. HTTP status: ' . $response->status() . ' — ' . $response->body()]);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception khi gọi API: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/SelectAll';

        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
            $body = $response->json();

            $combos = $body['Data'] ?? [];

            // Tìm combo theo ID
            $combo = collect($combos)->firstWhere('Id', (int) $id);

            if (!$combo) {
                return redirect()->route('combo')->withErrors(['api' => 'Không tìm thấy combo với ID ' . $id]);
            }

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

            return view('combo.editcombo', compact('combo', 'notiList', 'todayCount'));

        } catch (\Exception $e) {
            return redirect()->route('combo')->withErrors(['api' => 'Lỗi khi gọi API: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ComboCode'    => 'required|string|max:255',
            'ComboName'    => 'required|string|max:255',
            'TotalMeals'    => 'required|integer|min:1|gte:NumberOfDate',
            'NumberOfDate'  => 'required|integer|min:1|lte:TotalMeals',
            'Description'  => 'nullable|string',
            'Status'       => 'required|in:0,1',
            'ModifiedBy'   => 'nullable|string|max:255',
        ], [
            'TotalMeals.gte' => 'Tổng xuất ăn không được nhỏ hơn Số ngày sử dụng xuất ăn.',
        ]);


        $now = Carbon::now()->toIso8601String();
        $modifier = $validated['ModifiedBy'] ?? (auth()->check() ? auth()->user()->name : 'system');

        $payload = [
            'Id'            => (int) $id,
            'CreatedDate'   => $validated['CreatedDate'] ?? $now, // Nếu API yêu cầu thì truyền
            'CreatedBy'     => $validated['CreatedBy'] ?? 'system',
            'ModifiedDate'  => $now,
            'ModifiedBy'    => $modifier,
            'EditMode'      => 2, // Sửa
            'ComboCode'     => $validated['ComboCode'],
            'ComboName'     => $validated['ComboName'],
            'NumberOfDate'  => (int) ($validated['NumberOfDate']),
            'TotalMeals'    => (int) ($validated['TotalMeals']),
            'Description'   => $validated['Description'] ?? '',
            'Status'        => (int) $validated['Status'],
        ];

        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/SaveData';
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->asJson()->post($apiUrl, [$payload]);
            $body = $response->json();

            if ($response->ok() && ($body['Success'] ?? false) === true) {
                return redirect()->route('combo')->with('success', 'Cập nhật combo thành công.');
            }

            return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($body)]);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception khi gọi API: ' . $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        $now = Carbon::now()->toIso8601String();
        $user = auth()->check() ? auth()->user()->name : 'system';

        $payload = [
            'Id'            => (int) $id,
            'CreatedDate'   => $now, // Có thể API không cần
            'CreatedBy'     => $user,
            'ModifiedDate'  => $now,
            'ModifiedBy'    => $user,
            'EditMode'      => 3, // Xóa
            'ComboCode'     => '',
            'ComboName'     => '',
            'NumberOfDate'  => 0,
            'TotalMeals'    => 0,
            'Description'   => '',
            'Status'        => 0,
        ];

        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/SaveData';
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->asJson()->post($apiUrl, [$payload]);
            $body = $response->json();

            if ($response->ok() && ($body['Success'] ?? false) === true) {
                return redirect()->route('combo')->with('success', 'Xóa combo thành công.');
            }

            return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($body)]);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception khi gọi API: ' . $e->getMessage()]);
        }
    }

}
