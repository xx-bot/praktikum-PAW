<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AuraNotes — Ruang Catatan Kreatif Anda</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite Assets / CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.08), transparent 400px),
                        radial-gradient(circle at bottom left, rgba(244, 63, 94, 0.05), transparent 400px),
                        #0b0f19;
        }
        h1, h2, h3, .font-display {
            font-family: 'Outfit', sans-serif;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .note-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .note-card:hover {
            transform: translateY(-4px);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0b0f19;
        }
        ::-webkit-scrollbar-thumb {
            background: #1f2937;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #374151;
        }
    </style>
</head>
<body class="h-full text-slate-100 flex flex-col antialiased selection:bg-indigo-500/30 selection:text-indigo-200">

    <!-- Top Glow Header -->
    <header class="w-full border-b border-slate-800/80 bg-slate-950/50 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <!-- Logo & Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-rose-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 via-purple-400 to-rose-400 bg-clip-text text-transparent">AuraNotes</h1>
                    <p class="text-xs text-slate-400 font-medium">Tempat ide Anda bersinar</p>
                </div>
            </div>

            <!-- Search Bar (Premium, real-time client filter) -->
            <div class="w-full sm:max-w-md relative">
                <form action="{{ route('home') }}" method="GET" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        id="search-input"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari catatan instan (judul atau isi)..." 
                        class="block w-full pl-10 pr-4 py-2.5 bg-slate-900/60 border border-slate-700/60 rounded-xl text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/80 focus:border-indigo-500/80 transition-all text-sm backdrop-blur-sm"
                    >
                    @if($search)
                        <a href="{{ route('home') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Right Actions (Live Clock & User Profile / Logout) -->
            <div class="flex items-center gap-4.5 flex-wrap sm:flex-nowrap">
                <!-- Live Clock Widget -->
                <div class="hidden lg:flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-slate-900/50 border border-slate-800 text-xs font-semibold text-slate-350">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="live-clock">--:--:--</span>
                </div>

                <!-- User Profile & Logout -->
                <div class="flex items-center gap-3 pl-4 border-l border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-300 font-bold text-sm shadow-md">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <span class="block text-[9px] font-semibold uppercase tracking-wider text-slate-500 leading-none">Pengguna</span>
                            <span class="block text-xs font-bold text-slate-100 max-w-[100px] truncate leading-normal mt-0.5" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            title="Keluar Sesi"
                            class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-850 text-slate-400 hover:text-rose-400 hover:border-rose-500/30 flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105 active:scale-[0.98]"
                        >
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-8">
        
        <!-- SIDEBAR: Add/Edit Form -->
        <div class="w-full lg:w-96 flex-shrink-0">
            <div class="glass-card rounded-3xl p-6 shadow-2xl sticky top-28 transition-all duration-500 border border-indigo-500/10" id="form-container">
                
                <!-- Form Header -->
                <div class="flex items-center gap-3 mb-6">
                    <div id="form-icon-bg" class="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 transition-colors">
                        <svg class="w-5 h-5" id="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-white transition-all" id="form-title">Tulis Catatan</h2>
                        <p class="text-xs text-slate-400" id="form-subtitle">Tumpahkan ide cemerlang Anda sekarang</p>
                    </div>
                </div>

                <!-- Session Errors -->
                @if ($errors->any())
                    <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Dynamic Form -->
                <form id="note-form" action="{{ route('notes.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Judul Catatan</label>
                        <input 
                            type="text" 
                            name="title" 
                            id="note-title"
                            required
                            placeholder="Contoh: Belanja Mingguan..."
                            class="block w-full px-4 py-3 bg-slate-900/50 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all text-sm font-medium"
                        >
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Isi Catatan</label>
                        <textarea 
                            name="content" 
                            id="note-content"
                            required
                            rows="5"
                            placeholder="Tulis ide atau deskripsi lengkap di sini..."
                            class="block w-full px-4 py-3 bg-slate-900/50 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all text-sm font-medium leading-relaxed resize-none"
                        ></textarea>
                    </div>



                    <!-- Note Color Selector -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-3">Warna Catatan</label>
                        <div class="flex items-center gap-3">
                            <!-- Amber -->
                            <label class="relative flex items-center justify-center cursor-pointer">
                                <input type="radio" name="color" value="amber" class="sr-only peer" checked>
                                <span class="w-8 h-8 rounded-full bg-amber-500/20 border-2 border-amber-500/50 peer-checked:ring-2 peer-checked:ring-amber-400 peer-checked:scale-110 flex items-center justify-center text-amber-300 transition-all hover:scale-105">
                                    <svg class="w-3.5 h-3.5 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </label>

                            <!-- Emerald -->
                            <label class="relative flex items-center justify-center cursor-pointer">
                                <input type="radio" name="color" value="emerald" class="sr-only peer">
                                <span class="w-8 h-8 rounded-full bg-emerald-500/20 border-2 border-emerald-500/50 peer-checked:ring-2 peer-checked:ring-emerald-400 peer-checked:scale-110 flex items-center justify-center text-emerald-300 transition-all hover:scale-105">
                                    <svg class="w-3.5 h-3.5 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </label>

                            <!-- Blue -->
                            <label class="relative flex items-center justify-center cursor-pointer">
                                <input type="radio" name="color" value="blue" class="sr-only peer">
                                <span class="w-8 h-8 rounded-full bg-blue-500/20 border-2 border-blue-500/50 peer-checked:ring-2 peer-checked:ring-blue-400 peer-checked:scale-110 flex items-center justify-center text-blue-300 transition-all hover:scale-105">
                                    <svg class="w-3.5 h-3.5 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </label>

                            <!-- Rose -->
                            <label class="relative flex items-center justify-center cursor-pointer">
                                <input type="radio" name="color" value="rose" class="sr-only peer">
                                <span class="w-8 h-8 rounded-full bg-rose-500/20 border-2 border-rose-500/50 peer-checked:ring-2 peer-checked:ring-rose-400 peer-checked:scale-110 flex items-center justify-center text-rose-300 transition-all hover:scale-105">
                                    <svg class="w-3.5 h-3.5 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </label>

                            <!-- Violet -->
                            <label class="relative flex items-center justify-center cursor-pointer">
                                <input type="radio" name="color" value="violet" class="sr-only peer">
                                <span class="w-8 h-8 rounded-full bg-violet-500/20 border-2 border-violet-500/50 peer-checked:ring-2 peer-checked:ring-violet-400 peer-checked:scale-110 flex items-center justify-center text-violet-300 transition-all hover:scale-105">
                                    <svg class="w-3.5 h-3.5 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </label>

                            <!-- Slate -->
                            <label class="relative flex items-center justify-center cursor-pointer">
                                <input type="radio" name="color" value="slate" class="sr-only peer">
                                <span class="w-8 h-8 rounded-full bg-slate-550/20 border-2 border-slate-500/50 peer-checked:ring-2 peer-checked:ring-slate-400 peer-checked:scale-110 flex items-center justify-center text-slate-300 transition-all hover:scale-105">
                                    <svg class="w-3.5 h-3.5 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-2 flex flex-col gap-2.5">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-bold text-sm tracking-wide shadow-lg shadow-indigo-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>Simpan Catatan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </button>
                        
                        <button 
                            type="button" 
                            id="cancel-btn"
                            class="w-full py-3 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm active:scale-[0.98] transition-all hidden cursor-pointer"
                            onclick="resetForm()"
                        >
                            Batal Edit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT AREA: Stats & Notes Grid -->
        <div class="flex-grow space-y-6">

            <!-- Stats Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Stat 1: Total Notes -->
                <div class="glass-card rounded-2xl p-4.5 flex items-center justify-between shadow-xl border border-slate-800">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Catatan</span>
                        <span class="block text-2xl font-extrabold text-white mt-1">{{ $stats['total'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <!-- Stat 2: Colors distribution -->
                <div class="glass-card rounded-2xl p-4.5 flex items-center justify-between shadow-xl border border-slate-800">
                    <div class="w-full">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2.5">Distribusi Warna</span>
                        <div class="flex items-center gap-1.5 w-full">
                            @php
                                $colors = ['amber', 'emerald', 'blue', 'rose', 'violet', 'slate'];
                                $colorHex = [
                                    'amber' => 'bg-amber-500',
                                    'emerald' => 'bg-emerald-500',
                                    'blue' => 'bg-blue-500',
                                    'rose' => 'bg-rose-500',
                                    'violet' => 'bg-violet-500',
                                    'slate' => 'bg-slate-500'
                                ];
                            @endphp
                            @foreach($colors as $col)
                                <div class="flex-grow h-2 rounded-full relative group {{ $colorHex[$col] }} opacity-40 hover:opacity-100 transition-opacity">
                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-1.5 py-0.5 rounded bg-slate-900 text-[9px] font-bold text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-md">
                                        {{ $stats['colors'][$col] ?? 0 }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Notes List / Grid -->
            <div>
                @if($notes->isEmpty())
                    <!-- Empty State -->
                    <div class="glass-card rounded-3xl p-12 text-center border border-dashed border-slate-800 shadow-xl flex flex-col items-center justify-center min-h-[350px]" id="empty-state">
                        <div class="w-20 h-20 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mb-5 text-slate-500 shadow-inner">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        @if($search)
                            <h3 class="text-xl font-bold text-white mb-2">Hasil pencarian kosong</h3>
                            <p class="text-sm text-slate-400 max-w-sm leading-relaxed mb-6">Kami tidak dapat menemukan catatan yang cocok dengan "{{ $search }}". Silakan cari kata kunci lain.</p>
                            <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl bg-slate-850 hover:bg-slate-800 border border-slate-700 text-sm font-semibold text-white transition-all">Clear Search</a>
                        @else
                            <h3 class="text-xl font-bold text-white mb-2">Belum ada catatan</h3>
                            <p class="text-sm text-slate-400 max-w-xs leading-relaxed">Ruang kreativitas Anda masih kosong. Mulailah menulis ide hebat pertama Anda di sidebar sebelah kiri!</p>
                        @endif
                    </div>
                @else
                    <!-- Notes Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5" id="notes-grid">
                        @foreach($notes as $note)
                            @php
                                // Assign border and glow classes based on note color
                                $colorClasses = [
                                    'amber' => 'border-amber-500/20 bg-amber-500/5 hover:border-amber-500/40 text-amber-100 hover:shadow-[0_0_15px_rgba(245,158,11,0.06)]',
                                    'emerald' => 'border-emerald-500/20 bg-emerald-500/5 hover:border-emerald-500/40 text-emerald-100 hover:shadow-[0_0_15px_rgba(16,185,129,0.06)]',
                                    'blue' => 'border-blue-500/20 bg-blue-500/5 hover:border-blue-500/40 text-blue-100 hover:shadow-[0_0_15px_rgba(59,130,246,0.06)]',
                                    'rose' => 'border-rose-500/20 bg-rose-500/5 hover:border-rose-500/40 text-rose-100 hover:shadow-[0_0_15px_rgba(244,63,94,0.06)]',
                                    'violet' => 'border-violet-500/20 bg-violet-500/5 hover:border-violet-500/40 text-violet-100 hover:shadow-[0_0_15px_rgba(139,92,246,0.06)]',
                                    'slate' => 'border-slate-500/20 bg-slate-500/5 hover:border-slate-500/40 text-slate-100 hover:shadow-[0_0_15px_rgba(100,116,139,0.06)]',
                                ][$note->color] ?? 'border-slate-500/20 bg-slate-500/5 text-slate-100';

                                $badgeClasses = [
                                    'amber' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                    'emerald' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                    'blue' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                    'rose' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                    'violet' => 'bg-violet-500/10 text-violet-400 border border-violet-500/20',
                                    'slate' => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
                                ][$note->color] ?? 'bg-slate-500/10 text-slate-400';
                            @endphp
                            
                            <!-- Note Card -->
                            <article 
                                class="note-card border rounded-2xl p-5 flex flex-col justify-between min-h-[190px] shadow-xl {{ $colorClasses }}"
                                data-id="{{ $note->id }}"
                                data-title="{{ $note->title }}"
                                data-content="{{ $note->content }}"
                                data-color="{{ $note->color }}"
                            >
                                <div>
                                    <!-- Card Header (Title & Badges) -->
                                    <div class="flex items-start justify-between gap-3 mb-2.5">
                                        <h3 class="font-bold text-lg text-white font-display line-clamp-1 note-card-title">{{ $note->title }}</h3>
                                        
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider {{ $badgeClasses }}">
                                                {{ $note->color }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <p class="text-sm text-slate-300 leading-relaxed font-medium whitespace-pre-wrap line-clamp-4 note-card-content mb-4">{{ $note->content }}</p>
                                </div>

                                <!-- Card Footer Actions -->
                                <div class="flex items-center justify-between border-t border-slate-800/60 pt-3.5 mt-auto">
                                    <span class="text-[10px] text-slate-400 font-semibold" title="Diperbarui: {{ $note->updated_at->format('d M Y, H:i') }}">
                                        {{ $note->updated_at->diffForHumans() }}
                                    </span>
                                    
                                    <div class="flex items-center gap-2">


                                        <!-- Edit Action (Dynamic trigger) -->
                                        <button 
                                            type="button" 
                                            onclick="triggerEdit(this)"
                                            title="Ubah Catatan"
                                            class="w-8.5 h-8.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-indigo-400 hover:border-indigo-500/30 flex items-center justify-center transition-all cursor-pointer"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <!-- Delete Action -->
                                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                title="Hapus Catatan"
                                                class="w-8.5 h-8.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-rose-500 hover:border-rose-500/30 flex items-center justify-center transition-all cursor-pointer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- Floating Footer -->
    <footer class="w-full py-6 mt-10 border-t border-slate-900 bg-slate-950/20 text-center text-slate-500 text-xs font-semibold">
        <p>© 2026 AuraNotes CRUD. Dibuat dengan antusiasme tinggi untuk Praktikum PAW.</p>
    </footer>

    <!-- SUCCESS TOAST NOTIFICATION -->
    @if(session('success'))
        <div id="toast-notification" class="fixed bottom-6 right-6 max-w-sm glass-card rounded-2xl shadow-2xl border-indigo-500/30 border p-4.5 flex items-start gap-3.5 z-50 transform translate-y-20 opacity-0 transition-all duration-500 ease-out">
            <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center flex-shrink-0 border border-indigo-500/30">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-grow">
                <h4 class="text-sm font-bold text-white">Sukses</h4>
                <p class="text-xs text-slate-300 mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
            <button onclick="dismissToast()" class="text-slate-400 hover:text-slate-200 transition-colors flex-shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Inline JavaScript -->
    <script>
        // Live Clock
        function updateClock() {
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Slide in Toast
        window.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                setTimeout(() => {
                    toast.classList.remove('translate-y-20', 'opacity-0');
                }, 100);

                // Auto hide after 4 seconds
                setTimeout(() => {
                    dismissToast();
                }, 4000);
            }
        });

        function dismissToast() {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                toast.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }
        }

        // Trigger Edit Mode (Single Page CRUD Experience)
        function triggerEdit(button) {
            const card = button.closest('.note-card');
            const id = card.getAttribute('data-id');
            const title = card.getAttribute('data-title');
            const content = card.getAttribute('data-content');
            const color = card.getAttribute('data-color');

            // Swap Title and Style
            document.getElementById('form-title').textContent = 'Edit Catatan';
            document.getElementById('form-subtitle').textContent = 'Perbarui detail catatan Anda';
            
            const formIconBg = document.getElementById('form-icon-bg');
            formIconBg.classList.remove('bg-indigo-500/10', 'text-indigo-400');
            formIconBg.classList.add('bg-amber-500/10', 'text-amber-400');

            const formIcon = document.getElementById('form-icon');
            formIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />`;

            // Swap Form Route
            const form = document.getElementById('note-form');
            form.action = `/notes/${id}`;
            document.getElementById('form-method').value = 'PUT';

            // Populate Inputs
            document.getElementById('note-title').value = title;
            document.getElementById('note-content').value = content;

            // Highlight Color Radio
            const colorRadio = form.querySelector(`input[name="color"][value="${color}"]`);
            if (colorRadio) {
                colorRadio.checked = true;
            }

            // Show Cancel Button
            document.getElementById('cancel-btn').classList.remove('hidden');

            // Scroll to Form on Mobile
            const formContainer = document.getElementById('form-container');
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Pulse form container border to indicate active edit
            formContainer.classList.remove('border-indigo-500/10');
            formContainer.classList.add('border-amber-500/40', 'ring-2', 'ring-amber-500/20');
        }

        // Reset/Cancel Edit Mode
        function resetForm() {
            // Restore Title and Style
            document.getElementById('form-title').textContent = 'Tulis Catatan';
            document.getElementById('form-subtitle').textContent = 'Tumpahkan ide cemerlang Anda sekarang';
            
            const formIconBg = document.getElementById('form-icon-bg');
            formIconBg.classList.remove('bg-amber-500/10', 'text-amber-400');
            formIconBg.classList.add('bg-indigo-500/10', 'text-indigo-400');

            const formIcon = document.getElementById('form-icon');
            formIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />`;

            // Restore Form Route
            const form = document.getElementById('note-form');
            form.action = "{{ route('notes.store') }}";
            document.getElementById('form-method').value = 'POST';

            // Reset Fields
            form.reset();

            // Hide Cancel Button
            document.getElementById('cancel-btn').classList.add('hidden');

            // Remove highlighted border
            const formContainer = document.getElementById('form-container');
            formContainer.classList.remove('border-amber-500/40', 'ring-2', 'ring-amber-500/20');
            formContainer.classList.add('border-indigo-500/10');
        }

        // Instant Client-side Search (Enhancement)
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const noteCards = document.querySelectorAll('.note-card');
                const emptyState = document.getElementById('empty-state');
                const notesGrid = document.getElementById('notes-grid');

                let matchedCount = 0;

                noteCards.forEach(card => {
                    const title = card.querySelector('.note-card-title').textContent.toLowerCase();
                    const content = card.querySelector('.note-card-content').textContent.toLowerCase();

                    if (title.includes(query) || content.includes(query)) {
                        card.classList.remove('hidden');
                        matchedCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Dynamically show empty search screen if no results match client-side query
                if (matchedCount === 0 && notesGrid) {
                    if (!document.getElementById('client-empty-state')) {
                        const div = document.createElement('div');
                        div.id = 'client-empty-state';
                        div.className = 'glass-card rounded-3xl p-12 text-center border border-dashed border-slate-800 shadow-xl flex flex-col items-center justify-center min-h-[300px] w-full col-span-full';
                        div.innerHTML = `
                            <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mb-4 text-slate-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-1">Hasil instan kosong</h3>
                            <p class="text-xs text-slate-400 max-w-xs leading-relaxed">Tidak ada catatan lokal yang cocok dengan "${query}".</p>
                        `;
                        notesGrid.after(div);
                        notesGrid.classList.add('hidden');
                    }
                } else if (notesGrid) {
                    const localEmpty = document.getElementById('client-empty-state');
                    if (localEmpty) localEmpty.remove();
                    notesGrid.classList.remove('hidden');
                }
            });
        }
    </script>
</body>
</html>
