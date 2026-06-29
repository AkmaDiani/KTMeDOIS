<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KTM eDOIS') — Internal Review</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <div class="topbar">
        <div class="brand"><span class="dot"></span> KTM eDOIS</div>
        <nav>
            <a href="{{ route('do.index') }}" class="{{ request()->routeIs('do.*') ? 'active' : '' }}">Delivery Orders</a>
            <a href="{{ route('invoice.index') }}" class="{{ request()->routeIs('invoice.*') ? 'active' : '' }}">Invoices</a>
            <a href="{{ route('auditlog.index') }}" class="{{ request()->routeIs('auditlog.*') ? 'active' : '' }}">Audit Log</a>
        </nav>
        <div class="who">
            @if(session('staff_name'))
                <span>{{ session('staff_name') }} &middot; {{ session('staff_role') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout" type="submit">Log out</button>
                </form>
            @endif
        </div>
    </div>

    <div class="page">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    {{ $error }}@if(!$loop->last)<br>@endif
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>
