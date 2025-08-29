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


                {{-- Phần login vẫn giữ nguyên --}}
                <li class="pc-item pc-caption">
                    <label>Pages</label>
                    <i data-feather="monitor"></i>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="{{ route('login') }}" class="pc-link" target="_blank">
                        <span class="pc-micon"><i data-feather="lock"></i></span>
                        <span class="pc-mtext">Login</span>
                    </a>
                </li>


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
                    <h5 class="mb-0 font-medium">Combo xuất ăn</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item" aria-current="page">Combo xuất ăn</li>
                </ul>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12 xl:col-span-9 md:col-span-9">
                <div class="card table-card">
                    <div class="card-header">
                        <h5>Danh sách Combo xuất ăn</h5>
                        <a href="{{ route('addcombo') }}" class="btn btn-primary">+ Thêm mới</a>
                    </div>
                    <div class="card-header">
                        <h5></h5>
                        <div class="filter-container">
                            <!-- Tìm theo tên -->
                            <form method="GET" action="{{ route('combo') }}">
                                <input type="text" name="name" placeholder="Tìm kiếm tên combo" value="{{ request('name') }}" style="width:230px">


                                <select name="status">
                                    <option value="" {{ !request()->filled('status') ? 'selected' : '' }}>Trạng thái</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng</option>
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
                                <th>Mã Combo</th>
                                <th>Tên Combo</th>
                                <th>Tổng xuất ăn</th>
                                <th>Số ngày sử dụng xuất ăn</th>
                                <th>Ghi chú</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($combosList as $index => $combo)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $combo['ComboCode'] ?? '' }}</td>
                            <td>{{ $combo['ComboName'] ?? '' }}</td>
                            <td>{{ $combo['TotalMeals'] }}</td>
                            <td>{{ $combo['NumberOfDate'] }}</td>
                            <td>{{ $combo['Description'] ?? '' }}</td>
                            <td>
                                @if(($combo['Status'] ?? 0) == 1)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Ngừng</span>
                                @endif
                            </td>
                            <td>
                                <!-- Nút sửa, xóa -->
                                <a href="{{ route('editcombo', $combo['Id']) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('combo.destroy', $combo['Id']) }}" method="POST" style="display:inline; padding:0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" style="padding-top: 30px; text-align: right;">
                                    <div class="pagination">
                                        {{-- Nút trước --}}
                                        @if ($combosList->onFirstPage())
                                            <span class="btn disabled">◀</span>
                                        @else
                                            <a href="{{ $combosList->previousPageUrl() }}" class="btn">◀</a>
                                        @endif

                                        {{-- Các số trang --}}
                                        @foreach ($combosList->getUrlRange(1, $combosList->lastPage()) as $page => $url)
                                            @if ($page == $combosList->currentPage())
                                                <span class="btn active">{{ $page }}</span>
                                            @else
                                                <a href="{{ $url }}" class="btn">{{ $page }}</a>
                                            @endif
                                        @endforeach

                                        {{-- Nút sau --}}
                                        @if ($combosList->hasMorePages())
                                            <a href="{{ $combosList->nextPageUrl() }}" class="btn">▶</a>
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

<!-- [ Main Content ] end -->
@endsection