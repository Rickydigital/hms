{{-- resources/views/pharmacy/sales/receipt.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - {{ $sale->invoice_no }}</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { font-size: 12px; }
        }
        .receipt { max-width: 80mm; margin: 0 auto; font-family: monospace; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="receipt bg-white shadow-lg p-4 rounded">

        <div class="text-center mb-4">
            <h4 class="fw-bold">MANA DISPENSARY</h4>
            <p class="mb-0">Your Trusted Pharmacy</p>
            <small>Tel: 0755 123 456</small>
        </div>

        <hr>

        <div class="text-center mb-3">
            <h5>RECEIPT</h5>
            <p><strong>{{ $sale->invoice_no }}</strong></p>
            <small>{{ $sale->sold_at->format('d M Y h:i A') }}</small>
        </div>

        @if($sale->customer_name)
        <p><strong>Customer:</strong> {{ $sale->customer_name }} @if($sale->customer_phone)({{ $sale->customer_phone }})@endif</p>
        @endif

        <table class="table table-sm table-borderless">
            <thead>
                <tr class="border-bottom">
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->medicine->medicine_name }}</strong><br>
                        <small>Batch: {{ $item->batch_no }} • Exp: {{ $item->expiry_date->format('M/y') }}</small>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">Tsh {{ number_format($item->total_price) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr>

        <div class="text-end fw-bold fs-5">
            <div>Total: Tsh {{ number_format($sale->total_amount) }}</div>
            <div>Paid: Tsh {{ number_format($sale->amount_paid) }}</div>
            <div class="text-success">Change: Tsh {{ number_format($sale->change_due) }}</div>
        </div>

        <hr>

        <div class="text-center small">
            <p>Served by: <strong>{{ $sale->soldBy->name }}</strong></p>
            <p>Thank you for your purchase!</p>
            <p>Goods sold are non-returnable</p>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-teal">
                <i class="bi bi-printer"></i> Print Receipt
            </button>
            <a href="{{ route('pharmacy.sales.create') }}" class="btn btn-success ms-2">
                <i class="bi bi-plus-circle"></i> New Sale
            </a>
        </div>
    </div>
</div>
</body>
</html>