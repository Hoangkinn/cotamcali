<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/Customer/SelectAll');
        $customer = $response->json();

        // Lấy mảng Data
        $customerList = $customer['Data'] ?? [];

        usort($customerList, function ($a, $b) {
            return $b['Id'] <=> $a['Id']; 
        });

        // --- Lọc dữ liệu ---
        $name      = $request->input('name');        // tên khách hàng
        $orderType = $request->input('order_type');  // 1=Combo, 2=Sản phẩm lẻ
        $status    = $request->input('status');      // 1=Hoạt động, 0=Ngừng

        $filtered = array_filter($customerList, function ($item) use ($name, $orderType, $status) {
            $match = true;

            if (!empty($name)) {
                $customerName = $item['CustomerName'] ?? '';
                $match = $match && stripos($customerName, $name) !== false;
            }

            if ($orderType !== null && $orderType !== '') {
                $match = $match && (string)$item['OrderType'] === (string)$orderType;
            }

            if ($status !== null && $status !== '') {
                $match = $match && (string)$item['Status'] === (string)$status;
            }

            return $match;
        });

        // --- Phân trang ---
        $page = $request->input('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $items = new \Illuminate\Support\Collection($filtered);

        $paginatedcustomer = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
                $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Customer/selectnewcode';
                $resNew = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
            ])->get($apiUrl);
                $body = $resNew->json();
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

        return view('khachhang.khachhang', ['customerList' => $paginatedcustomer,'newCode' => $newCode, 'notiList' => $notiList,
        'todayCount' => $todayCount,]);
    }


    public function create(Request $request) {
        $provinces = [];
        $districts = [];
        $communes  = [];
        $combos    = []; // Combo Types
        try {
            $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Customer/selectnewcode';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
        $body = $response->json();

        // Giả sử API trả về dạng { "Data": "COMBO123" }
        $newCode = $body['Data'] ?? '';

        } catch (\Exception $e) {
            $newCode = '';
        }

        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Province/SelectAll';
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
            $body = $response->json();
            
            if(!empty($body['Data'])) {
                $provinces = $body['Data'];
            }

        } catch (\Exception $e) {
            \Log::error("Lỗi API Province: " . $e->getMessage());
        }

        // --- Lấy combo types ---
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/SelectAll';
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
            $body = $response->json();
            
            if(!empty($body['Data'])) {
                $combos = $body['Data'];
            }

        } catch (\Exception $e) {
            \Log::error("Lỗi API ComboTypes: " . $e->getMessage());
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

        // $districts = [];
        // $communes = [];
        return view('khachhang.addkhachhang', compact('provinces', 'districts', 'communes', 'combos', 'newCode', 'notiList', 'todayCount'));
    }

    public function getDistricts($provinceId)
    {
        $districts = [];
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $url = "https://localhost:44390/api/District/selectByProviceId?id={$provinceId}";
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($url);
              $body = $response->json();
           if(!empty($body['Data'])) {
               $districts = $body['Data'];
            }
        } catch (\Exception $e) {
            \Log::error("Lỗi API District: " . $e->getMessage());
        }
 
        return response()->json($districts);
    }


    public function getCommunes($districtId)
    {
        $communes = [];
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $url = "https://localhost:44390/api/Commune/selectByDistricId?id={$districtId}";
            $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($url);
              $body = $response->json();
            if(!empty($body['Data'])) {
               $communes = $body['Data'];
            }
        } catch (\Exception $e) {
            \Log::error("Lỗi API Commune: " . $e->getMessage());
        }

        return response()->json($communes);
    }


    public function store(Request $request)
    {
        // $validatedData = $request->validate(
        //     [
        //         'CustomerCode'        => 'required|string|max:50',
        //         'CustomerName'        => 'required|string|max:255',

        //         'PhoneNumber'         => ['bail','required','regex:/^(0|\+84)\d{9}$/'],

        //         'Address'             => 'nullable|string|max:255',
        //         'Gender'              => 'required|integer|in:0,1,2',
        //         'OrderType'           => 'required|integer|in:1,2',
        //         'ComboId'             => 'nullable|integer',
        //         'ComboPrice'          => 'nullable|integer|min:0',
        //         'TotalMealsPurchased' => 'nullable|integer|min:0',
        //         'StartDate'           => 'nullable|date',
        //         'EndDate'             => 'nullable|date|after_or_equal:StartDate',
        //         'Note'                => 'nullable|string',
        //         'Status'              => 'required|integer|in:0,1',
        //         'ProvinceId'          => 'nullable|integer',
        //         'DistrictId'          => 'nullable|integer',
        //         'CommuneId'           => 'nullable|integer',
        //     ],
        //     [
        //         'CustomerCode.required' => 'Mã khách hàng không được để trống',
        //         'CustomerName.required' => 'Tên khách hàng không được để trống',

        //         'PhoneNumber.required'  => 'Số điện thoại không được để trống',
        //         'PhoneNumber.regex'     => 'Số điện thoại không hợp lệ (dạng 0xxxxxxxxx hoặc +84xxxxxxxxx)',

        //         'Gender.required'       => 'Vui lòng chọn giới tính',
        //         'Gender.in'             => 'Giới tính không hợp lệ',
        //         'OrderType.required'    => 'Vui lòng chọn loại đơn hàng',
        //         'OrderType.in'          => 'Loại đơn hàng không hợp lệ',
        //         'EndDate.after_or_equal'=> 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
        //         'Status.required'       => 'Vui lòng chọn trạng thái',
        //         'Status.in'             => 'Trạng thái không hợp lệ',
        //     ]
        // );

        $payload = [
            "CustomerCode"        => (string) $request->CustomerCode,
            "CustomerName"        => (string) $request->CustomerName,
            "PhoneNumber"         => (string) $request->PhoneNumber,
            "Address"             => (string) $request->Address,
            "Gender"              => (int) $request->Gender,
            "OrderType"           => (int) $request->OrderType,
            "ComboId"             => $request->ComboId ? (int) $request->ComboId : 0,
            "ComboPrice"          => (int) $request->ComboPrice,
            "TotalMealsPurchased" => $request->TotalMealsPurchased ? (int) $request->TotalMealsPurchased : 0,
            "StartDate"           => $request->StartDate ? date('c', strtotime($request->StartDate)) : null, // ISO 8601
            "EndDate"             => $request->EndDate ? date('c', strtotime($request->EndDate)) : null,
            "Note"                => $request->Note,
            "Status"              => (int) $request->Status,
            "ProvinceId"          => (int) $request->ProvinceId,
            "DistrictId"          => (int) $request->DistrictId,
            "CommuneId"           => (int) $request->CommuneId,
            "EditMode"            => 1,
                ];
        // dd($payload);


        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Customer/SaveData';
        try {
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
        //     dd([
        //     'payload_sent' => $payload,
        //     'status'       => $response->status(),
        //     'response'     => $body,
        // ]);
           
            if ($response->ok()) {
                if (isset($body['Success']) && $body['Success'] === true) {
                
                    return redirect()->route('khachhang')->with('success', 'Thêm mới khachhang thành công.');
                }

                return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($body)]);
            }

            return back()->withErrors(['api' => 'Lỗi kết nối API. HTTP status: ' . $response->status() . ' — ' . $response->body()]);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception khi gọi API: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        // Lấy danh sách khách hàng
        $selectUrl = $baseUrl . '/api/Customer/SelectAll';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($selectUrl);
        $body = $response->json();

        if (!$response->ok() || empty($body['Data'])) {
            return back()->withErrors(['api' => 'Không lấy được dữ liệu khách hàng từ API']);
        }

        // Tìm khách hàng theo Id
        $customer = collect($body['Data'])->firstWhere('Id', (int)$id);
        if (!$customer) {
            return back()->withErrors(['api' => 'Không tìm thấy khách hàng với ID = ' . $id]);
        }
        
        $combos    = []; // Combo Types
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
                $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/ComboTypes/SelectAll';
                $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
                $body = $response->json();
                
                if(!empty($body['Data'])) {
                    $combos = $body['Data'];
                }

            } catch (\Exception $e) {
                \Log::error("Lỗi API ComboTypes: " . $e->getMessage());
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

        return view('khachhang.editkhachhang', compact('customer', 'combos', 'notiList', 'todayCount'));
    }

    public function update(Request $request, $id)
    {
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';
        $now = Carbon::now()->toIso8601String();
        $user = auth()->check() ? auth()->user()->name : 'system';

        //gọi combo
        $combos = [];
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $apiUrl = $baseUrl . '/api/ComboTypes/SelectAll';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
        $body = $response->json();
        if (!empty($body['Data'])) {
            $combos = $body['Data'];
        }
        $comboId = (int)$request->ComboId;
        $comboName = null;
        $selectedCombo = collect($combos)->firstWhere('Id', $comboId);
        if ($selectedCombo) {
            $comboName = $selectedCombo['ComboName'];
        }

        // Lấy lại data gốc từ SelectAll
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $selectUrl = $baseUrl . '/api/Customer/SelectAll';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($selectUrl);
        $body = $response->json();

        if (!$response->ok() || empty($body['Data'])) {
            return back()->withErrors(['api' => 'Không lấy được dữ liệu khách hàng từ API']);
        }

        $customer = collect($body['Data'])->firstWhere('Id', (int)$id);
        if (!$customer) {
            return back()->withErrors(['api' => 'Không tìm thấy khách hàng']);
        }

        // Ghi đè dữ liệu từ form vào $customer
        $payload = [
            "CustomerCode"   => $request->CustomerCode ?? $customer['CustomerCode'],
            "CustomerName"   => $request->CustomerName ?? $customer['CustomerName'],
            "PhoneNumber"    => $request->PhoneNumber ?? $customer['PhoneNumber'],
            "Address"        => $request->Address ?? $customer['Address'],
            "Gender"         => $request->Gender !== null ? (int)$request->Gender : $customer['Gender'],
            "OrderType"      => $request->OrderType !== null ? (int)$request->OrderType : $customer['OrderType'],
            "Status"         => $request->Status !== null ? (int)$request->Status : $customer['Status'],
            "ProvinceId"     => $request->ProvinceId ?? $customer['ProvinceId'],
            "DistrictId"     => $request->DistrictId ?? $customer['DistrictId'],
            "CommuneId"      => $request->CommuneId ?? $customer['CommuneId'],
            "Id"             => (int)$id,
            "EditMode"       => 2,
            "ModifiedBy"     => "system",
            "MealsUsed"      => $request->CommuneId ?? $customer['MealsUsed'],
        ];

        // Nếu là Combo (OrderType = 1)
        if ($payload["OrderType"] == 1) {
            $payload["ComboId"]             = $comboId ?? ($customer['ComboId'] ?? null);
            $payload["ComboName"]           = $comboName ?? ($customer['ComboName'] ?? null);
            $payload["ComboPrice"]          = $request->ComboPrice ?? ($customer['ComboPrice'] ?? null);
            $payload["TotalMealsPurchased"] = $request->TotalMealsPurchased ?? ($customer['TotalMealsPurchased'] ?? null);
            $payload["MealsRemaining"]      = $request->MealsRemaining ?? ($customer['MealsRemaining'] ?? null);
            $payload["StartDate"]           = $request->StartDate ?? ($customer['StartDate'] ?? null);
            $payload["EndDate"]             = $request->EndDate ?? ($customer['EndDate'] ?? null);
        }

        // Nếu là Sản phẩm lẻ (OrderType = 2)
        if ($payload["OrderType"] == 2) {
            $payload["ProductId"]            = $request->ProductId ?? ($customer['ProductId'] ?? null);
            $payload["TotalMealsPurchased"]  = $request->TotalMealsPurchased ?? ($customer['TotalMealsPurchased'] ?? null);
            $payload["MealsRemaining"]       = $request->MealsRemaining ?? ($customer['MealsRemaining'] ?? null);
            $payload["ComboPrice"]           = $request->ComboPrice ?? ($customer['ComboPrice'] ?? null);
        }

        // dd($payload);
        // Gửi lên SaveData
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $apiUrl = $baseUrl . '/api/Customer/SaveData';
        $saveResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->asJson()->post($apiUrl, [$payload]);
        $saveBody = $saveResponse->json();
        // dd($saveBody);

        if ($saveResponse->ok() && ($saveBody['Success'] ?? false) === true) {
            return redirect()->route('khachhang')->with('success', 'Cập nhật khách hàng thành công.');
        }

        return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($saveBody)]);
    }

    
    public function destroy($id)
    {
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';

        try {
            // 1️⃣ Lấy tất cả khách hàng
            $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
            $allResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($baseUrl . '/api/Customer/SelectAll');
            $allCustomers = $allResponse->json()['Data'] ?? [];

            // 2️⃣ Tìm khách hàng theo ID
            $customer = collect($allCustomers)->firstWhere('Id', (int)$id);
            if (!$customer) {
                return back()->withErrors(['api' => 'Không tìm thấy khách hàng với ID = ' . $id]);
            }

            // 3️⃣ Build payload để xoá
            $payload = [
                "CustomerCode"        => $customer['CustomerCode'] ?? '',
                "CustomerName"        => $customer['CustomerName'] ?? '',
                "PhoneNumber"         => $customer['PhoneNumber'] ?? '',
                "Address"             => $customer['Address'] ?? '',
                "Gender"              => (int)$customer['Gender'] ?? '',
                "OrderType"           => (int)$customer['OrderType'] ?? '',
                "ComboId"             => isset($customer['ComboId']) ? (int)$customer['ComboId'] : 0,
                "ComboName"           => $customer['ComboName']  ?? '',
                "ProductId"           => isset($customer['ProductId']) ? (int)$customer['ProductId'] : 0,
                "ComboPrice"          => isset($customer['ComboPrice']) ? (float)$customer['ComboPrice'] : 0,
                "TotalMealsPurchased" => isset($customer['TotalMealsPurchased']) ? (int)$customer['TotalMealsPurchased'] : 0,
                "MealsRemaining"      => isset($customer['MealsRemaining']) ? (int)$customer['MealsRemaining'] : 0,
                "StartDate"      => $customer['StartDate'] ?? now()->toDateString(),
                "EndDate"        => $customer['EndDate'] ?? now()->toDateString(),
                "Status"              => (int)$customer['Status'],
                "ProvinceId"          => isset($customer['ProvinceId']) ? (int)$customer['ProvinceId'] : 0,
                "ProvinceName"             => $customer['ProvinceName']  ?? '',
                "DistrictId"          => isset($customer['DistrictId']) ? (int)$customer['DistrictId'] : 0,
                "DistrictName"             => $customer['DistrictName']  ?? '',
                "CommuneId"           => isset($customer['CommuneId']) ? (int)$customer['CommuneId'] : 0,
                "CommuneName"             => $customer['CommuneName']  ?? '',
                "Id"                  => (int)$id,
                "EditMode"            => 3, // Xoá
                "ModifiedBy"          => "system",
            ];
            // dd($payload);

            // 4️⃣ Gọi API SaveData (giả sử backend có)
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $apiUrl = $baseUrl . '/api/Customer/SaveData';
            $deleteResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->asJson()->post($apiUrl, [$payload]);
            $body = $deleteResponse->json();
            // dd($body);

            if ($deleteResponse->ok() && ($body['Success'] ?? true) === true) {
                return redirect()->route('khachhang')->with('success', 'Xoá khách hàng thành công.');
            }

            return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($body)]);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception khi gọi API: ' . $e->getMessage()]);
        }
    }

}
