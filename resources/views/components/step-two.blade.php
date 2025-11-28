<h3>{{ __('messages.wizard.step_two') }}</h3>
<section>
    <h5>{{ __('messages.initial_details') }}</h5>
    @if($fromCommit)
        <div class="form-group row">
            <label class="col-lg-2 control-label">{{ __('messages.table_headers.date_reported') }}</label>
            <div class="col-lg-10">
                <p>{{ $message->report_time ? \Carbon\Carbon::parse($message->report_time)->format('d/m/Y') : __('messages.not_provided') }}</p>
                <input type="hidden" name="report_date" value="{{ $message->report_time ? \Carbon\Carbon::parse($message->report_time)->format('Y-m-d') : '' }}">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-2 control-label">{{ __('messages.table_headers.time_reported') }}</label>
            <div class="col-lg-10">
                <p>{{ $message->report_time ? \Carbon\Carbon::parse($message->report_time)->format('H:i') : __('messages.not_provided') }}</p>
                <input type="hidden" name="report_time" value="{{ $message->report_time ? \Carbon\Carbon::parse($message->report_time)->format('H:i') : '' }}">
            </div>
        </div>
    @else
        <div class="form-group row">
            <label class="col-lg-2 control-label" for="report_date">{{ __('messages.table_headers.date_reported') }}</label>
            <div class="col-lg-10">
                <input type="date" class="form-control @error('report_date') is-invalid @enderror" 
                    id="report_date" name="report_date" value="{{ old('report_date') }}">
                @error('report_date')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <label class="col-lg-2 control-label" for="report_time">{{ __('messages.table_headers.time_reported') }}</label>
            <div class="col-lg-10">
                <input type="time" class="form-control @error('report_time') is-invalid @enderror" 
                    id="report_time" name="report_time" value="{{ old('report_time') }}">
                @error('report_time')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>
    @endif
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="incident_occurred_time">{{ __('messages.table_headers.incident_time') }}</label>
        <div class="col-lg-10">
            <input type="datetime-local" class="form-control @error('incident_occurred_time') is-invalid @enderror" 
                   id="incident_occurred_time" name="incident_occurred_time" 
                   value="{{ request()->query('incident_occurred_time', old('incident_occurred_time')) }}">
            @error('incident_occurred_time')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="complaint_number">{{ __('messages.table_headers.complaint_number') }}</label>
        <div class="col-lg-10">
            <input type="text" class="form-control @error('complaint_number') is-invalid @enderror" 
                   id="complaint_number" name="complaint_number" 
                   value="{{ request()->query('complaint_number', old('complaint_number')) }}">
            @error('complaint_number')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <h5 class="mt-4">{{ __('messages.reporter_details.header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="reporter_id">{{ __('messages.table_headers.reporter_full_name') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->reporter_id)
                <input type="hidden" name="reporter_id" id="reporter_id" value="{{ $message->reporter_id }}">
                <p id="reporter_display">{{ $message->reporter->full_name ?? __('messages.not_specified') }}</p>
            @elseif(!is_null($newReporter))
                <input type="hidden" name="reporter_id" id="reporter_id" value="{{ $newReporter->reporter_id }}">
                <p id="reporter_display">{{ $newReporter->full_name }}</p>
            @else
                <input type="hidden" name="reporter_id" id="reporter_id" value="">
                <p id="reporter_display">{{ __('messages.no_reporter_selected') }}</p>
                <button type="button" class="btn btn-link" onclick="openAddPersonModal('Reporter', '{{ route('reports.add-reporter') }}')">{{ __('messages.add_reporter') }}</button>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="reporter_age">{{ __('messages.table_headers.reporter_age') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->reporter_id)
                <p>{{ $message->reporter->age ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="reporter_age" id="reporter_age" value="{{ $message->reporter->age ?? '' }}">
            @elseif(!is_null($newReporter))
                <p>{{ $newReporter->age ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="reporter_age" id="reporter_age" value="{{ $newReporter->age ?? '' }}">
            @else
                <p>{{ __('messages.not_provided') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="relationship_to_victim">{{ __('messages.table_headers.reporter_relationship') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->reporter_id)
                <p>{{ $message->reporter->relationship_to_victim ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="relationship_to_victim" id="relationship_to_victim" value="{{ $message->reporter->relationship_to_victim ?? '' }}">
            @elseif(!is_null($newReporter))
                <p>{{ $newReporter->relationship_to_victim ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="relationship_to_victim" id="relationship_to_victim" value="{{ $newReporter->relationship_to_victim ?? '' }}">
            @else
                <p>{{ __('messages.not_provided') }}</p>
            @endif
        </div>
    </div>

    <h5 class="mt-4">{{ __('messages.victim_details.header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_id">{{ __('messages.table_headers.victim_full_name') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <input type="hidden" name="victim_id" id="victim_id" value="{{ $message->victim_id }}">
                <p id="victim_display">{{ $message->victim->full_name ?? __('messages.not_specified') }}</p>
            @elseif(!is_null($newVictim))
                <input type="hidden" name="victim_id" id="victim_id" value="{{ $newVictim->victim_id }}">
                <p id="victim_display">{{ $newVictim->full_name }}</p>
            @else
                <p id="victim_display">{{ __('messages.no_victim_selected') }}</p>
                <button type="button" class="btn btn-link" onclick="openAddPersonModal('Victim', '{{ route('reports.add-victim') }}')">{{ __('messages.add_victim') }}</button>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_age">{{ __('messages.table_headers.victim_age') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->age ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_age" id="victim_age" value="{{ $message->victim->age ?? '' }}">
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->age ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_age" id="victim_age" value="{{ $newVictim->age ?? '' }}">
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_disability">{{ __('messages.table_headers.victim_disability_status') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->disability_status == 0 ? __('messages.disability_status.none') : __('messages.disability_status.disabled') }}</p>
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->disability_status == 0 ? __('messages.disability_status.none') : __('messages.disability_status.disabled') }}</p>
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_disability_type">{{ __('messages.table_headers.victim_disability_type') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->disability_status == 0 ? __('messages.disability_type.none') : ($message->victim->disability_type ?? __('messages.disability_type.not_specified')) }}</p>
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->disability_status == 0 ? __('messages.disability_type.none') : ($newVictim->disability_type ?? __('messages.disability_type.not_specified')) }}</p>
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_nationality">{{ __('messages.table_headers.victim_nationality') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->nationality ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_nationality" id="victim_nationality" value="{{ $message->victim->nationality ?? '' }}">
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->nationality ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_nationality" id="victim_nationality" value="{{ $newVictim->nationality ?? '' }}">
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_occupation">{{ __('messages.table_headers.victim_occupation') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->work ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_occupation" id="victim_occupation" value="{{ $message->victim->occupation ?? '' }}">
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->work ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_occupation" id="victim_occupation" value="{{ $newVictim->work ?? '' }}">
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_address">{{ __('messages.table_headers.victim_address') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->address ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_address" id="victim_address" value="{{ $message->victim->address ?? '' }}">
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->address ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_address" id="victim_address" value="{{ $newVictim->address ?? '' }}">
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_phone">{{ __('messages.table_headers.victim_phone') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->phone_number ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_phone" id="victim_phone" value="{{ $message->victim->phone_number ?? '' }}">
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->phone_number ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_phone" id="victim_phone" value="{{ $newVictim->phone_number ?? '' }}">
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="victim_email">{{ __('messages.table_headers.victim_email') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->victim_id)
                <p>{{ $message->victim->email ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_email" id="victim_email" value="{{ $message->victim->email ?? '' }}">
            @elseif(!is_null($newVictim))
                <p>{{ $newVictim->email ?? __('messages.not_provided') }}</p>
                <input type="hidden" name="victim_email" id="victim_email" value="{{ $newVictim->email ?? '' }}">
            @else
                <p>{{ __('messages.no_victim_selected') }}</p>
            @endif
        </div>
    </div>

    <h5 class="mt-4">{{ __('messages.suspect_details.header') }}</h5>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="suspect_id">{{ __('messages.table_headers.suspect_full_name') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->suspect_id)
                <input type="hidden" name="suspect_id[]" id="suspect_id_{{ $message->suspect_id }}" value="{{ $message->suspect_id }}">
                <p id="suspect_display_{{ $message->suspect_id }}">{{ $message->suspect->full_name ?? __('messages.not_specified') }}</p>
            @elseif($newSuspects->isNotEmpty())
                @foreach($newSuspects as $suspect)
                    <input type="hidden" name="suspect_id[]" id="suspect_id_{{ $suspect->suspect_id }}" value="{{ $suspect->suspect_id }}">
                    <p id="suspect_display_{{ $suspect->suspect_id }}">{{ $suspect->full_name }}</p>
                @endforeach
                <button type="button" class="btn btn-link" onclick="openAddPersonModal('Suspect', '{{ route('reports.add-suspect') }}')">{{ __('messages.add_another_suspect') }}</button>
            @else
                 <button type="button" class="btn btn-link" onclick="openAddPersonModal('Suspect', '{{ route('reports.add-suspect') }}')">{{ __('messages.add_suspect') }}</button>
                <p id="suspect_display">{{ __('messages.no_suspect_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-2 control-label" for="suspect_age">{{ __('messages.table_headers.suspect_age') }}</label>
        <div class="col-lg-10">
            @if($fromCommit && $message && $message->suspect_id)
                <p>{{ $message->suspect->age ?? __('messages.not_provided') }}</p>
            @elseif($newSuspects->isNotEmpty())
                @foreach($newSuspects as $suspect)
                    <p>{{ $suspect->age ?? __('messages.not_provided') }}</p>
                @endforeach
            @else
                <p>{{ __('messages.no_suspect_selected') }}</p>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <label class="col-lg-12 control-label"><span class="text-danger">*</span> {{ __('messages.mandatory') }}</label>
    </div>
</section>