<body style="background-color: #f8fafc; font-family: sans-serif; color: #2d3748;">
    <div
        style="max-width: 640px; margin: 0 auto; background-color: #38a169; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <h1 style="font-size: 24px; font-weight: 600; color: #ffffff; margin-bottom: 16px;">
            {{__('medical_history_fields.alert')}}
        </h1>
        <p style="font-size: 18px; margin-bottom: 16px; color: #ffffff;">{{__('medical_history_fields.alert content')}}
        </p>
        <p style="padding: 16px; border-radius: 8px; color: #2d3748; font-weight: bold;">{{ $messageBody }}</p>
        <div style="margin-top: 24px;">
            <p style="font-size: 14px; color: #ffffff;">{{__('medical_history_fields.thanks')}},</p>
            <p style="font-weight: 600; color: #ffffff;">{{ config('app.name') }}</p>
        </div>
    </div>
</body>