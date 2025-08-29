@extends('layouts.app')

@section('content')
<!-- [ Sidebar Menu ] start -->

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header flex items-center py-4 px-6 h-header-height">
            <a href="{{ url('/') }}" class="b-brand flex items-center gap-3">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/images/user/logo.png') }}"
                    class="img-fluid logo logo" alt="logotrêt" width="150" />
                <img src="{{ asset('assets/images/user/logo.png') }}"
                    class="img-fluid logo logo-sm" alt="logo" />
            </a>
        </div>
        <div class="navbar-content h-[calc(100vh_-_74px)] py-2.5">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                <label>Website nội bộ</label>
                    <i data-feather="monitor"></i>
                </li>

                <li class="pc-item">
                    <a href="{{ url('/') }}" class="pc-link">
                        <span class="pc-micon">
                            <i data-feather="home"></i>
                        </span>
                        <span class="pc-mtext">Trang chủ</span>
                    </a>
                </li>

                    @php
                        $user = session('user');
                        $role = $user['Roles'] ?? null;
                    @endphp

                    {{-- Quản lý (role 1,2) --}}
                    @if(in_array($role, [1,2]))
                        <li class="pc-item pc-caption">
                            <label>Quản lý</label>
                            <i data-feather="monitor"></i>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="{{ route('khachhang') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="users"></i></span>
                                <span class="pc-mtext">Khách hàng</span>
                            </a>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="{{ route('combo') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="package"></i></span>
                                <span class="pc-mtext">Combo</span>
                            </a>
                        </li>
                    @endif

                    {{-- Đơn hàng / Vận chuyển / Lịch sử (role 1,2,3) --}}
                    @if(in_array($role, [1,2,3]))
                        <li class="pc-item pc-caption">
                            <label>Giao hàng</label>
                            <i data-feather="truck"></i>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="{{ route('donhang') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="shopping-bag"></i></span>
                                <span class="pc-mtext">Đơn hàng</span>
                            </a>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="{{ route('vanchuyen') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="truck"></i></span>
                                <span class="pc-mtext">Vận chuyển</span>
                            </a>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="{{ route('lichsu') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="clock"></i></span>
                                <span class="pc-mtext">Lịch sử</span>
                            </a>
                        </li>
                    @endif

                    {{-- Nhân viên (chỉ role 1) --}}
                    @if($role == 1)
                        <li class="pc-item pc-caption">
                            <label>Nhân sự</label>
                            <i data-feather="user"></i>
                        </li>

                        <li class="pc-item pc-hasmenu">
                            <a href="{{ route('account') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="user"></i></span>
                                <span class="pc-mtext">Nhân viên</span>
                            </a>
                        </li>
                    @endif



            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper flex max-sm:px-[15px] px-[25px] grow">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse max-lg:hidden lg:inline-flex">
                    <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="sidebar-hide">
                        <i data-feather="menu"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup lg:hidden">
                    <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="mobile-collapse">
                        <i data-feather="menu"></i>
                    </a>
                </li>
                <!-- <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <i data-feather="search"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search">
                        <form class="px-2 py-1">
                            <input type="search" class="form-control !border-0 !shadow-none"
                                placeholder="Search here. . ." />
                        </form>
                    </div>
                </li> -->
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <i data-feather="sun"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                            <i data-feather="moon"></i>
                            <span>Dark</span>
                        </a>
                        <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                            <i data-feather="sun"></i>
                            <span>Light</span>
                        </a>
                    </div>
                </li>
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <i data-feather="bell"></i>
                        <span class="badge bg-success-500 text-white rounded-full z-10 absolute right-0 top-0">
                            {{ $todayCount }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown p-2">
                        <div class="dropdown-header flex items-center justify-between py-4 px-5">
                            <h5 class="m-0">Thông báo</h5>
                            <a href="#!" class="btn btn-link btn-sm">Đơn hàng mới</a>
                        </div>
                        <div class="dropdown-body header-notification-scroll relative py-4 px-5"
                            style="max-height: calc(100vh - 215px)">
                            <p class="text-span mb-3">Hôm nay</p>
                            @foreach($notiList as $index => $noti)
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="flex gap-4">
                                        <div class="shrink-0">
                                            <img class="img-radius w-12 h-12 rounded-0"
                                                src="{{ asset('assets/images/user/avatar-1.jpg') }}"
                                                alt="Generic placeholder image" />
                                        </div>
                                        <div class="grow">
                                            <span class="float-end text-sm text-muted" style="width:100%; padding-bottom: 5px;color: #ff0000;font-weight: 600;">
                                                {{ \Carbon\Carbon::parse($noti['CreatedDate'])->format('H:i \n\g\à\y d/m/Y') }}
                                            </span>

                                            <p class="mb-0">
                                                {{ $noti['Message'] }} 
                                                @if(!empty($noti['ActionDescription']))
                                                    - <b>{{ $noti['ActionDescription'] }}</b>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </li>
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-pc-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-pc-auto-close="outside" aria-expanded="false">
                        <i data-feather="user"></i>
                    </a>
                    <div
                        class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown p-2 overflow-hidden">
                        <div class="dropdown-header flex items-center justify-between py-4 px-5 bg-primary-500">
                            <div class="flex mb-1 items-center">
                                <div class="shrink-0">
                                    <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image"
                                        class="w-10 rounded-full" />
                                </div>
                                @php
                                    $roles = [
                                        1 => 'Quản trị viên',
                                        2 => 'Nhân viên',
                                        3 => 'Giao hàng',
                                    ];

                                    $user = session('user');
                                @endphp

                                <div class="grow ms-3">
                                    <h6 class="mb-1 text-white">{{ $user['Name'] ?? 'Khách' }}</h6>
                                    <span class="text-white">{{ $roles[$user['Roles']] ?? 'Không xác định' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-body py-4 px-5">
                            <div class="profile-notification-scroll position-relative"
                                style="max-height: calc(100vh - 225px)">
                                <a href="#" class="dropdown-item">
                                    <span>
                                        <svg class="pc-icon text-muted me-2 inline-block">
                                            <use xlink:href="#custom-setting-outline"></use>
                                        </svg>
                                        <span>Tài khoản</span>
                                    </span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <span>
                                        <svg class="pc-icon text-muted me-2 inline-block">
                                            <use xlink:href="#custom-lock-outline"></use>
                                        </svg>
                                        <span>Quên mật khẩu</span>
                                    </span>
                                </a>
                                <div class="grid my-3">
                                    <form method="POST" action="{{ route('logout') }}" style="width:100%">
                                        @csrf
                                        <button class="btn btn-primary flex items-center justify-center" style="width:100%">
                                            <svg class="pc-icon me-2 w-[22px] h-[22px]">
                                                <use xlink:href="#custom-logout-1-outline"></use>
                                            </svg>    
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="page-header-title">
                    <h5 class="mb-0 font-medium">Trang chủ</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item" aria-current="page">Vận chuyển</li>
                </ul>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12 xl:col-span-9 md:col-span-9">
                <div class="card table-card">
                    <div class="card-header">
                        <h5>Đơn hàng cần giao hôm nay</h5>
                        <!-- <button class="btn btn-primary">+ Thêm mới</button> -->
                        <div class="filter-container">
                            <!-- Tìm theo tên -->
                            <form method="GET" action="{{ route('vanchuyen') }}">
                                <input type="text" name="name" placeholder="Tìm kiếm tên khách hàng" value="{{ request('name') }}" style="width:300px">

                                <select name="status">
                                    <option value="" {{ !request()->filled('status') ? 'selected' : '' }}>Trạng thái</option>
                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Giao hàng</option>
                                    <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Đã giao</option>
                                    <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Khách hủy</option>
                                </select>
                                <button type="submit">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã đơn hàng</th>
                                        <th>Tên shipper</th>
                                        <th>Tên khách hàng</th>
                                        <!-- <th>Số điện thoại</th> -->
                                        <th>Địa chỉ</th>
                                        <th>Số lượng xuất ăn</th>
                                        <th>Giá tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($transportList as $index => $transport)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transport['OrderCode'] ?? '' }}</td>
                                    <td>{{ $transport['AccountName'] ?? '' }}</td>
                                    <td>{{ $transport['CustomerName'] ?? '' }}</td>
                                    <td>{{ $transport['Address'] ?? '' }} - {{ $transport['CommuneName'] ?? '' }} - {{ $transport['DistrictName'] ?? '' }} - {{ $transport['ProvinceName'] ?? '' }}</td>
                                    <td>{{ $transport['Quantity'] ?? '' }}</td>
                                    <td>{{ $transport['Price'] ?? '' }}</td>
                                    <!-- <td>
                                        @if(($transport['Status'] ?? 0) == 1)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Ngừng</span>
                                        @endif
                                    </td> -->
                                    <td>
                                        <!-- <button type="button" class="btn btn-sm btn-info btn-show-detail">Chi tiết</button> -->
                                        <!-- <button type="button"
                                            class="btn btn-sm
                                                {{ ($transport['DeliveryStatus'] ?? 0) == 2 ? 'btn-warning' :
                                                (($transport['DeliveryStatus'] ?? 0) == 3 ? 'btn-success' :
                                                (($transport['DeliveryStatus'] ?? 0) == 4 ? 'btn-danger' : 'btn-secondary')) }}
                                                btn-update-status"
                                            data-order-id="{{ $transport['OrderId'] ?? 0 }}"
                                            data-status="{{ $transport['DeliveryStatus'] ?? 0 }}">
                                            {{ ($transport['DeliveryStatus'] ?? 0) == 2 ? 'Giao hàng' :
                                            (($transport['DeliveryStatus'] ?? 0) == 3 ? 'Đã giao' :
                                            (($transport['DeliveryStatus'] ?? 0) == 4 ? 'Khách hủy' : 'Sắp xếp')) }}
                                        </button> -->
                                        <button type="button"
                                            class="btn btn-sm 
                                            @if(($transport['DeliveryStatus'] ?? 0) == 2) btn-warning
                                            @elseif(($transport['DeliveryStatus'] ?? 0) == 3) btn-success
                                            @elseif(($transport['DeliveryStatus'] ?? 0) == 4) btn-danger
                                            @else btn-secondary @endif
                                            btn-update-status"
                                            data-assignment-id="{{ $transport['Id']}}"
                                            data-status="{{ $transport['DeliveryStatus'] ?? 0 }}"
                                            @if(($transport['DeliveryStatus'] ?? 0) == 3) disabled @endif
                                        >
                                            @if(($transport['DeliveryStatus'] ?? 0) == 2)
                                                Giao hàng
                                            @elseif(($transport['DeliveryStatus'] ?? 0) == 3)
                                                Đã giao
                                            @elseif(($transport['DeliveryStatus'] ?? 0) == 4)
                                                Khách hủy
                                            @else
                                                Sắp xếp
                                            @endif
                                        
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9" style="padding-top: 30px; text-align: right;">
                                            <div class="pagination">
                                                {{-- Nút trước --}}
                                                @if ($transportList->onFirstPage())
                                                    <span class="btn disabled">◀</span>
                                                @else
                                                    <a href="{{ $transportList->previousPageUrl() }}" class="btn">◀</a>
                                                @endif

                                                {{-- Các số trang --}}
                                                @foreach ($transportList->getUrlRange(1, $transportList->lastPage()) as $page => $url)
                                                    @if ($page == $transportList->currentPage())
                                                        <span class="btn active">{{ $page }}</span>
                                                    @else
                                                        <a href="{{ $url }}" class="btn">{{ $page }}</a>
                                                    @endif
                                                @endforeach

                                                {{-- Nút sau --}}
                                                @if ($transportList->hasMorePages())
                                                    <a href="{{ $transportList->nextPageUrl() }}" class="btn">▶</a>
                                                @else
                                                    <span class="btn disabled">▶</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>

                                <style>
                                    .pagination {
                                        text-align: center;
                                    }
                                    .pagination .btn {
                                        display: inline-block;
                                        padding: 5px 12px;
                                        margin: 0 2px;
                                        background: #007bff;
                                        color: white;
                                        text-decoration: none;
                                        border-radius: 4px;
                                    }
                                    .pagination .btn:hover {
                                        background: #0056b3;
                                    }
                                    .pagination .btn.active {
                                        background: #1de9b6;
                                    }
                                    .pagination .btn.disabled {
                                        background: #ccc;
                                        pointer-events: none;
                                    }
                                </style>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>

<form id="updateStatusForm" method="POST" action="{{ route('orders.updateStatus') }}" style="display:none;">
    @csrf
    <input type="hidden" name="assignment_id" id="form_assignment_id">
    <input type="hidden" name="status" id="form_status">
</form>
<!-- Popup -->
<div id="statusPopup" style="display:none; position:fixed; top:30%; left:50%; transform:translate(-50%, -30%);
     border-radius:10px; z-index:1000;">
    <div class="popup-content">
        <h5 style="margin-bottom: 20px">Chọn trạng thái đơn hàng</h5>
        <select id="statusSelect" class="form-select mt-2">
            <option value="2">Giao hàng</option>
            <option value="3">Đã giao</option>
            <option value="4">Khách hủy</option>
        </select>
        <div class="mt-3 text-end" style="text-align: right;">
            <button type="button" id="cancelPopup" class="btn btn-secondary btn-sm">Hủy</button>
            <button type="button" id="confirmPopup" class="btn btn-primary btn-sm">Xác nhận</button>
        </div>
    </div>
</div>

                            <style>
                                .popup-overlay {
                                    position: fixed;
                                    top: 0; left: 0; right: 0; bottom: 0;
                                    background: rgba(0, 0, 0, 0.5);
                                    display: none;
                                    justify-content: center;
                                    align-items: center;
                                    z-index: 9999;
                                    animation: fadeIn 0.2s ease-in-out;
                                }
                                .popup-content {
                                        background: #fff;
                                        padding: 25px 20px;
                                        border-radius: 12px;
                                        width: 450px;
                                        max-width: 95%;
                                        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
                                        animation: slideUp 0.25s ease;
                                }
    
                                /* Title */
                                .popup-content h3 {
                                    margin: 0 0 15px;
                                    font-size: 18px;
                                    font-weight: 600;
                                    color: #333;
                                }
                                #statusSelect {
                                    width: 100%;
                                    padding: 10px;
                                    border: 1px solid #ddd;
                                    border-radius: 6px;
                                    font-size: 15px;
                                    margin-bottom: 20px;
                                    transition: border 0.2s;
                                }

                                #statusSelect:focus {
                                    outline: none;
                                    border-color: #28a745;
                                    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.15);
                                }

                                /* Action buttons */
                                .popup-actions {
                                    display: flex;
                                    justify-content: flex-end;
                                    gap: 10px;
                                }

                                .popup-actions button {
                                    padding: 8px 16px;
                                    border: none;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    font-size: 14px;
                                    font-weight: 500;
                                    transition: background 0.2s;
                                }

                                #confirmStatusBtn {
                                    background: #28a745;
                                    color: #fff;
                                }
                                #confirmStatusBtn:hover {
                                    background: #218838;
                                }

                                #closePopupBtn {
                                    background: #e0e0e0;
                                    color: #333;
                                }
                                #closePopupBtn:hover {
                                    background: #c7c7c7;
                                }

                                /* Animations */
                                @keyframes fadeIn {
                                    from {opacity: 0;} to {opacity: 1;}
                                }
                                @keyframes slideUp {
                                    from {transform: translateY(20px); opacity: 0;}
                                    to {transform: translateY(0); opacity: 1;}
                                }
                                .popup-actions { margin-top: 15px; text-align: right; }
                                .popup-actions button { margin-left: 5px; padding: 6px 12px; cursor: pointer; }
                                .btn-success { background:#28a745;color:#fff; }
                                .btn-warning { background:#ffc107;color:#000; }
                                .btn-danger { background:#dc3545;color:#fff; }
                                .btn-secondary { background:#6c757d;color:#fff; }
                            </style>

<script>
let currentOrderId = null;
    let popup = document.getElementById("statusPopup");
    let statusSelect = document.getElementById("statusSelect");

    document.querySelectorAll(".btn-update-status").forEach(btn => {
    btn.addEventListener("click", function() {
        currentOrderId = this.dataset.assignmentId; // <-- đổi thành assignmentId
        popup.style.display = "block";
    });
});

    document.getElementById("cancelPopup").addEventListener("click", function() {
        popup.style.display = "none";
    });

    document.getElementById("confirmPopup").addEventListener("click", function() {
        if (currentOrderId) {
            document.getElementById("form_assignment_id").value = currentOrderId;
            document.getElementById("form_status").value = statusSelect.value;
            document.getElementById("updateStatusForm").submit();
        }
    });
</script>



<!-- [ Main Content ] end -->
@endsection