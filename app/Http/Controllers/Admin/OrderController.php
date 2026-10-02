<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
    use App\Mail\ReviewRequestMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /** All Orders (both, with the payment_method filter dropdown) */
    public function index()
    {
        return view('admin.orders.index', ['fixedPaymentMethod' => null, 'pageTitle' => 'Orders']);
    }

    /** Online Orders only — separate dedicated page */
    public function online()
    {
        return redirect()->route('admin.orders.index', ['payment_method' => 'online']);
    }

    /** COD Orders only — separate dedicated page */
    public function cod()
    {
        return redirect()->route('admin.orders.index', ['payment_method' => 'cod']);
    }

    public function data(Request $request)
    {
        $orders = Order::with(['deliveryOption'])->select('orders.*');

        // Agar dedicated Online/COD page se aaya hai, to hamesha usi method pe lock rahega
        if ($request->filled('fixed_payment_method')) {
            $orders->where('payment_method', $request->fixed_payment_method);
        } elseif ($request->filled('payment_method')) {
            $orders->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $orders->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $orders->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $orders->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $orders->whereDate('created_at', '<=', $request->date_to);
        }

        return DataTables::of($orders)
            ->editColumn('created_at', fn (Order $o) => $o->created_at->format('d M Y, h:i A'))
            ->editColumn('delivery_date', fn (Order $o) => $o->delivery_date?->format('d M Y') . ($o->delivery_time_slot ? ' (' . $o->delivery_time_slot . ')' : ''))
            ->editColumn('total_amount', fn (Order $o) => '₹' . number_format($o->total_amount, 2))
            ->addColumn('payment_method_badge', function (Order $o) {
                return $o->payment_method === 'online'
                    ? '<span class="badge bg-primary">Online</span>'
                    : '<span class="badge bg-secondary">COD</span>';
            })
            ->addColumn('payment_status_badge', function (Order $o) {
                $colors = ['pending' => 'warning text-dark', 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'secondary'];
                return '<span class="badge bg-' . ($colors[$o->payment_status] ?? 'secondary') . '">' . ucfirst($o->payment_status) . '</span>';
            })
            ->addColumn('status_badge', function (Order $o) {
                $colors = [
                    'pending' => 'warning text-dark', 'confirmed' => 'info text-dark',
                    'preparing' => 'primary', 'out_for_delivery' => 'secondary',
                    'delivered' => 'success', 'cancelled' => 'danger',
                ];
                $label = Order::STATUS_FLOW[$o->status] ?? $o->status;
                return '<span class="badge bg-' . ($colors[$o->status] ?? 'secondary') . '">' . $label . '</span>';
            })
            ->addColumn('actions', function (Order $o) {
                return '<a href="' . route('admin.orders.show', $o) . '" class="btn btn-sm btn-outline-primary">View</a>';
            })
            ->rawColumns(['payment_method_badge', 'payment_status_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'deliveryOption', 'coupon', 'user', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    // public function updateStatus(Request $request, Order $order)
    // {
    //     $validated = $request->validate([
    //         'status' => ['required', 'in:pending,confirmed,preparing,out_for_delivery,delivered,cancelled'],
    //         'cancellation_reason' => ['nullable', 'string', 'max:500'],
    //     ]);

    //     $order->update([
    //         'status' => $validated['status'],
    //         'cancellation_reason' => $validated['status'] === 'cancelled' ? ($validated['cancellation_reason'] ?? null) : null,
    //     ]);

    //     return response()->json(['success' => true, 'message' => 'Order status updated successfully.']);
    // }


    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,preparing,out_for_delivery,delivered,cancelled'],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $previousStatus = $order->status;

        $data = [
            'status' => $validated['status'],
            'cancellation_reason' => $validated['status'] === 'cancelled' ? ($validated['cancellation_reason'] ?? null) : null,
        ];

        // COD: cash is collected at delivery, so mark the payment as paid automatically
        if ($validated['status'] === 'delivered'
            && $order->payment_method === 'cod'
            && $order->payment_status === 'pending') {
            $data['payment_status'] = 'paid';
        }

        $order->update($data);

        // Review request email, only the first time the order becomes delivered
        if ($validated['status'] === 'delivered' && $previousStatus !== 'delivered' && $order->user_id && $order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new ReviewRequestMail($order));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'payment_status' => $order->payment_status,
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        // Online payments are verified by Razorpay, they must never be edited by hand
        if ($order->payment_method !== 'cod') {
            return response()->json([
                'success' => false,
                'message' => 'Online payment status is managed by Razorpay and cannot be changed manually.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed'],
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        return response()->json(['success' => true, 'message' => 'Payment status updated successfully.']);
    }

    public function updateAdminNotes(Request $request, Order $order)
    {
        $validated = $request->validate(['admin_notes' => ['nullable', 'string', 'max:1000']]);
        $order->update(['admin_notes' => $validated['admin_notes']]);
        return response()->json(['success' => true, 'message' => 'Notes updated.']);
    }
}
