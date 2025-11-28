@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="" alt="Mana Dispensary HMS" height="40">
        @endcomponent
    @endslot

    {{-- Body --}}
    <div style="text-align: center; padding: 30px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h1 style="margin: 0; font-size: 28px;">Welcome to Mana Dispensary HMS!</h1>
        <p style="font-size: 18px; margin: 10px 0 0;">Your staff account has been created</p>
    </div>

    <div style="padding: 30px 20px; background-color: #f8f9fa;">
        <p style="font-size: 16px; color: #333;">Dear <strong>{{ $user->name }}</strong>,</p>

        <p>Congratulations! Your account at <strong>Mana Dispensary HMS</strong> has been successfully created.</p>

        @component('mail::panel')
            <div style="text-align: left;">
                <p style="margin: 8px 0; font-size: 15px;">
                    <strong>Employee Code:</strong> <code style="background:#e9ecef;padding:2px 8px;border-radius:4px;">{{ $user->employee_code }}</code>
                </p>
                <p style="margin: 8px 0; font-size: 15px;">
                    <strong>Email / Username:</strong> {{ $user->email }}
                </p>
                <p style="margin: 8px 0; font-size: 15px;">
                    <strong>Temporary Password:</strong> 
                    <code style="background:#fff3cd;color:#856404;padding:4px 10px;border-radius:6px;font-weight:bold;font-size:16px;">
                        {{ $plainPassword }}
                    </code>
                </p>
                <p style="margin: 15px 0 5px; color: #d63384; font-weight: bold;">
                    Please change your password immediately after first login!
                </p>
            </div>
        @endcomponent

        <p style="margin-top: 25px;">You can now access the system using the credentials above.</p>
    </div>

    @component('mail::button', ['url' => route('login')])
        Login to Mana Dispensary HMS
    @endcomponent

    <div style="padding: 20px; background-color: #f1f3f5; text-align: center; color: #666; font-size: 14px;">
        <p style="margin: 10px 0;">
            <strong>Department:</strong> {{ $user->department }}<br>
            <strong>Role:</strong> {{ $user->roles->first()->name ?? 'Staff' }}
        </p>
        <hr style="border: 1px dashed #ddd; margin: 20px 0;">
        <p style="margin: 0; color: #888;">
            This is an automated message from Mana Dispensary HMS<br>
            © {{ date('Y') }} Mana Dispensary HMS. All rights reserved.
        </p>
    </div>

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset
@endcomponent