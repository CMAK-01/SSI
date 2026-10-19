<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM-CML</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8f9fa; color: #1f2937; }
        header { background: #0f172a; color: white; padding: 12px 20px; }
        header a { color: white; text-decoration: none; margin-right: 12px; }
        .container { max-width: 1200px; margin: 20px auto; background: #fff; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fff; }
        .btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 8px 12px; border-radius: 6px; border: 0; cursor: pointer; }
        .btn-secondary { background: #475569; }
        .btn-danger { background: #dc2626; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .field { margin-bottom: 12px; }
        label { display: block; font-weight: 600; margin-bottom: 4px; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f1f5f9; }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 12px; }
        .alert-success { background: #dcfce7; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; border: 1px solid #fca5a5; }
    </style>
</head>
<body>
<header>
    @if(\App\Support\AppLogo::exists())
        <img src="{{ \App\Support\AppLogo::url() }}" alt="Logo" style="height: 40px; vertical-align: middle; margin-right: 10px; background: #fff; padding: 2px 6px; border-radius: 4px;">
    @endif
    <strong>SIM-CML</strong>
    <nav style="margin-top:8px;">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('rapport-mensuels.index') }}">Rapport mensuel</a>
        <a href="{{ route('parc-informatiques.index') }}">Parc Informatique Mine</a>
        <a href="{{ route('rapport-diagnostics.index') }}">Rapport de diagnostic</a>
    </nav>
</header>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>
</body>
</html>
