<h3>{{ __('messages.wizard.step_one') }}</h3>
<section>
    <h5>{{ __('messages.gbv_registration') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="case_number">{{ __('messages.table_headers.case_number') }} <span class="text-danger">*</span></label>
        <div class="col-lg-10">
            <input class="form-control @error('case_number') is-invalid @enderror" 
                   id="case_number" name="case_number" type="text" required value="{{ $caseNumber }}" readonly>
            @error('case_number')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="institution_name">{{ __('messages.table_headers.institution_name') }} <span class="text-danger">*</span></label>
        <div class="col-lg-10">
            <input class="form-control @error('institution_name') is-invalid @enderror" 
                   id="institution_name" name="institution_name" type="text" required value="{{ $institutionName }}" readonly>
            @error('institution_name')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="region">{{ __('messages.table_headers.region') }} <span class="text-danger">*</span></label>
        <div class="col-lg-10">
            <input class="form-control @error('region') is-invalid @enderror" 
                   id="region" name="region" type="text" required value="{{ $regionName }}" readonly>
            @error('region')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="district">{{ __('messages.table_headers.district') }} <span class="text-danger">*</span></label>
        <div class="col-lg-10">
            <input class="form-control @error('district') is-invalid @enderror" 
                   id="district" name="district" type="text" required value="{{ $districtName }}" readonly>
            @error('district')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="ward">{{ __('messages.table_headers.ward') }} <span class="text-danger">*</span></label>
        <div class="col-lg-10">
            <input class="form-control @error('ward') is-invalid @enderror" 
                   id="ward" name="ward" type="text" required value="{{ $wardName }}" readonly>
            @error('ward')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="campus_branch">{{ __('messages.table_headers.campus_branch') }} <span class="text-danger">*</span></label>
        <div class="col-lg-10">
            <select class="form-control @error('campus_branch') is-invalid @enderror" 
                    id="campus_branch" name="campus_branch" required>
                @if ($campuses->isEmpty())
                    <option value="" selected disabled>{{ __('messages.no_campuses_available') }}</option>
                @else
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->name }}" 
                                {{ $campus->name === 'Main' ? 'selected' : (old('campus_branch') == $campus->name ? 'selected' : '') }}>
                            {{ $campus->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('campus_branch')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-12 control-label"><span class="text-danger">*</span> {{ __('messages.mandatory') }}</label>
    </div>
</section>