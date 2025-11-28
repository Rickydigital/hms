<h3>{{ __('messages.wizard.step_four') }}</h3>
<section>
    <h5>{{ __('messages.coordinator_details.header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="coordinator_name">{{ __('messages.table_headers.coordinator_name') }}</label>
        <div class="col-lg-10">
            <input type="text" class="form-control @error('coordinator_name') is-invalid @enderror" 
                   id="coordinator_name" name="coordinator_name" value="{{ auth()->user()->name ?? old('coordinator_name') }}" readonly>
            @error('coordinator_name')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="coordinator_phone">{{ __('messages.table_headers.coordinator_phone') }}</label>
        <div class="col-lg-10">
            <input type="text" class="form-control @error('coordinator_phone') is-invalid @enderror" 
                   id="coordinator_phone" name="coordinator_phone" value="{{ auth()->user()->phone_number ?? old('coordinator_phone') }}" readonly>
            @error('coordinator_phone')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="coordinator_email">{{ __('messages.table_headers.coordinator_email') }}</label>
        <div class="col-lg-10">
            <input type="email" class="form-control @error('coordinator_email') is-invalid @enderror" 
                   id="coordinator_email" name="coordinator_email" value="{{ auth()->user()->email ?? old('coordinator_email') }}" readonly>
            @error('coordinator_email')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <h5 class="mt-4">{{ __('messages.coordinator_details.notes_header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="coordinator_notes">{{ __('messages.table_headers.coordinator_notes') }}</label>
        <div class="col-lg-10">
            <textarea class="form-control @error('coordinator_notes') is-invalid @enderror" 
                      id="coordinator_notes" name="coordinator_notes">{{ old('coordinator_notes') }}</textarea>
            @error('coordinator_notes')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <h5 class="mt-4">{{ __('messages.coordinator_details.follow_up_header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="follow_up_details">{{ __('messages.table_headers.follow_up_details') }}</label>
        <div class="col-lg-10">
            <textarea class="form-control @error('follow_up_details') is-invalid @enderror" 
                      id="follow_up_details" name="follow_up_details">{{ old('follow_up_details') }}</textarea>
            @error('follow_up_details')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <h5 class="mt-4">{{ __('messages.coordinator_details.additional_info_header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="additional_info">{{ __('messages.table_headers.additional_info') }}</label>
        <div class="col-lg-10">
            <textarea class="form-control @error('additional_info') is-invalid @enderror" 
                      id="additional_info" name="additional_info">{{ old('additional_info') }}</textarea>
            @error('additional_info')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
</section>