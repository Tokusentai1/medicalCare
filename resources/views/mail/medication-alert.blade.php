<body style="background-color: #f8fafc; font-family: sans-serif; color: #2d3748; text-align: center;">
    <div
        style="max-width: 640px; margin: 40px auto 0; background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

        <!-- Logo -->
        <div style="margin-bottom: 24px;">
            <img src="{{ asset('logo/logo.png') }}" alt="Medical Care" style="max-width: 150px; height: auto;">
        </div>

        <!-- Title -->
        <h1 style="font-size: 24px; font-weight: 600; color: #38a169; margin-bottom: 16px;">
            {{__('medical_history_fields.alert')}}
        </h1>

        <!-- Content Text -->
        <p style="font-size: 18px; margin-bottom: 16px;">{{__('medical_history_fields.alert content')}}</p>

        <!-- Important Message -->
        <p style="background-color: #c6f6d5; padding: 16px; border-radius: 8px; color: #2f855a; font-weight: bold;">
            {{ $messageBody }}
        </p>

        <!-- Footer -->
        <div style="margin-top: 24px;">
            <p style="font-size: 14px; color: #718096;">{{__('medical_history_fields.thanks')}},</p>
            <p style="font-weight: 600; color: #2d3748;">{{ config('app.name') }}</p>
        </div>
    </div>
</body>