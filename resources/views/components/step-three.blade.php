<style>
    .category-title, .other-btn {
        color: #31bbe1;
        text-align: center;
    }
    .category-column {
        border-right: 1px solid #151313c4;
        padding-right: 15px;
        margin-right: 15px;
    }
    .row .col-md-4:last-child .category-column {
        border-right: none;
        padding-right: 0;
        margin-right: 0;
    }
    #wizard-validation-form section:nth-child(3) .form-group {
        border: 2px solid #4a90e2;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    #wizard-validation-form section:nth-child(3) .form-group label.control-label {
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        font-size: 1.1em;
    }
    #wizard-validation-form section:nth-child(3) .form-check {
        display: inline-block;
        width: 30%;
        margin-bottom: 10px;
        padding-right: 15px;
    }
    #wizard-validation-form section:nth-child(3) .form-check-input {
        margin-right: 8px;
    }
    #wizard-validation-form section:nth-child(3) .form-check-label {
        font-size: 0.95em;
        color: #555;
    }
    #wizard-validation-form section:nth-child(3) .other-btn {
        display: block;
        margin-top: 10px;
        color: #4a90e2;
        font-weight: 500;
    }
    #wizard-validation-form section:nth-child(3) .other-btn:hover {
        text-decoration: underline;
    }
</style>

<h3>{{ __('messages.wizard.step_three') }}</h3>
<section>
    <h5>{{ __('messages.incident_details') }}</h5>
    
    <!-- First Row: Aina za Uhalifu, Uhusiano wa Mshukiwa na Muhanga, Sababu za Uhalifu -->
    <div class="row">
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.crime_types') }}</span> <span class="text-danger">*</span></label>
                <div id="crime-types-container-append">
                    @foreach($crimeTypes as $crimeType)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input crime-type-checkbox" 
                                   name="crime_type_ids[]" value="{{ $crimeType->crime_type_id }}" 
                                   id="crime_type_{{ $crimeType->crime_type_id }}"
                                   {{ in_array($crimeType->crime_type_id, old('crime_type_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="crime_type_{{ $crimeType->crime_type_id }}">{{ $crimeType->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.crime_types') }}', '{{ route('incident-details.add-crime-type') }}', 'crime_type_ids[]', '#crime-types-container-append')">{{ __('messages.other') }}</button>
                    @error('crime_type_ids')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.suspect_victim_relationship') }}</span> <span class="text-danger">*</span></label>
                <div id="suspect-victim-relationships-container-append">
                    @foreach($suspectVictimRelationships as $relationship)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input svr-checkbox" 
                                   name="suspect_victim_relationship_ids[]" value="{{ $relationship->suspect_victim_relationship_id }}" 
                                   id="svr_{{ $relationship->suspect_victim_relationship_id }}" 
                                   {{ in_array($relationship->suspect_victim_relationship_id, old('suspect_victim_relationship_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="svr_{{ $relationship->suspect_victim_relationship_id }}">{{ $relationship->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.suspect_victim_relationships') }}', '{{ route('incident-details.add-suspect-victim-relationship') }}', 'suspect_victim_relationship_ids[]', '#suspect-victim-relationships-container-append')">{{ __('messages.other') }}</button>
                    @error('suspect_victim_relationship_ids')
                        <span class="text-danger" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.crime_causes') }}</span></label>
                <div id="crime-causes-container-append">
                    @foreach($crimeCauses as $crimeCause)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input crime-cause-checkbox" 
                                   name="crime_cause_ids[]" value="{{ $crimeCause->crime_cause_id }}" 
                                   id="cause_{{ $crimeCause->crime_cause_id }}" 
                                   {{ in_array($crimeCause->crime_cause_id, old('crime_cause_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cause_{{ $crimeCause->crime_cause_id }}">{{ $crimeCause->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.table_headers.crime_causes') }}', '{{ route('incident-details.add-crime-cause') }}', 'crime_cause_ids[]', '#crime-causes-container-append')">{{ __('messages.other') }}</button>
                </div>
            </div>
        </div>
    </div> <!-- Closing the first row -->

    <div class="row">
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.crime_effects') }}</span></label>
                <div id="crime-effects-container-append">
                    @foreach($crimeEffects as $crimeEffect)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input crime-effect-checkbox" 
                                   name="crime_effect_ids[]" value="{{ $crimeEffect->crime_effect_id }}" 
                                   id="effect_{{ $crimeEffect->crime_effect_id }}" 
                                   {{ in_array($crimeEffect->crime_effect_id, old('crime_effect_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="effect_{{ $crimeEffect->crime_effect_id }}">{{ $crimeEffect->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.table_headers.crime_effects') }}', '{{ route('incident-details.add-crime-effect') }}', 'crime_effect_ids[]', '#crime-effects-container-append')">{{ __('messages.other') }}</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.crime_locations') }}</span></label>
                <div id="crime-locations-container-append">
                    @foreach($crimeLocations as $crimeLocation)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input crime-location-checkbox" 
                                   name="crime_location_ids[]" value="{{ $crimeLocation->crime_location_id }}" 
                                   id="location_{{ $crimeLocation->crime_location_id }}" 
                                   {{ in_array($crimeLocation->crime_location_id, old('crime_location_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="location_{{ $crimeLocation->crime_location_id }}">{{ $crimeLocation->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.table_headers.crime_locations') }}', '{{ route('incident-details.add-crime-location') }}', 'crime_location_ids[]', '#crime-locations-container-append')">{{ __('messages.other') }}</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.initiatives_taken') }}</span></label>
                <div id="initiatives-taken-container-append">
                    @foreach($initiativesTaken as $initiative)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input initiative-taken-checkbox" 
                                   name="initiative_taken_ids[]" value="{{ $initiative->initiative_taken_id }}" 
                                   id="initiative_{{ $initiative->initiative_taken_id }}" 
                                   {{ in_array($initiative->initiative_taken_id, old('initiative_taken_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="initiative_{{ $initiative->initiative_taken_id }}">{{ $initiative->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.table_headers.initiatives_taken') }}', '{{ route('incident-details.add-initiative-taken') }}', 'initiative_taken_ids[]', '#initiatives-taken-container-append')">{{ __('messages.other') }}</button>
                </div>
            </div>
        </div>
    </div> <!-- Closing the second row -->

    <div class="row">
        <div class="col-md-4">
            <div class="form-group category-column">
                <label><span class="category-title">{{ __('messages.table_headers.suspect_jobs') }}</span></label>
                <div id="suspect-jobs-container-append">
                    @foreach($suspectJobs as $suspectJob)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input suspect-job-checkbox" 
                                   name="suspect_job_ids[]" value="{{ $suspectJob->suspect_job_id }}" 
                                   id="job_{{ $suspectJob->suspect_job_id }}" 
                                   {{ in_array($suspectJob->suspect_job_id, old('suspect_job_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="job_{{ $suspectJob->suspect_job_id }}">{{ $suspectJob->name }}</label>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-link other-btn" 
                            onclick="openOtherModal('{{ __('messages.table_headers.suspect_jobs') }}', '{{ route('incident-details.add-suspect-job') }}', 'suspect_job_ids[]', '#suspect-jobs-container-append')">{{ __('messages.other') }}</button>
                </div>
            </div>
        </div>
    </div> <!-- Closing the third row -->

    <div class="form-group row">
        <label class="col-lg-12 control-label"><span class="text-danger">*</span> {{ __('messages.mandatory') }}</label>
    </div>
</section>