<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; margin: 0; padding: 20px; background: #f0f8ff; }
        .card { width: 85.6mm; height: 53.98mm; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; overflow: hidden; }
        .logo { text-align: center; font-size: 22px; font-weight: bold; margin-bottom: 8px; }
        .id { font-size: 24px; font-weight: bold; text-align: center; letter-spacing: 3px; }
        .name { font-size: 18px; text-align: center; margin: 10px 0; }
        .details { font-size: 12px; line-height: 1.4; }
        .expiry { position: absolute; bottom: 15px; right: 15px; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 20px; font-size: 11px; }
        .qr { position: absolute; bottom: 15px; left: 15px; width: 60px; height: 60px; background: white; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">{{ setting('hospital_name', 'Mana Medical Dispensary Hospital') }}</div>
        <div class="id">{{ $patient->patient_id }}</div>
        <div class="name">{{ $patient->name }}</div>
        <div class="details">
            Age: {{ $patient->age }} • {{ $patient->gender }}<br>
            Phone: {{ $patient->phone }}
        </div>
        <div class="expiry">Valid till {{ $patient->expiry_date->format('d/m/Y') }}</div>
        <div class="qr"></div>
    </div>
</body>
</html>