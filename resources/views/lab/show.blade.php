@extends('components.main-layout')
@section('title', 'Enter Lab Result')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h2>Enter Lab Result</h2>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h4 class="text-primary">{{ $order->test->test_name }}</h4>
                        <p class="lead">
                            Patient: <strong>{{ $order->visit->patient->name }}</strong><br>
                            Token #{{ $order->visit->id }} • {{ $order->visit->created_at->format('d M Y') }}
                        </p>
                        @if($order->extra_instruction)
                            <div class="alert alert-info">
                                <strong>Doctor Note:</strong> {{ $order->extra_instruction }}
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('lab.result.store', $order) }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Result Value (Numeric)</label>
                                <input type="number" step="0.01" name="result_value" class="form-control form-control-lg" 
                                       value="{{ old('result_value', $order->result?->result_value) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Normal Range</label>
                                <input type="text" name="normal_range" class="form-control" 
                                       value="{{ old('normal_range', $order->test->normal_range) }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Result Text (if non-numeric)</label>
                                <textarea name="result_text" rows="3" class="form-control">{{ old('result_text', $order->result?->result_text) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Remarks</label>
                                <textarea name="remarks" rows="3" class="form-control" placeholder="Any additional comments...">{{ old('remarks', $order->result?->remarks) }}</textarea>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-lg">
                                Save Result & Complete
                            </button>
                            <a href="{{ route('lab.index') }}" class="btn btn-secondary btn-lg px-5 ms-3">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection