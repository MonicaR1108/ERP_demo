<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - ERP System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Figtree', sans-serif;
            background: #f5f5f5;
        }
        :root {
            --erp-nav-bg: #2c3e50;
            --erp-hover-bg: rgba(59, 130, 246, 0.22);
            --erp-hover-bg-strong: rgba(96, 165, 250, 0.24);
            --erp-hover-text: #ffffff;
            --erp-hover-accent: #7dd3fc;
            --erp-hover-shadow: 0 8px 18px rgba(15, 23, 42, 0.22);
            --erp-underline: linear-gradient(90deg, #60a5fa 0%, #22d3ee 100%);
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: var(--erp-nav-bg);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 1200;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.closed {
            transform: translateX(-100%);
        }
        .sidebar-header {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            min-height: 60px;
        }
        .sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 18px 0;
        }
        .logo {
            font-size: 18px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
            flex: 1;
            line-height: 1.2;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed .logo {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .menu-toggle {
            background: none;
            border: none;
            color: #ffffff !important;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 5px;
            transition: all 0.3s;
            flex-shrink: 0;
            margin-left: 10px;
        }
        .sidebar.collapsed .menu-toggle {
            margin-left: 0;
            width: 100%;
            justify-content: center;
        }
        .menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        .menu-toggle i {
            color: #ffffff !important;
            display: block;
            line-height: 1;
        }
        .sidebar-menu {
            padding: 10px 0 14px;
            overflow-y: scroll;
            overflow-x: hidden;
            flex: 1;
            max-height: calc(100vh - 60px);
            scrollbar-gutter: stable;
        }
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Simple inline loader for submit buttons */
        .btn-loading-spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            margin-right: 6px;
            display: inline-block;
            vertical-align: middle;
            animation: btn-spin 0.6s linear infinite;
        }

        @keyframes btn-spin {
            to { transform: rotate(360deg); }
        }
        .menu-item-header {
            padding: 12px 20px;
            font-size: 12px;
            color: #f9fafb;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.3s ease;
            background: rgba(15, 23, 42, 0.55);
            border-left: 3px solid rgba(148, 163, 184, 0.35);
            margin: 4px 0;
            position: relative;
            overflow: hidden;
        }
        .menu-item-header::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 0;
            height: 2px;
            background: var(--erp-underline);
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 0.3s ease;
        }
        .menu-item-header:hover {
            background: rgba(30, 41, 59, 0.95);
            color: var(--erp-hover-text);
            border-left-color: var(--erp-hover-accent);
            box-shadow: var(--erp-hover-shadow);
            transform: scale(1.02);
        }
        .menu-item-header:hover::after {
            transform: scaleX(1);
        }
        .menu-item-header.active-section {
            background: rgba(59, 130, 246, 0.2);
            border-left-color: #60a5fa;
            color: #ffffff;
        }
        .menu-item-header .menu-header-icon {
            font-size: 16px;
            margin-right: 8px;
        }
        .menu-item-header .arrow {
            transition: transform 0.3s ease, opacity 0.3s ease;
            font-size: 10px;
            margin-left: 8px;
            opacity: 0.85;
        }
        .menu-item-header.collapsed .arrow {
            transform: rotate(-90deg);
            opacity: 0.65;
        }
        .menu-sub-items {
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease, transform 0.3s ease, padding 0.3s ease;
            max-height: 1000px;
            background: rgba(17, 24, 39, 0.35);
            opacity: 1;
            transform: translateY(0);
            padding: 2px 0 6px;
        }
        .sidebar:not(.menu-ready) .menu-sub-items,
        .sidebar:not(.menu-ready) .menu-item-header .arrow,
        .sidebar:not(.menu-ready) .menu-item-header {
            transition: none !important;
        }
        .menu-sub-items.collapsed {
            max-height: 0;
            opacity: 0;
            transform: translateY(-6px);
            padding: 0;
        }
        .menu-item {
            padding: 11px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            line-height: 1.5;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        .menu-item::after {
            content: "";
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: 0;
            height: 2px;
            background: var(--erp-underline);
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 0.3s ease;
        }
        .menu-item:hover {
            background: var(--erp-hover-bg);
            color: var(--erp-hover-text);
            border-left-color: var(--erp-hover-accent);
            box-shadow: var(--erp-hover-shadow);
            transform: scale(1.03);
        }
        .menu-item:hover::after {
            transform: scaleX(1);
        }
        .menu-sub-items .menu-item {
            background: rgba(255, 255, 255, 0.03);
            color: rgba(241, 245, 249, 0.9);
            font-size: 13px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .menu-sub-items .menu-item:hover {
            background: var(--erp-hover-bg-strong);
            color: #ffffff;
        }
        .menu-item.active {
            background: rgba(59, 130, 246, 0.28);
            color: #ffffff;
            border-left-color: #93c5fd;
            font-weight: 600;
        }
        .menu-item i {
            width: 20px;
            text-align: left;
            font-size: 18px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .menu-item span {
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
            line-height: 1.5;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed .menu-item span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 14px 0;
            gap: 0;
        }
        /* In collapsed mode: show only the section icon, hide text and arrow */
        .sidebar.collapsed .menu-item-header span {
            display: none;
        }
        .sidebar.collapsed .menu-item-header .arrow {
            display: none;
        }
        .sidebar.collapsed .menu-item-header {
            justify-content: center;
        }
        .sidebar.collapsed .menu-item i {
            justify-content: center;
            text-align: center;
            width: 20px;
            margin: 0 auto;
        }
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-width: 0;
            flex: 1;
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }
        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }
        .main-content.sidebar-collapsed {
            margin-left: 70px;
            width: calc(100% - 70px);
        }
        .top-header {
            background: var(--erp-nav-bg);
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .mobile-sidebar-toggle {
            display: none;
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 8px;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }
        .top-nav-action,
        .branch-selector {
            transition: background 0.3s ease, color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
            position: relative;
            overflow: visible;
        }
        .branch-selector-wrap {
            position: relative;
        }
        .top-nav-action::after,
        .branch-selector-wrap::after {
            content: "";
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.9);
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 0.3s ease;
            pointer-events: none;
        }
        .top-nav-action:hover,
        .top-nav-action:focus-visible {
            background: rgba(255,255,255,0.3) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.24);
            transform: scale(1.05);
        }
        .top-nav-action:hover::after,
        .top-nav-action:focus-visible::after,
        .branch-selector-wrap:hover::after,
        .branch-selector-wrap:focus-within::after {
            transform: scaleX(1);
        }
        .branch-selector:hover,
        .branch-selector:focus-visible {
            background-color: rgba(255,255,255,0.3) !important;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.2);
            transform: scale(1.04);
            outline: none;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .role-badge {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .entity-badge {
            background: #48bb78;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .top-header .user-info {
            color: white;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
            position: relative;
            overflow: hidden;
        }
        .logout-btn::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.9);
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 0.3s ease;
        }
        .logout-btn:hover {
            background: #c82333;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
            transform: scale(1.05);
        }
        .logout-btn:hover::after {
            transform: scaleX(1);
        }
        .logout-btn:active {
            transform: scale(1);
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        }
        .logout-btn i {
            font-size: 16px;
        }
        .content-area {
            padding: 30px;
            min-width: 0;
        }
        @media (hover: hover) and (pointer: fine) {
            .erp-custom-cursor {
                position: fixed;
                top: 0;
                left: 0;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                border: 1.5px solid rgba(15, 23, 42, 0.8);
                background: rgba(15, 23, 42, 0.12);
                box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.16), 0 0 10px rgba(15, 23, 42, 0.22);
                pointer-events: none;
                transform: translate(-50%, -50%) scale(1);
                opacity: 0;
                z-index: 1500;
                will-change: transform, left, top, opacity, border-color, background-color, box-shadow;
                transition: transform 0.24s ease, border-color 0.24s ease, background-color 0.24s ease, box-shadow 0.24s ease, opacity 0.24s ease;
            }
            .erp-custom-cursor.is-active {
                border-color: rgba(2, 6, 23, 0.95);
                background: rgba(2, 6, 23, 0.18);
                box-shadow: 0 0 0 1px rgba(2, 6, 23, 0.22), 0 0 14px rgba(2, 6, 23, 0.3);
            }
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: min(86vw, 320px);
                z-index: 1300;
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 28px rgba(0,0,0,0.35);
            }
            .main-content,
            .main-content.sidebar-collapsed,
            .main-content.expanded {
                margin-left: 0;
                width: 100% !important;
                max-width: 100vw;
            }
            .top-header {
                padding: 10px 12px;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
            }
            .mobile-sidebar-toggle {
                display: inline-flex;
                margin-top: 2px;
            }
            .top-header .user-info {
                flex: 1;
                min-width: 0;
                display: flex !important;
                flex-wrap: wrap;
                justify-content: flex-end;
                align-items: center;
                gap: 8px !important;
            }
            .top-header .role-badge,
            .top-header .entity-badge {
                font-size: 12px;
                padding: 6px 10px;
            }
            .top-header .branch-selector-wrap {
                width: min(220px, 100%);
                margin-left: auto;
            }
            .top-header .top-nav-action {
                padding: 7px 10px !important;
            }
            .top-header .branch-selector {
                width: 100%;
                max-width: 100%;
                font-size: 13px !important;
                padding: 7px 28px 7px 10px !important;
            }
            .top-header .logout-btn {
                padding: 8px 12px;
                font-size: 13px;
                justify-self: end;
            }
            .top-header .logout-btn span {
                display: none;
            }
            .content-area {
                padding: 16px;
                overflow-x: hidden;
            }
            .content-area > * {
                width: 100%;
                max-width: 100% !important;
                box-sizing: border-box;
                min-width: 0;
            }
            .content-area table {
                max-width: 100%;
            }
            .content-area #searchForm {
                max-width: 100% !important;
                min-width: 0 !important;
            }
            .content-area #searchForm input[type="text"],
            .content-area #searchForm input[type="search"] {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box;
            }
        }
        @media (max-width: 1024px) {
            .menu-item-header:hover,
            .menu-item:hover,
            .top-nav-action:hover,
            .branch-selector:hover,
            .logout-btn:hover {
                transform: scale(1.01);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .menu-item-header,
            .menu-item,
            .top-nav-action,
            .branch-selector,
            .logout-btn,
            .menu-item-header::after,
            .menu-item::after,
            .top-nav-action::after,
            .branch-selector-wrap::after,
            .logout-btn::after {
                transition: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">ERP System</div>
                <button class="menu-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="menu-item" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Account Settings --}}
                <a href="{{ route('account.change-password') }}" class="menu-item" title="Change Password">
                    <i class="fas fa-user-cog"></i>
                    <span>Account Settings</span>
                </a>
                
                {{-- System Admin Menu (Super Admin only) --}}
                @if(auth()->user()->isSuperAdmin())
                    <div class="menu-item-header" onclick="toggleSystemAdminMenu()" id="systemAdminHeader" style="margin-top: 10px;" title="System Admin">
                        <i class="fas fa-tools menu-header-icon"></i>
                        <span>System Admin</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items collapsed" id="systemAdminMenu">
                        <a href="{{ route('branches.index') }}" class="menu-item" title="Branches">
                            <i class="fas fa-sitemap"></i>
                            <span>Branches</span>
                        </a>
                        
                        <a href="{{ route('users.index') }}" class="menu-item" title="Users">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                        
                        <a href="{{ route('roles.index') }}" class="menu-item" title="Roles">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles</span>
                        </a>
                        
                        <a href="{{ route('role-permissions.select') }}" class="menu-item" title="Role Permissions">
                            <i class="fas fa-key"></i>
                            <span>Role Permissions</span>
                        </a>
                    </div>
                @endif
                
                {{-- Branch User Menu --}}
                @if(auth()->user()->isBranchUser())
                    <a href="{{ route('transactions.index') }}" class="menu-item" title="Transactions">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Transactions</span>
                    </a>
                @endif
                
                {{-- Enquiry Sales Module --}}
                <div class="menu-item-header" onclick="toggleEnquirySalesMenu()" id="enquirySalesHeader" style="margin-top: 10px;" title="Enquiry Sales">
                    <i class="fas fa-question-circle menu-header-icon"></i>
                    <span>Enquiry Sales</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items collapsed" id="enquirySalesMenu">
                <a href="{{ route('quotations.index') }}" class="menu-item" title="Quotations">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Quotations</span>
                </a>
                <a href="{{ route('proforma-invoices.index') }}" class="menu-item" title="Proforma Invoices">
                    <i class="fas fa-file-invoice"></i>
                    <span>Proforma Invoices</span>
                </a>
                </div>

                {{-- Tender Sales Module --}}
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('tenders', 'view'))
                    <div class="menu-item-header" onclick="toggleTenderSalesMenu()" id="tenderSalesHeader" style="margin-top: 10px;" title="Tender Sales">
                        <i class="fas fa-file-contract menu-header-icon"></i>
                        <span>Tender Sales</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items collapsed" id="tenderSalesMenu">
                        <a href="{{ route('tenders.index') }}" class="menu-item" title="Tenders">
                            <i class="fas fa-gavel"></i>
                            <span>Tenders</span>
                        </a>
                        <a href="{{ route('customer-orders.index') }}" class="menu-item" title="Customer Orders">
                            <i class="fas fa-file-contract"></i>
                            <span>Customer Orders</span>
                        </a>
                        <a href="{{ route('tender-evaluations.index') }}" class="menu-item" title="Tender Evaluation">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Tender Evaluation</span>
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('approvals', 'view'))
                        <a href="{{ route('approvals.index', ['form' => 'customer_orders']) }}" class="menu-item" title="Pending Approvals">
                            <i class="fas fa-check-circle"></i>
                            <span>Pending Approvals</span>
                        </a>
                        @endif
                        <a href="{{ route('customer-complaints.index') }}" class="menu-item" title="Customer Complaint Register">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Customer Complaint Register</span>
                        </a>
                    </div>
                @endif

                {{-- Supplier Master Module --}}
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('suppliers', 'view'))
                    <div class="menu-item-header" onclick="toggleSupplierMenu()" id="supplierHeader" style="margin-top: 10px;" title="Supplier">
                        <i class="fas fa-truck menu-header-icon"></i>
                        <span>Supplier</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items collapsed" id="supplierMenu">
                        <a href="{{ route('suppliers.index') }}" class="menu-item" title="Suppliers">
                            <i class="fas fa-truck"></i>
                            <span>Suppliers</span>
                        </a>
                        <a href="{{ route('supplier-evaluations.index') }}" class="menu-item" title="Supplier Evaluation">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Supplier Evaluation</span>
                        </a>
                        <a href="{{ route('subcontractor-evaluations.index') }}" class="menu-item" title="Subcontractor Evaluation">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Subcontractor Evaluation</span>
                        </a>
                    </div>
                @endif

                {{-- Purchase Module --}}
                 <div class="menu-item-header" onclick="togglePurchaseMenu()" id="purchaseHeader" style="margin-top: 10px;" title="Purchase">
                     <i class="fas fa-shopping-cart menu-header-icon"></i>
                    <span>Purchase</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items collapsed" id="purchaseMenu">
                    <a href="{{ route('purchase-indents.index') }}" class="menu-item" title="Purchase Indents">
                        <i class="fas fa-file-alt"></i>
                        <span>Purchase Indents</span>
                    </a>
                    <a href="{{ route('purchase-orders.index') }}" class="menu-item" title="Purchase Orders">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Purchase Orders</span>
                    </a>
                </div>

                {{-- Store Module --}}
                 <div class="menu-item-header" onclick="toggleStoreMenu()" id="storeHeader" style="margin-top: 10px;" title="Store">
                     <i class="fas fa-warehouse menu-header-icon"></i>
                     <span>Store</span>
                     <i class="fas fa-chevron-down arrow"></i>
                 </div>
                <div class="menu-sub-items collapsed" id="storeMenu">
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('material-inwards', 'view'))
                    <a href="{{ route('material-inwards.index') }}" class="menu-item" title="Material Inward">
                        <i class="fas fa-arrow-down"></i>
                        <span>Material Inward</span>
                    </a>
                    @endif
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('qc-material-inwards', 'view'))
                    <a href="{{ route('qc-material-inwards.index') }}" class="menu-item" title="QC Material Inward">
                        <i class="fas fa-clipboard-check"></i>
                        <span>QC Material Inward</span>
                    </a>
                    @endif
                </div>

                {{-- Production Module --}}
                <div class="menu-item-header" onclick="toggleProductionMenu()" id="productionHeader" style="margin-top: 10px;" title="Production">
                    <i class="fas fa-industry menu-header-icon"></i>
                    <span>Production</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items collapsed" id="productionMenu">
                    <a href="{{ route('work-orders.index') }}" class="menu-item" title="Work Order">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Work Order</span>
                    </a>
                    <a href="{{ route('dpl.index') }}" class="menu-item" title="Daily Production List">
                        <i class="fas fa-list-check"></i>
                        <span>Daily Production List</span>
                    </a>
                    <a href="{{ route('production-list.index') }}" class="menu-item" title="Production List">
                        <i class="fas fa-list"></i>
                        <span>Production List</span>
                    </a>
                </div>

                 <div class="menu-item-header" onclick="toggleMastersMenu()" id="mastersHeader" title="Masters">
                     <i class="fas fa-database menu-header-icon"></i>
                    <span>Masters</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items collapsed" id="mastersMenu">
                    @if(auth()->user()->hasPermission('departments', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('departments.index') }}" class="menu-item" title="Departments">
                        <i class="fas fa-building"></i>
                        <span>Departments</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('designations', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('designations.index') }}" class="menu-item" title="Designations">
                        <i class="fas fa-user-tie"></i>
                        <span>Designations</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('units', 'view'))
                    <a href="{{ route('units.index') }}" class="menu-item" title="Units">
                        <i class="fas fa-balance-scale"></i>
                        <span>Units</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('raw-material-categories', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('raw-material-categories.index') }}" class="menu-item" title="Raw Material Categories">
                        <i class="fas fa-layer-group"></i>
                        <span>Raw Material Categories</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('raw-material-sub-categories', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('raw-material-sub-categories.index') }}" class="menu-item" title="Raw Material SubCategories">
                        <i class="fas fa-sitemap"></i>
                        <span>Raw Material SubCategories</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('raw-materials', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('raw-materials.index') }}" class="menu-item" title="Raw Materials">
                        <i class="fas fa-cube"></i>
                        <span>Raw Materials</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('product-categories', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('product-categories.index') }}" class="menu-item" title="Product Categories">
                        <i class="fas fa-tags"></i>
                        <span>Product Categories</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('products', 'view'))
                    <a href="{{ route('products.index') }}" class="menu-item" title="Products">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('processes', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('processes.index') }}" class="menu-item" title="Processes">
                        <i class="fas fa-cogs"></i>
                        <span>Processes</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('bom-processes', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('bom-processes.index') }}" class="menu-item" title="BOM Processes">
                        <i class="fas fa-clipboard-list"></i>
                        <span>BOM Processes</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('employees', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('employees.index') }}" class="menu-item" title="Employees">
                        <i class="fas fa-user-friends"></i>
                        <span>Employees</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('customers', 'view'))
                    <a href="{{ route('customers.index') }}" class="menu-item" title="Customers">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('production-departments', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('production-departments.index') }}" class="menu-item" title="Production Departments">
                        <i class="fas fa-industry"></i>
                        <span>Production Departments</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('billing-addresses', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('billing-addresses.index') }}" class="menu-item" title="Billing Addresses">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Billing Addresses</span>
                    </a>
                    @endif
                </div>

                {{-- Settings Menu (Super Admin only) --}}
                @if(auth()->user()->isSuperAdmin())
                     <div class="menu-item-header" onclick="toggleSettingsMenu()" id="settingsHeader" style="margin-top: 10px;" title="Settings">
                         <i class="fas fa-cog menu-header-icon"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items collapsed" id="settingsMenu">
                        <a href="{{ route('company-information.index') }}" class="menu-item" title="Company Information">
                            <i class="fas fa-building"></i>
                            <span>Company Information</span>
                        </a>
                    </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <header class="top-header">
                <button class="mobile-sidebar-toggle" onclick="toggleSidebar()" title="Open Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-info" style="display: flex; align-items: center; gap: 15px;">
                    @php
                        $user = auth()->user();
                        $notificationCount = 0;
                        if ($user->isSuperAdmin() || $user->hasPermission('purchase-indents', 'approve')) {
                            $notificationCount = \App\Models\Notification::getUnreadCountForAdmins();
                        }
                    @endphp
                    
                    @if($user->isSuperAdmin() || $user->hasPermission('purchase-indents', 'approve'))
                        <a href="{{ route('notifications.index') }}" class="top-nav-action" style="position: relative; padding: 8px 12px; background: rgba(255,255,255,0.2); border-radius: 5px; color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-bell"></i>
                            @if($notificationCount > 0)
                                <span style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                                    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                    
                    @if(auth()->user()->role)
                        <span class="role-badge">{{ auth()->user()->role->name }}</span>
                    @endif
                    
                    @php
                        $user = auth()->user();
                        $activeBranchId = session('active_branch_id');
                        $activeBranchName = session('active_branch_name');
                        // For Super Admin show all active branches; for others show only their active branches
                        $branchesForSelector = $user->isSuperAdmin()
                            ? \App\Models\Branch::where('is_active', true)->get()
                            : $user->branches()->where('is_active', true)->get();
                    @endphp

                    {{-- Branch Selector (top-right) --}}
                    @if($branchesForSelector->count() > 1)
                        <div class="branch-selector-wrap">
                            <select id="branch-selector" class="branch-selector" onchange="switchBranch(this.value)" 
                                style="padding: 8px 30px 8px 12px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.2); color: white; font-size: 14px; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"white\" height=\"20\" viewBox=\"0 0 24 24\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 8px center;">
                                @foreach($branchesForSelector as $branch)
                                    <option value="{{ $branch->id }}" {{ $activeBranchId == $branch->id ? 'selected' : '' }} style="background-color: #2c3e50; color: white;">
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($activeBranchName)
                        <span class="entity-badge" style="background: #f59e0b;">{{ $activeBranchName }}</span>
                    @endif
                    
                    <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </header>
            
            <script>
                function switchBranch(branchId) {
                    if (branchId) {
                        window.location.href = '{{ url("/branches") }}/' + branchId + '/switch';
                    }
                }
            </script>

            <!-- Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.querySelector('.menu-toggle i');
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                const willOpen = !sidebar.classList.contains('open');
                sidebar.classList.toggle('open', willOpen);
                sidebar.classList.toggle('closed', !willOpen);
                mainContent.classList.add('expanded');
                mainContent.classList.remove('sidebar-collapsed');
                return;
            }
            
            // Toggle collapsed state (show icons only)
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Update toggle icon based on state
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-chevron-right');
            } else {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-bars');
            }
            
            // Remove closed class if present (for mobile)
            sidebar.classList.remove('closed');
            mainContent.classList.remove('expanded');
        }
        
        // Handle mobile view (and restore sidebar when back to desktop)
        function handleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.add('closed');
                sidebar.classList.remove('open');
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('expanded');
                mainContent.classList.remove('sidebar-collapsed');
            } else {
                // On desktop widths always show sidebar (unless user manually collapses it)
                sidebar.classList.remove('open');
                sidebar.classList.remove('closed');
                mainContent.classList.remove('expanded');
            }
        }
        
        // Check on load and resize
        window.addEventListener('load', handleMobileSidebar);
        window.addEventListener('resize', handleMobileSidebar);

        // Toggle Masters menu
        function toggleMastersMenu() {
            const mastersMenu = document.getElementById('mastersMenu');
            const mastersHeader = document.getElementById('mastersHeader');
            
            if (mastersMenu && mastersHeader) {
                mastersMenu.classList.toggle('collapsed');
                mastersHeader.classList.toggle('collapsed');
                mastersHeader.classList.toggle('active-section', !mastersMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_mastersMenuCollapsed', mastersMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Tender Sales menu
        function toggleTenderSalesMenu() {
            const tenderSalesMenu = document.getElementById('tenderSalesMenu');
            const tenderSalesHeader = document.getElementById('tenderSalesHeader');
            
            if (tenderSalesMenu && tenderSalesHeader) {
                tenderSalesMenu.classList.toggle('collapsed');
                tenderSalesHeader.classList.toggle('collapsed');
                tenderSalesHeader.classList.toggle('active-section', !tenderSalesMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_tenderSalesMenuCollapsed', tenderSalesMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Enquiry Sales menu
        function toggleEnquirySalesMenu() {
            const enquirySalesMenu = document.getElementById('enquirySalesMenu');
            const enquirySalesHeader = document.getElementById('enquirySalesHeader');
            
            if (enquirySalesMenu && enquirySalesHeader) {
                enquirySalesMenu.classList.toggle('collapsed');
                enquirySalesHeader.classList.toggle('collapsed');
                enquirySalesHeader.classList.toggle('active-section', !enquirySalesMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_enquirySalesMenuCollapsed', enquirySalesMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Supplier menu
        function toggleSupplierMenu() {
            const supplierMenu = document.getElementById('supplierMenu');
            const supplierHeader = document.getElementById('supplierHeader');
            
            if (supplierMenu && supplierHeader) {
                supplierMenu.classList.toggle('collapsed');
                supplierHeader.classList.toggle('collapsed');
                supplierHeader.classList.toggle('active-section', !supplierMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_supplierMenuCollapsed', supplierMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Purchase menu
        function togglePurchaseMenu() {
            const purchaseMenu = document.getElementById('purchaseMenu');
            const purchaseHeader = document.getElementById('purchaseHeader');
            
            if (purchaseMenu && purchaseHeader) {
                purchaseMenu.classList.toggle('collapsed');
                purchaseHeader.classList.toggle('collapsed');
                purchaseHeader.classList.toggle('active-section', !purchaseMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_purchaseMenuCollapsed', purchaseMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Store menu
        function toggleStoreMenu() {
            const storeMenu = document.getElementById('storeMenu');
            const storeHeader = document.getElementById('storeHeader');
            
            if (storeMenu && storeHeader) {
                storeMenu.classList.toggle('collapsed');
                storeHeader.classList.toggle('collapsed');
                storeHeader.classList.toggle('active-section', !storeMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_storeMenuCollapsed', storeMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Production menu
        function toggleProductionMenu() {
            const productionMenu = document.getElementById('productionMenu');
            const productionHeader = document.getElementById('productionHeader');
            
            if (productionMenu && productionHeader) {
                productionMenu.classList.toggle('collapsed');
                productionHeader.classList.toggle('collapsed');
                productionHeader.classList.toggle('active-section', !productionMenu.classList.contains('collapsed'));
                
                localStorage.setItem('sidebarV2_productionMenuCollapsed', productionMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle Settings menu
        function toggleSettingsMenu() {
            const settingsMenu = document.getElementById('settingsMenu');
            const settingsHeader = document.getElementById('settingsHeader');
            
            if (settingsMenu && settingsHeader) {
                settingsMenu.classList.toggle('collapsed');
                settingsHeader.classList.toggle('collapsed');
                settingsHeader.classList.toggle('active-section', !settingsMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_settingsMenuCollapsed', settingsMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Toggle System Admin menu
        function toggleSystemAdminMenu() {
            const systemAdminMenu = document.getElementById('systemAdminMenu');
            const systemAdminHeader = document.getElementById('systemAdminHeader');
            
            if (systemAdminMenu && systemAdminHeader) {
                systemAdminMenu.classList.toggle('collapsed');
                systemAdminHeader.classList.toggle('collapsed');
                systemAdminHeader.classList.toggle('active-section', !systemAdminMenu.classList.contains('collapsed'));
                
                // Save state to localStorage
                localStorage.setItem('sidebarV2_systemAdminMenuCollapsed', systemAdminMenu.classList.contains('collapsed'));
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            }
        }

        // Initialize collapsible menus using persisted state.
        document.addEventListener('DOMContentLoaded', function() {
            // Restore menu collapse state instead of resetting all sections on each refresh.
            const menuStateMap = [
                { menuId: 'mastersMenu', headerId: 'mastersHeader', key: 'sidebarV2_mastersMenuCollapsed' },
                { menuId: 'tenderSalesMenu', headerId: 'tenderSalesHeader', key: 'sidebarV2_tenderSalesMenuCollapsed' },
                { menuId: 'enquirySalesMenu', headerId: 'enquirySalesHeader', key: 'sidebarV2_enquirySalesMenuCollapsed' },
                { menuId: 'supplierMenu', headerId: 'supplierHeader', key: 'sidebarV2_supplierMenuCollapsed' },
                { menuId: 'purchaseMenu', headerId: 'purchaseHeader', key: 'sidebarV2_purchaseMenuCollapsed' },
                { menuId: 'storeMenu', headerId: 'storeHeader', key: 'sidebarV2_storeMenuCollapsed' },
                { menuId: 'productionMenu', headerId: 'productionHeader', key: 'sidebarV2_productionMenuCollapsed' },
                { menuId: 'settingsMenu', headerId: 'settingsHeader', key: 'sidebarV2_settingsMenuCollapsed' },
                { menuId: 'systemAdminMenu', headerId: 'systemAdminHeader', key: 'sidebarV2_systemAdminMenuCollapsed' }
            ];
            const sidebar = document.getElementById('sidebar');

            const persistCurrentMenuState = function () {
                menuStateMap.forEach(function (item) {
                    const subMenu = document.getElementById(item.menuId);
                    if (!subMenu) {
                        return;
                    }
                    localStorage.setItem(item.key, subMenu.classList.contains('collapsed'));
                });
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            };
            const setExclusiveOpenSection = function (openMenuId) {
                menuStateMap.forEach(function (item) {
                    const subMenu = document.getElementById(item.menuId);
                    const header = document.getElementById(item.headerId);
                    if (!subMenu || !header) {
                        return;
                    }

                    const isOpen = item.menuId === openMenuId;
                    subMenu.classList.toggle('collapsed', !isOpen);
                    header.classList.toggle('collapsed', !isOpen);
                    header.classList.toggle('active-section', isOpen);
                    localStorage.setItem(item.key, (!isOpen).toString());
                });
                localStorage.setItem('sidebarMenuStateSnapshotTs', Date.now().toString());
            };

            menuStateMap.forEach(function (item) {
                const subMenu = document.getElementById(item.menuId);
                const header = document.getElementById(item.headerId);
                if (!subMenu || !header) {
                    return;
                }

                const savedState = localStorage.getItem(item.key);
                const shouldCollapse = savedState === null ? true : savedState === 'true';

                subMenu.classList.toggle('collapsed', shouldCollapse);
                header.classList.toggle('collapsed', shouldCollapse);
                header.classList.toggle('active-section', !shouldCollapse);
            });

            // Restore sidebar menu scroll position so it doesn't jump to top on navigation
            const sidebarMenu = document.querySelector('.sidebar-menu');
            if (sidebarMenu) {
                const savedScroll = localStorage.getItem('sidebarMenuScrollTop');
                if (savedScroll !== null) {
                    sidebarMenu.scrollTop = parseInt(savedScroll, 10) || 0;
                }
                
                // Persist scroll position while user scrolls
                sidebarMenu.addEventListener('scroll', function () {
                    localStorage.setItem('sidebarMenuScrollTop', sidebarMenu.scrollTop);
                });
            }

            // Highlight one active menu item and keep only its parent section open.
            const currentPath = (window.location.pathname || '/').replace(/\/+$/, '') || '/';
            const menuLinks = Array.from(document.querySelectorAll('.sidebar-menu a.menu-item[href]'));
            const activeLink = menuLinks.find(function (link) {
                const linkPath = (new URL(link.href, window.location.origin).pathname || '/').replace(/\/+$/, '') || '/';
                return linkPath === currentPath;
            });
            if (activeLink) {
                activeLink.classList.add('active');
                const subMenu = activeLink.closest('.menu-sub-items');
                if (subMenu && subMenu.id) {
                    setExclusiveOpenSection(subMenu.id);
                }
            }

            // Keep clicked subsection's parent expanded before page navigation.
            document.querySelectorAll('.menu-sub-items a.menu-item[href]').forEach(function (link) {
                link.addEventListener('click', function () {
                    const sidebarMenu = document.querySelector('.sidebar-menu');
                    if (sidebarMenu) {
                        localStorage.setItem('sidebarMenuScrollTop', sidebarMenu.scrollTop);
                    }

                    const subMenu = link.closest('.menu-sub-items');
                    if (!subMenu) {
                        persistCurrentMenuState();
                        return;
                    }

                    if (subMenu.id) {
                        setExclusiveOpenSection(subMenu.id);
                    }
                    persistCurrentMenuState();
                }, true);
            });

            // Persist just before navigation/reload (including filter reset actions)
            window.addEventListener('beforeunload', persistCurrentMenuState);
            window.addEventListener('pagehide', persistCurrentMenuState);

            // Preserve sidebar state when page-level reset/clear controls are used.
            document.addEventListener('click', function (event) {
                const trigger = event.target.closest('button, input[type="button"], input[type="submit"], a');
                if (!trigger) {
                    return;
                }

                const label = ((trigger.textContent || trigger.value || '') + '').toLowerCase().trim();
                if (trigger.type === 'reset' || label === 'reset' || label === 'clear') {
                    persistCurrentMenuState();
                }
            }, true);

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('reset', function () {
                    persistCurrentMenuState();
                });
            });

            // Enable transitions only after initial state restore to avoid open/close flicker on navigation.
            if (sidebar) {
                window.requestAnimationFrame(function () {
                    sidebar.classList.add('menu-ready');
                });
            }

            // Global form submit loader to prevent double submits and show progress
            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    // Prevent double submission
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return;
                    }
                    form.dataset.submitting = 'true';

                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitButtons.forEach(function (btn) {
                        // Skip if already processed
                        if (btn.dataset.loadingApplied === 'true') {
                            return;
                        }
                        btn.dataset.loadingApplied = 'true';
                        btn.disabled = true;

                        if (btn.tagName === 'BUTTON') {
                            btn.dataset.originalHtml = btn.innerHTML;
                            btn.innerHTML = '<span class="btn-loading-spinner"></span>Submitting...';
                        } else if (btn.tagName === 'INPUT') {
                            btn.dataset.originalValue = btn.value;
                            btn.value = 'Submitting...';
                        }
                    });
                });
            });

            // Custom cursor effect only inside dashboard content area on pointer-based devices
            const isDashboardPage = @json(request()->routeIs('dashboard')) || (window.location.pathname || '').replace(/\/+$/, '') === '/dashboard';
            const supportsFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
            const contentArea = document.querySelector('.content-area');
            if (isDashboardPage && supportsFinePointer && contentArea) {
                contentArea.classList.add('cursor-effect-enabled');

                const customCursor = document.createElement('div');
                customCursor.className = 'erp-custom-cursor';
                document.body.appendChild(customCursor);

                const interactiveSelector = 'a, button, .btn, [role="button"], [onclick], input[type="submit"], input[type="button"], input[type="checkbox"], input[type="radio"], select';

                let targetX = 0;
                let targetY = 0;
                let currentX = 0;
                let currentY = 0;
                let visible = false;
                let isInteractive = false;
                let rafId = null;

                const animateCursor = function () {
                    currentX += (targetX - currentX) * 0.2;
                    currentY += (targetY - currentY) * 0.2;

                    customCursor.style.left = currentX + 'px';
                    customCursor.style.top = currentY + 'px';
                    customCursor.style.opacity = visible ? '1' : '0';
                    customCursor.style.transform = 'translate(-50%, -50%) scale(' + (isInteractive ? '1.16' : '1') + ')';
                    customCursor.classList.toggle('is-active', isInteractive);

                    if (visible) {
                        rafId = window.requestAnimationFrame(animateCursor);
                    } else {
                        rafId = null;
                    }
                };

                const startAnimation = function () {
                    if (rafId === null) {
                        rafId = window.requestAnimationFrame(animateCursor);
                    }
                };

                contentArea.addEventListener('mouseenter', function (event) {
                    visible = true;
                    targetX = event.clientX;
                    targetY = event.clientY;
                    currentX = event.clientX;
                    currentY = event.clientY;
                    startAnimation();
                });

                contentArea.addEventListener('mousemove', function (event) {
                    targetX = event.clientX;
                    targetY = event.clientY;
                    isInteractive = !!event.target.closest(interactiveSelector);
                    startAnimation();
                });

                contentArea.addEventListener('mouseleave', function () {
                    visible = false;
                    isInteractive = false;
                });

                window.addEventListener('blur', function () {
                    visible = false;
                    isInteractive = false;
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
