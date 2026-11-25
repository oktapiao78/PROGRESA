<!-- Sidebar navigation per role -->
@php
    $user = auth()->user();
    $sidebarUnreadNotif = $user ? \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count() : 0;
    $sidebarUnreadChat = $user ? \App\Models\ChatMessage::where('receiver_id', $user->id)->where('is_read', false)->count() : 0;
@endphp
<nav style="background-color: #f3e8ff; padding: 20px; width: 240px; flex-shrink: 0; border-radius: 16px; align-self: flex-start; min-height: 400px;">
    <ul style="list-style: none; padding: 0; margin: 0;">
        @if($user && $user->isMahasiswa())
            <li style="margin-bottom: 12px;">
                <a href="{{ route('dashboard.mahasiswa') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('dashboard.mahasiswa') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('dashboard.mahasiswa') ? 'background-color: #e2d9f7;' : '' }}">
                    Dashboard Mahasiswa
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('theses.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('theses.*', 'chapters.*') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('theses.*', 'chapters.*') ? 'background-color: #e2d9f7;' : '' }}">
                    Skripsi Saya
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('supervision.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('supervision.*') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('supervision.*') ? 'background-color: #e2d9f7;' : '' }}">
                    Jadwal &amp; Pengajuan
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('notifications.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('notifications.*') ? '800' : '600' }}; display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('notifications.*') ? 'background-color: #e2d9f7;' : '' }}">
                    <span>Pemberitahuan</span>
                    @if($sidebarUnreadNotif > 0)
                        <span style="background:#9d62cc; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:9999px;">{{ $sidebarUnreadNotif }}</span>
                    @endif
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('chat.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('chat.*') ? '800' : '600' }}; display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('chat.*') ? 'background-color: #e2d9f7;' : '' }}">
                    <span>Chat Pembimbing</span>
                    @if($sidebarUnreadChat > 0)
                        <span style="background:#9d62cc; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:9999px;">{{ $sidebarUnreadChat }}</span>
                    @endif
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('profile.mahasiswa') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('profile.mahasiswa') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('profile.mahasiswa') ? 'background-color: #e2d9f7;' : '' }}">
                    Profil Mahasiswa
                </a>
            </li>
        @elseif($user && $user->isDosen())
            <li style="margin-bottom: 12px;">
                <a href="{{ route('dashboard.dosen') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('dashboard.dosen') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('dashboard.dosen') ? 'background-color: #e2d9f7;' : '' }}">
                    Dashboard Dosen
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('theses.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('theses.*', 'chapters.*') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('theses.*', 'chapters.*') ? 'background-color: #e2d9f7;' : '' }}">
                    Mahasiswa Bimbingan
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('supervision.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('supervision.*') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('supervision.*') ? 'background-color: #e2d9f7;' : '' }}">
                    Sesi &amp; Slot Bimbingan
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('notifications.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('notifications.*') ? '800' : '600' }}; display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('notifications.*') ? 'background-color: #e2d9f7;' : '' }}">
                    <span>Pemberitahuan</span>
                    @if($sidebarUnreadNotif > 0)
                        <span style="background:#9d62cc; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:9999px;">{{ $sidebarUnreadNotif }}</span>
                    @endif
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('chat.index') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('chat.*') ? '800' : '600' }}; display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('chat.*') ? 'background-color: #e2d9f7;' : '' }}">
                    <span>Chat Mahasiswa</span>
                    @if($sidebarUnreadChat > 0)
                        <span style="background:#9d62cc; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:9999px;">{{ $sidebarUnreadChat }}</span>
                    @endif
                </a>
            </li>
            <li style="margin-bottom: 12px;">
                <a href="{{ route('profile.dosen') }}" style="color: #6b4f96; text-decoration: none; font-weight: {{ request()->routeIs('profile.dosen') ? '800' : '600' }}; display: block; padding: 8px 12px; border-radius: 10px; {{ request()->routeIs('profile.dosen') ? 'background-color: #e2d9f7;' : '' }}">
                    Profil Dosen
                </a>
            </li>
        @elseif($user && $user->isAdmin())
            <li style="margin-bottom: 12px;"><a href="{{ route('admin.audit') }}" style="color: #6b4f96; text-decoration: none; font-weight: 600;">Audit Logs</a></li>
            <li style="margin-bottom: 12px;"><a href="{{ route('admin.chapters') }}" style="color: #6b4f96; text-decoration: none; font-weight: 600;">Kelola Bab</a></li>
            <li style="margin-bottom: 12px;"><a href="{{ route('admin.users') }}" style="color: #6b4f96; text-decoration: none; font-weight: 600;">Kelola Pengguna</a></li>
        @endif
        <li style="margin-top: 20px; padding-top: 12px; border-top: 1px solid #e2d9f7;">
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#6b4f96; font-weight:600; cursor:pointer; padding: 8px 12px;">Keluar</button>
            </form>
        </li>
    </ul>
</nav>
