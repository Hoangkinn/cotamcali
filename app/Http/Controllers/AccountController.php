<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get('https://localhost:44390/api/Account/SelectAll');
        $account = $response->json();
        // Lấy mảng Data
        $accountList = $account['Data'] ?? [];
        
        usort($accountList, function ($a, $b) {
            return $b['Id'] <=> $a['Id']; 
        });

        // --- Lọc dữ liệu ---
        $name      = $request->input('name');        // tên khách hàng
        $status    = $request->input('status');      

        $filtered = array_filter($accountList, function ($item) use ($name, $status) {
            $match = true;

            if (!empty($name)) {
                $customerName = $item['Name'] ?? '';
                $match = $match && stripos($customerName, $name) !== false;
            }

            if ($status !== null && $status !== '') {
                $deliveryStatus = $item['Roles'] ?? null;
                $match = $match && (string)$deliveryStatus === (string)$status;
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
        $paginatedaccount = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // --- Gọi API lấy new code ---
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Account/selectnewcode';
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

        return view('account.account', [
            'accountList' => $paginatedaccount,
            'newCode'     => $newCode,
            'notiList'    => $notiList,
            'todayCount'  => $todayCount,
        ]);
    }

    public function create(Request $request) {
        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Account/selectnewcode';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($apiUrl);
        $body = $response->json();
// dd([
//         'token' => session('token'),
//         'status' => $response->status(),
//         'headers' => $response->headers(),
//         'body' => $response->json(),
//     ]);
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

        return view('account.addaccount', compact('newCode', 'notiList', 'todayCount'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'Name'        => 'required|string|max:255',
                'DateOfBirth' => 'required|date',
                'PhoneNumber' => 'required|string|max:20',
                'Address'     => 'nullable|string',
                'Email'       => 'required|email',
                'Password'    => 'required|string|min:6',
                'Gender'      => 'required|integer',
                'Roles'       => 'required|integer',
            ],
            [
                'Name.required'        => 'Tên người dùng không được để trống',
                'DateOfBirth.required' => 'Ngày sinh không được để trống',
                'PhoneNumber.required' => 'Số điện thoại không được để trống',
                'PhoneNumber.max'      => 'Số điện thoại không được vượt quá 20 ký tự',
                'Email.required'       => 'Email không được để trống',
                'Email.email'          => 'Email không đúng định dạng',
                'Password.required'    => 'Vui lòng nhập mật khẩu',
                'Password.min'         => 'Mật khẩu phải có ít nhất 6 ký tự',
                'Gender.required'      => 'Vui lòng chọn giới tính',
                'Roles.required'       => 'Vui lòng chọn vai trò',
            ]
        );

        $payload = [
                "Id" => 0,
                "CreatedDate" => now()->toISOString(),
                "CreatedBy" => "admin",
                "ModifiedDate" => now()->toISOString(),
                "ModifiedBy" => "admin",
                "EditMode" => 1, // 1 = thêm mới
                "Code" => $request->Code,
                "Name" => $request->Name,
                "DateOfBirth" => $request->DateOfBirth,
                "PhoneNumber" => $request->PhoneNumber,
                "Address" => $request->Address,
                "Email" => $request->Email,
                "Password" => md5($request->Password), // mã hóa MD5
                "Gender" => (int) $request->Gender,
                "Status" => 1,
                "Roles" => (int) $request->Roles
            ];
        // dd($payload);


        $apiUrl = (env('API_BASE_URL') ?? 'https://localhost:44390') . '/api/Account/SaveData';
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
                
                    return redirect()->route('account')->with('success', 'Thêm mới Account thành công.');
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
        $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';

        // Lấy danh sách khách hàng
        $selectUrl = $baseUrl . '/api/Account/SelectAll';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($selectUrl);
        $body = $response->json();

        if (!$response->ok() || empty($body['Data'])) {
            return back()->withErrors(['api' => 'Không lấy được dữ liệu khách hàng từ API']);
        }

        // Tìm khách hàng theo Id
        $account = collect($body['Data'])->firstWhere('Id', (int)$id);
        if (!$account) {
            return back()->withErrors(['api' => 'Không tìm thấy khách hàng với ID = ' . $id]);
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
        

        return view('account.editaccount', compact('account', 'notiList', 'todayCount'));
    }

    public function update(Request $request, $id)
    {
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';
        $now = Carbon::now()->toIso8601String();
        $user = auth()->check() ? auth()->user()->name : 'system';

        // Lấy lại data gốc từ SelectAll
        $selectUrl = $baseUrl . '/api/Account/SelectAll';
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($selectUrl);
        $body = $response->json();

        if (!$response->ok() || empty($body['Data'])) {
            return back()->withErrors(['api' => 'Không lấy được dữ liệu tài khoản từ API']);
        }

        $account = collect($body['Data'])->firstWhere('Id', (int)$id);
        if (!$account) {
            return back()->withErrors(['api' => 'Không tìm thấy tài khoản']);
        }

        $payload = [
            "Id"          => (int)$id,
            "Name"        => $request->Name ?? $account['Name'],
            "DateOfBirth" => $request->DateOfBirth ?? $account['DateOfBirth'],
            "PhoneNumber" => $request->PhoneNumber ?? $account['PhoneNumber'],
            "Address"     => $request->Address ?? $account['Address'],
            "Code"        => $request->Code ?? $account['Code'],
            "Email"       => $request->Email ?? $account['Email'],
            "Password" => md5($request->Password), // mã hóa MD5
            "Gender"      => $request->Gender !== null ? (int)$request->Gender : $account['Gender'],
            "Roles"       => $request->Roles !== null ? (int)$request->Roles : $account['Roles'],
            "EditMode"    => 2, 
            "ModifiedBy"  => $user,
            "ModifiedDate"=> $now,
            "Status" => 1,
        ];

        // dd($payload);
        // Gửi lên SaveData
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
        }
        $apiUrl = $baseUrl . '/api/Account/SaveData';
        $saveResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->asJson()->post($apiUrl, [$payload]);
        $saveBody = $saveResponse->json();
        // dd($saveBody);

        if ($saveResponse->ok() && ($saveBody['Success'] ?? false) === true) {
            return redirect()->route('account')->with('success', 'Cập nhật tài khoản thành công.');
        }

        return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($saveBody)]);
    }

    
    public function destroy($id)
    {
        $baseUrl = env('API_BASE_URL') ?? 'https://localhost:44390';

        try {
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            // 1️⃣ Lấy tất cả account
            $allResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => session('token'),
        ])->get($baseUrl . '/api/Account/SelectAll');
            $allAccounts = $allResponse->json()['Data'] ?? [];

            // 2️⃣ Tìm account theo ID
            $account = collect($allAccounts)->firstWhere('Id', (int)$id);
            if (!$account) {
                return back()->withErrors(['api' => 'Không tìm thấy account với ID = ' . $id]);
            }

            // 3️⃣ Build payload để xoá (EditMode = 3)
            $payload = [
                "Id"            => (int)$id,
                "Username"      => $account['Username'] ?? '',
                "Password"      => $account['Password'] ?? '',
                "Email"         => $account['Email'] ?? '',
                "PhoneNumber"   => $account['PhoneNumber'] ?? '',
                "FullName"      => $account['FullName'] ?? '',
                "RoleId"        => isset($account['RoleId']) ? (int)$account['RoleId'] : 0,
                "Status"        => isset($account['Status']) ? (int)$account['Status'] : 1,
                "CreatedDate"   => $account['CreatedDate'] ?? now()->toIso8601String(),
                "ModifiedDate"  => now()->toIso8601String(),
                "ModifiedBy"    => "system",
                "EditMode"      => 3, // Xoá
            ];
            // dd($payload);

            // 4️⃣ Gọi API SaveData cho Account
            $token = session('token');

            if (!$token) {
                return redirect()->route('login')->withErrors('Tài khoản của bạn đữ hết hạn đăng nhập. Vui lòng đăng nhập lại');
            }
            $apiUrl = $baseUrl . '/api/Account/SaveData';
            $deleteResponse = Http::withoutVerifying()->asJson()->post($apiUrl, [$payload]);
            $body = $deleteResponse->json();
            // dd($body);

            if ($deleteResponse->ok() && ($body['Success'] ?? false) === true) {
                return redirect()->route('account')->with('success', 'Xoá account thành công.');
            }

            return back()->withErrors(['api' => 'API trả về lỗi: ' . json_encode($body)]);
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Exception khi gọi API: ' . $e->getMessage()]);
        }
    }


}
