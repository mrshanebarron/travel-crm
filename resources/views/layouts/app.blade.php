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
                --primary: #e8941c;
                --primary-dark: #d17c0a;
            }
            body { font-family: 'Inter', system-ui, sans-serif; }
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
                background: #e8941c;
                color: #ffffff;
            }
            .stat-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 20px;
                border: 1px solid #e2e8f0;
                cursor: pointer;
                transition: all 0.2s;
            }
            .stat-card:hover {
                border-color: #e8941c;
                box-shadow: 0 4px 12px rgba(232, 148, 28, 0.15);
            }
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
            }
            .btn-primary {
                background: #e8941c;
                color: white;
            }
            .btn-primary:hover { background: #d17c0a; }
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
            .tab-container {
                display: flex;
                gap: 0;
                border-bottom: 2px solid #e2e8f0;
                margin-bottom: 24px;
                overflow-x: auto;
            }
            .tab {
                padding: 12px 24px;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                color: #64748b;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                white-space: nowrap;
            }
            .tab:hover { color: #0f172a; }
            .tab.active {
                border-bottom-color: #e8941c;
                color: #e8941c;
            }
            .tab-content { display: none; }
            .tab-content.active { display: block; }
        </style>
    </head>
    <body class="antialiased bg-[#f8fafc]">
        <div class="min-h-screen flex">
            @include('layouts.sidebar')

            <main class="flex-1 ml-64">
                @include('layouts.header')

                <div class="p-8">
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
