<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Safari CRM - Tapestry of Africa</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --background: #f8fafc;
                --foreground: #0f172a;
                --sidebar: #1e293b;
                --primary: #E17D2F;
                --primary-dark: #c96a1e;
            }
            body { font-family: 'Inter', system-ui, sans-serif; }

            /* Sidebar */
            .sidebar {
                background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            }
            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                border-radius: 8px;
                color: #94a3b8;
                transition: all 0.2s;
            }
            .sidebar-link:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #ffffff;
            }
            .sidebar-link.active {
                background: #E17D2F;
                color: #ffffff;
            }

            /* Mobile sidebar overlay */
            .sidebar-overlay {
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
            }

            /* Cards */
            .stat-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 20px;
                border: 1px solid #e2e8f0;
                cursor: pointer;
                transition: all 0.2s;
            }
            .stat-card:hover {
                border-color: #E17D2F;
                box-shadow: 0 4px 12px rgba(232, 148, 28, 0.15);
            }

            /* Data Tables */
            .data-table { width: 100%; border-collapse: collapse; }
            .data-table th {
                text-align: left;
                padding: 12px 16px;
                background: #f8fafc;
                font-weight: 600;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                border-bottom: 1px solid #e2e8f0;
            }
            .data-table td {
                padding: 16px;
                border-bottom: 1px solid #e2e8f0;
            }
            .data-table tr:hover { background: #f8fafc; }

            /* Responsive table wrapper */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Mobile card view for tables */
            @media (max-width: 768px) {
                .data-table.mobile-cards thead { display: none; }
                .data-table.mobile-cards tbody tr {
                    display: block;
                    padding: 16px;
                    margin-bottom: 12px;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    background: white;
                }
                .data-table.mobile-cards tbody tr:hover {
                    background: white;
                    border-color: #E17D2F;
                }
                .data-table.mobile-cards td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 8px 0;
                    border: none;
                }
                .data-table.mobile-cards td::before {
                    content: attr(data-label);
                    font-weight: 600;
                    font-size: 12px;
                    text-transform: uppercase;
                    color: #64748b;
                    margin-right: 16px;
                }
                .data-table.mobile-cards td:last-child {
                    justify-content: flex-end;
                    padding-top: 12px;
                    border-top: 1px solid #e2e8f0;
                    margin-top: 8px;
                }
            }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }
            .btn-primary {
                background: #E17D2F;
                color: white;
            }
            .btn-primary:hover { background: #c96a1e; }
            .btn-secondary {
                background: #f1f5f9;
                color: #475569;
            }
            .btn-secondary:hover { background: #e2e8f0; }
            .btn-danger {
                background: #ef4444;
                color: white;
            }
            .btn-danger:hover { background: #dc2626; }

            /* Badges */
            .badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 12px;
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
            }
            .badge-success { background: #dcfce7; color: #166534; }
            .badge-warning { background: #fef3c7; color: #92400e; }
            .badge-info { background: #dbeafe; color: #1e40af; }
            .badge-orange { background: #ffedd5; color: #c2410c; }

            /* Tabs */
            .tab-container {
                display: flex;
                gap: 0;
                border-bottom: 2px solid #e2e8f0;
                margin-bottom: 24px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .tab {
                padding: 10px 14px;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                color: #64748b;
                font-weight: 500;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }
            .tab:hover { color: #0f172a; }
            .tab.active {
                border-bottom-color: #E17D2F;
                color: #E17D2F;
            }
            .tab-content { display: none; }
            .tab-content.active { display: block; }

            /* Mobile hamburger animation */
            .hamburger-line {
                transition: all 0.3s ease;
            }
            .hamburger-active .hamburger-line:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }
            .hamburger-active .hamburger-line:nth-child(2) {
                opacity: 0;
            }
            .hamburger-active .hamburger-line:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -6px);
            }

            /* Smooth sidebar transition */
            .sidebar-mobile {
                transition: transform 0.3s ease-in-out;
            }

            /* Mobile-specific utilities */
            @media (max-width: 640px) {
                .btn { padding: 8px 16px; font-size: 13px; }
                .stat-card { padding: 16px; }
                .stat-card p.text-3xl { font-size: 1.5rem; }
            }
        </style>
    </head>
    <body class="antialiased bg-[#f8fafc]" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex">
            <!-- Mobile sidebar overlay -->
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 sidebar-overlay lg:hidden"
            ></div>

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main content -->
            <main class="flex-1 lg:ml-64 min-w-0">
                @include('layouts.header')

                <div class="p-4 sm:p-6 lg:p-8">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
