
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
                    <h5 class="mb-0 font-medium">Sửa thông tin khách hàng</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('khachhang') }}">Khách hàng</a></li>
                    <li class="breadcrumb-item" aria-current="page">Sửa thông tin khách hàng</li>
                </ul>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12 xl:col-span-9 md:col-span-9">
                <div class="card table-card">
                    <div class="card-header">
                        <h5>Sửa thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                    <form action="{{ route('khachhang.update', $customer['Id']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-12 gap-x-6">
                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="id" class="form-label">Mã khách hàng</label>
                                <input type="text" class="form-control" id="id" name="CustomerCode" 
                                    value="{{ $customer['CustomerCode'] }}" readonly>
                            </div>

                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="ten" class="form-label">Tên khách hàng</label>
                                <input type="text" class="form-control" id="ten" name="CustomerName" 
                                    value="{{ $customer['CustomerName'] }}" required>
                            </div>

                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="sdt" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="sdt" name="PhoneNumber" 
                                    value="{{ $customer['PhoneNumber'] }}" required>
                            </div>

                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="diachi" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="diachi" name="Address" 
                                    value="{{ $customer['Address'] }}">
                            </div>

                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="OrderType" class="form-label">Loại đơn hàng</label>
                                <select class="form-select" name="OrderType" id="OrderType">
                                    <option value="">-- Chọn --</option>
                                    <option value="1" {{ (isset($customer['OrderType']) && $customer['OrderType'] == 1) ? 'selected' : '' }}>Combo</option>
                                    <option value="2" {{ (isset($customer['OrderType']) && $customer['OrderType'] != 1) ? 'selected' : '' }}>Sản phẩm lẻ</option>
                                </select>
                            </div>

                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="giatien" class="form-label">Giá tiền</label>
                                <input type="text" class="form-control" id="giatien" name="ComboPrice" step="0.01"
                                    value="{{ $customer['ComboPrice'] }}">
                            </div>

                            <!-- <div class="col-span-12 xl:col-span-6 md:col-span-6" id="total-le-field">
                                <label for="TotalMealsPurchased" class="form-label">Tổng số xuất ăn</label>
                                <input type="text" class="form-control" id="TotalMealsPurchased" name="TotalMealsPurchased" 
                                    value="{{ $customer['TotalMealsPurchased'] }}">
                            </div> -->
                            <div class="col-span-12 xl:col-span-6 md:col-span-6" id="total-le-field">
                                <label for="TotalMealsPurchasedLe" class="form-label">Tổng số xuất ăn (Ăn lẻ)</label>
                                <input type="text" class="form-control" id="TotalMealsPurchasedLe" 
                                    value="{{ old('TotalMealsPurchased', $customer['TotalMealsPurchased'] ?? '') }}">
                            </div>

                            <!-- Combo fields -->
                            <div id="combo-fields" class="col-span-12 xl:col-span-6 md:col-span-6 {{ (isset($customer['OrderType']) && $customer['OrderType'] == 1) ? '' : 'hidden' }}">                              
                                <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                    <label for="goi" class="form-label">Gói đăng ký</label>
                                    <select class="form-select" name="ComboId" id="goi">
                                        @foreach($combos as $combo)
                                            <option value="{{ $combo['Id'] }}" 
                                                data-meals="{{ $combo['TotalMeals'] ?? 0 }}"
                                                {{ isset($customer['ComboId']) && $customer['ComboId'] == $combo['Id'] ? 'selected' : '' }}>
                                                {{ $combo['ComboName'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- <div class="col-span-12 xl:col-span-6 md:col-span-6" id="total-combo-field">
                                    <label for="allxuatancombo" class="form-label">Tổng số xuất ăn combo</label>
                                    <input type="text" class="form-control" id="allxuatancombo" name="TotalMealsPurchased" 
                                        value="{{ $customer['TotalMealsPurchased'] ?? '' }}">
                                </div> -->
                                <div class="col-span-12 xl:col-span-6 md:col-span-6" id="total-combo-field">
                                    <label for="TotalMealsPurchasedCombo" class="form-label">Tổng số xuất ăn (Combo)</label>
                                    <input type="text" class="form-control" id="TotalMealsPurchasedCombo" 
                                        value="{{ old('TotalMealsPurchased', $customer['TotalMealsPurchased'] ?? '') }}">
                                </div>

                                <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                    <label for="ngaybatdau" class="form-label">Ngày bắt đầu</label>
                                    <input type="date" class="form-control" id="ngaybatdau" name="StartDate" 
                                        value="{{ !empty($customer['StartDate']) ? \Carbon\Carbon::parse($customer['StartDate'])->format('Y-m-d') : '' }}">
                                </div>

                                <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                    <label for="ngayketthuc" class="form-label">Ngày kết thúc</label>
                                    <input type="date" class="form-control" id="ngayketthuc" name="EndDate" 
                                        value="{{ !empty($customer['EndDate']) ? \Carbon\Carbon::parse($customer['EndDate'])->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                            <input type="hidden" id="TotalMealsPurchased" name="TotalMealsPurchased" 
                                value="{{ old('TotalMealsPurchased', $customer['TotalMealsPurchased'] ?? '') }}">

                            <div class="col-span-12 xl:col-span-6 md:col-span-6">
                                <label for="trangthai" class="form-label">Trạng thái</label>
                                <select class="form-select" name="Status" id="trangthai">
                                    <option value="1" {{ $customer['Status']==1 ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ $customer['Status']==0 ? 'selected' : '' }}>Ngừng</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-st" style="text-align: right;">
                            <button type="submit" class="btn btn-success">Lưu</button>
                        </div>
                    </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
<style>
    .hidden {
  display: none !important;
}
</style>
<!-- <script>
    const priceInput = document.getElementById("giatien");

    priceInput.addEventListener("input", function () {
        // Bỏ ký tự không phải số
        let value = this.value.replace(/\D/g, "");
        
        if (value) {
            // Format VNĐ
            this.value = new Intl.NumberFormat("vi-VN").format(value);
        } else {
            this.value = "";
        }
    });
</script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
    function toggleComboFields() {
        let orderType = $('#OrderType').val();
        console.log("OrderType value:", orderType);

        if (parseInt(orderType) === 1) {
            $('#combo-fields').removeClass('hidden'); // bỏ hidden
        } else {
            $('#combo-fields').addClass('hidden');   // thêm hidden
        }
    }

    toggleComboFields(); // chạy khi load

    $('#OrderType').on('change', toggleComboFields); // chạy khi đổi
});



</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const orderType = document.getElementById("OrderType");
    const comboField = document.getElementById("total-combo-field");
    const leField = document.getElementById("total-le-field");
    const comboInput = document.getElementById("TotalMealsPurchasedCombo");
    const leInput = document.getElementById("TotalMealsPurchasedLe");
    const hiddenInput = document.getElementById("TotalMealsPurchased");
    const comboSelect = document.getElementById("goi");

    function syncValue() {
        if (orderType.value == "1") {
            hiddenInput.value = comboInput.value;
        } else if (orderType.value == "2") {
            hiddenInput.value = leInput.value;
        }
    }

    function toggleFields() {
        if (orderType.value == "1") {
            comboField.style.display = "block";
            leField.style.display = "none";
            comboInput.readOnly = true; // readonly để không sửa tay
            updateComboMeals(); // khi chuyển sang Combo thì set luôn
            syncValue();
        } else if (orderType.value == "2") {
            comboField.style.display = "none";
            leField.style.display = "block";
            comboInput.readOnly = false;
            syncValue();
        } else {
            comboField.style.display = "none";
            leField.style.display = "none";
        }
    }

    function updateComboMeals() {
        if (comboSelect.value) {
            let meals = comboSelect.selectedOptions[0].dataset.meals || 0;
            comboInput.value = meals;
            hiddenInput.value = meals;
        }
    }

    // Gán sự kiện
    orderType.addEventListener("change", toggleFields);
    comboInput.addEventListener("input", syncValue);
    leInput.addEventListener("input", syncValue);
    comboSelect.addEventListener("change", updateComboMeals);

    // Khởi tạo ban đầu
    toggleFields();
});



</script>
<!-- [ Main Content ] end -->
@endsection