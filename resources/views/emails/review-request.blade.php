<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background:#f7f2ec; padding:20px; margin:0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden;">
        <tr>
            <td style="background:#A31E42; padding:24px; text-align:center; color:#fff; font-size:1.3rem; font-weight:bold;">
                🎂 {{ config('app.name') }}
            </td>
        </tr>
        <tr>
            <td style="padding:24px 30px;">
                <h2 style="color:#2B2B2B; margin-top:0;">Hi {{ $order->customer_name }}, how was your order?</h2>
                <p style="color:#555;">Your order <strong>#{{ $order->order_number }}</strong> has been delivered. We'd love to hear what you think, and your photos help other customers too.</p>

                <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse; margin:16px 0;">
                    @foreach ($order->items->whereNotNull('product_id') as $item)
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="font-size:0.9rem;">
                                <strong>{{ $item->product_name }}</strong>
                                @if ($item->weight_label)<br><span style="color:#888; font-size:0.8rem;">{{ $item->weight_label }}</span>@endif
                            </td>
                        </tr>
                    @endforeach
                </table>

                <div style="text-align:center; margin-top:24px;">
                    <a href="{{ url('/account/reviews') }}" style="background:#A31E42; color:#fff; padding:12px 28px; border-radius:6px; text-decoration:none; font-weight:bold;">
                        Write a Review
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <td style="background:#f7f2ec; padding:16px; text-align:center; color:#999; font-size:0.75rem;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>