<style>
    .review-container {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    .review-header h5 {
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        font-size: 1.5em;
    }

    .review-header p {
        color: #555;
        font-size: 1.1em;
        line-height: 1.5;
        margin-bottom: 25px;
    }

    .review-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .btn-rounded {
        border-radius: 20px;
        padding: 10px 25px;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-primary {
        background-color: #4a90e2;
        border-color: #4a90e2;
    }

    .btn-primary:hover {
        background-color: #357abd;
        border-color: #357abd;
    }

    #wizard-validation-form section:nth-child(5) .w-list {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #wizard-validation-form section:nth-child(5) .w-list li {
        margin-bottom: 10px;
        font-size: 1em;
        color: #333;
    }

    #wizard-validation-form section:nth-child(5) .w-list li b {
        display: inline-block;
        width: 250px;
        font-weight: 600;
        color: #4a90e2;
    }

    #wizard-validation-form section:nth-child(5) .w-list li span {
        color: #555;
    }

    #wizard-validation-form section:nth-child(5) .btn-primary {
        background-color: #4a90e2;
        border-color: #4a90e2;
        padding: 10px 20px;
        font-size: 1em;
        border-radius: 5px;
    }

    #wizard-validation-form section:nth-child(5) .btn-primary:hover {
        background-color: #357abd;
        border-color: #357abd;
    }

    .section h5 {
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        border-bottom: 2px solid #4a90e2;
        padding-bottom: 5px;
    }

    .section p {
        margin: 0 0 10px;
    }

    .review-container {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .review-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .review-header h5 {
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }

    .review-header p {
        color: #555;
    }

    .review-section {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .review-section:last-child {
        border-bottom: none;
    }

    .review-section h6 {
        font-weight: bold;
        color: #4a90e2;
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 1px solid #4a90e2;
    }

    .review-item {
        display: flex;
        margin-bottom: 10px;
    }

    .review-label {
        font-weight: 600;
        color: #333;
        min-width: 250px;
    }

    .review-value {
        color: #555;
        flex-grow: 1;
    }

    .suspect-divider {
        margin: 15px 0;
        border-color: #eee;
    }

    .review-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
    }

    .btn-rounded {
        border-radius: 20px;
        padding: 10px 25px;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-primary {
        background-color: #4a90e2;
        border-color: #4a90e2;
    }

    .btn-primary:hover {
        background-color: #357abd;
        border-color: #357abd;
    }
</style>

<h3>{{ __('messages.wizard.step_five') }}</h3>
<section>
    <div class="review-container">
        <div class="review-header">
            <h5>{{ __('messages.review.header') }}</h5>
            <p>{{ __('messages.review.description') }}</p>
        </div>

        <!-- Section 3.0: Registration Details -->
        <div class="review-section">
            <h6>{{ __('messages.gbv_registration') }}</h6>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.case_number') }}</span>
                <span class="review-value">{{ $caseNumber }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.institution_name') }}</span>
                <span class="review-value">{{ $institutionName }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.region') }}</span>
                <span class="review-value">{{ $regionName }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.district') }}</span>
                <span class="review-value">{{ $districtName }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.ward') }}</span>
                <span class="review-value">{{ $wardName }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.campus_branch') }}</span>
                <span class="review-value">
                    @if($campuses->isNotEmpty())
                        {{ old('campus_branch', $campuses->firstWhere('name', 'Main') ? 'Main' : $campuses->first()->name) }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Section 3.1-3.2: Initial Details -->
        <div class="review-section">
            <h6>{{ __('messages.initial_details') }}</h6>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.date_reported') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->report_time)
                        {{ \Carbon\Carbon::parse($message->report_time)->format('d/m/Y') }}
                    @else
                        {{ old('report_date') ? \Carbon\Carbon::parse(old('report_date'))->format('d/m/Y') : __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.time_reported') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->report_time)
                        {{ \Carbon\Carbon::parse($message->report_time)->format('H:i') }}
                    @else
                        {{ old('report_time') ?? __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.incident_time') }}</span>
                <span class="review-value">
                    @if(old('incident_occurred_time'))
                        {{ \Carbon\Carbon::parse(old('incident_occurred_time'))->format('d/m/Y H:i') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.complaint_number') }}</span>
                <span class="review-value">{{ old('complaint_number') ?? __('messages.not_specified') }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.reporter_full_name') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->reporter_id)
                        {{ $message->reporter->full_name ?? __('messages.not_specified') }}
                    @elseif(!is_null($newReporter))
                        {{ $newReporter->full_name ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.reporter_gender') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->reporter_id)
                        {{ $message->reporter->gender ?? __('messages.not_specified') }}
                    @elseif(!is_null($newReporter))
                        {{ $newReporter->gender ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.reporter_age') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->reporter_id)
                        {{ $message->reporter->age ?? __('messages.not_specified') }}
                    @elseif(!is_null($newReporter))
                        {{ $newReporter->age ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.reporter_relationship') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->reporter_id)
                        {{ $message->reporter->relationship_to_victim ?? __('messages.not_specified') }}
                    @elseif(!is_null($newReporter))
                        {{ $newReporter->relationship_to_victim ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_full_name') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->full_name ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->full_name ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_gender') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->gender ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->gender ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_age') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->age ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->age ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_disability_status') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->disability_status == 0 ? __('messages.disability_status.none') : __('messages.disability_status.disabled') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->disability_status == 0 ? __('messages.disability_status.none') : __('messages.disability_status.disabled') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_disability_type') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->disability_status == 0 ? __('messages.disability_type.none') : ($message->victim->disability_type ?? __('messages.not_specified')) }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->disability_status == 0 ? __('messages.disability_type.none') : ($newVictim->disability_type ?? __('messages.not_specified')) }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_nationality') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->nationality ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->nationality ?? __('messages.not_specified') }}
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_education_level') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->education_level ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->education_level ?? __('messages.not_specified') }}
                    @else
                        {{ old('victim_education_level') ?? __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_occupation') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->work ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->work ?? __('messages.not_specified') }}
                    @else
                        {{ old('victim_occupation') ?? __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_address') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->address ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->address ?? __('messages.not_specified') }}
                    @else
                        {{ old('victim_address') ?? __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_phone') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->phone_number ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->phone_number ?? __('messages.not_specified') }}
                    @else
                        {{ old('victim_phone') ?? __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.victim_email') }}</span>
                <span class="review-value">
                    @if($fromCommit && $message && $message->victim_id)
                        {{ $message->victim->email ?? __('messages.not_specified') }}
                    @elseif(!is_null($newVictim))
                        {{ $newVictim->email ?? __('messages.not_specified') }}
                    @else
                        {{ old('victim_email') ?? __('messages.not_specified') }} <!-- Fixed: Corrected old('victim_phone') to old('victim_email') -->
                    @endif
                </span>
            </div>
            @if(($fromCommit && $message && $message->suspect_id) || $newSuspects->isNotEmpty())
                @if($fromCommit && $message && $message->suspect_id)
                    <hr class="suspect-divider">
                    <div class="review-item">
                        <span class="review-label">{{ __('messages.table_headers.suspect_full_name') }}</span>
                        <span class="review-value">{{ $message->suspect->full_name ?? __('messages.not_specified') }}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">{{ __('messages.table_headers.suspect_gender') }}</span>
                        <span class="review-value">{{ $message->suspect->gender ?? __('messages.not_specified') }}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">{{ __('messages.table_headers.suspect_age') }}</span>
                        <span class="review-value">{{ $message->suspect->age ?? __('messages.not_specified') }}</span>
                    </div>
                @endif
                @if($newSuspects->isNotEmpty())
                    @foreach($newSuspects as $index => $suspect)
                        <hr class="suspect-divider">
                        <div class="review-item">
                            <span class="review-label">{{ __('messages.table_headers.suspect_full_name') }} ({{ $index + 1 }}):</span>
                            <span class="review-value">{{ $suspect->full_name ?? __('messages.not_specified') }}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">{{ __('messages.table_headers.suspect_gender') }}</span>
                            <span class="review-value">{{ $suspect->gender ?? __('messages.not_specified') }}</span>
                        </div>
                        <div class="review-item">
                            <span class="review-label">{{ __('messages.table_headers.suspect_age') }}</span>
                            <span class="review-value">{{ $suspect->age ?? __('messages.not_specified') }}</span>
                        </div>
                    @endforeach
                @endif
            @else
                <hr class="suspect-divider">
                <div class="review-item">
                    <span class="review-label">{{ __('messages.table_headers.suspect_full_name') }}</span>
                    <span class="review-value">{{ __('messages.not_specified') }}</span>
                </div>
            @endif
        </div>

        {{--  <!-- Section 3.4: Incident Details -->
        <div class="review-section">
            <h6>{{ __('messages.incident_details') }}</h6>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.crime_types') }}</span>
                <span class="review-value">
                    @if(old('crime_type_ids'))
                        @foreach(old('crime_type_ids', []) as $crimeTypeId)
                            {{ $crimeTypes->find($crimeTypeId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.suspect_victim_relationship') }}</span>
                <span class="review-value">
                    @if(old('suspect_victim_relationship_ids'))
                        @foreach(old('suspect_victim_relationship_ids', []) as $relationshipId)
                            {{ $suspectVictimRelationships->find($relationshipId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.crime_causes') }}</span>
                <span class="review-value">
                    @if(old('crime_cause_ids'))
                        @foreach(old('crime_cause_ids', []) as $crimeCauseId)
                            {{ $crimeCauses->find($crimeCauseId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.crime_effects') }}</span>
                <span class="review-value">
                    @if(old('crime_effect_ids'))
                        @foreach(old('crime_effect_ids', []) as $crimeEffectId)
                            {{ $crimeEffects->find($crimeEffectId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.crime_locations') }}</span>
                <span class="review-value">
                    @if(old('crime_location_ids'))
                        @foreach(old('crime_location_ids', []) as $crimeLocationId)
                            {{ $crimeLocations->find($crimeLocationId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.initiatives_taken') }}</span>
                <span class="review-value">
                    @if(old('initiative_taken_ids'))
                        @foreach(old('initiative_taken_ids', []) as $initiativeId)
                            {{ $initiativesTaken->find($initiativeId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.suspect_jobs') }}</span>
                <span class="review-value">
                    @if(old('suspect_job_ids'))
                        @foreach(old('suspect_job_ids', []) as $suspectJobId)
                            {{ $suspectJobs->find($suspectJobId)->name ?? __('messages.not_specified') }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    @else
                        {{ __('messages.not_specified') }}
                    @endif
                </span>
            </div>
        </div>  --}}

        <!-- Section 3.3, 3.5-3.7: Coordinator Details -->
        <div class="review-section">
            <h6>{{ __('messages.coordinator_details.header') }}</h6>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.coordinator_name') }}</span>
                <span class="review-value">{{ auth()->user()->name ?? old('coordinator_name', __('messages.not_specified')) }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.coordinator_phone') }}</span>
                <span class="review-value">{{ auth()->user()->phone_number ?? old('coordinator_phone', __('messages.not_specified')) }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.coordinator_email') }}</span>
                <span class="review-value">{{ auth()->user()->email ?? old('coordinator_email', __('messages.not_specified')) }}</span>
            </div>
            {{--  <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.coordinator_notes') }}</span>
                <span class="review-value">{{ old('coordinator_notes') ?? __('messages.not_specified') }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.follow_up_details') }}</span>
                <span class="review-value">{{ old('follow_up_details') ?? __('messages.not_specified') }}</span>
            </div>
            <div class="review-item">
                <span class="review-label">{{ __('messages.table_headers.additional_info') }}</span>
                <span class="review-value">{{ old('additional_info') ?? __('messages.not_specified') }}</span>
            </div>  --}}
        </div>

        <!-- Action Buttons -->
        <div class="review-actions">
            {{--  <button type="button" id="review-previous-btn" class="btn btn-secondary btn-rounded">{{ __('messages.wizard.previous') }}</button>  --}}
            <button type="button" id="review-finish-btn" class="btn btn-primary btn-rounded">{{ __('messages.wizard.finish') }}</button>
        </div>
    </div>
</section>