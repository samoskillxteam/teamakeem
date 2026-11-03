<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderEnquiry;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OrderEnquiryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = OrderEnquiry::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $enquiries = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'enquiries' => $enquiries,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        $products = Product::with(['category', 'variants', 'media'])
            ->where('is_available', true)
            ->orderBy('name')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'brand_name' => $p->category?->brand_name ?? 'TeamAkeem',
                    'effective_price' => (float) $p->effective_price,
                    'primary_image_url' => $p->primary_image_url,
                    'variants' => $p->variants->map(fn ($v) => [
                        'id' => $v->id,
                        'title' => $v->title,
                        'price' => (float) $v->effective_price,
                    ]),
                ];
            });

        return Inertia::render('Admin/Orders/Create', [
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:191',
            'customer_phone'   => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'customer_notes'   => 'nullable|string|max:1000',
            'status'           => 'required|in:pending,contacted,completed,cancelled',
            'delivery_fee'     => 'nullable|numeric|min:0',
            'items'            => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.variant_title'=> 'nullable|string',
            'items.*.brand_name'   => 'nullable|string',
        ]);

        $reference = 'TAK-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        $deliveryFee = (float) ($validated['delivery_fee'] ?? 0);

        $processedItems = [];
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $unitPrice = (float) $item['unit_price'];
            $qty = (int) $item['quantity'];
            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;

            $processedItems[] = [
                'key' => (string) Str::uuid(),
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'brand_name' => $item['brand_name'] ?? 'TeamAkeem',
                'variant_title' => $item['variant_title'] ?? null,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'formatted_unit_price' => '₦' . number_format($unitPrice),
                'formatted_line_total' => '₦' . number_format($lineTotal),
                'image_url' => $item['image_url'] ?? null,
                'selected_options' => [],
                'addons' => [],
            ];
        }

        $total = $subtotal + $deliveryFee;

        // WhatsApp message builder
        $businessPhone = Setting::get('whatsapp_number', '2348076187311');
        $cleanPhone = preg_replace('/[^\d]/', '', $businessPhone);
        if (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 11) {
            $cleanPhone = '234' . substr($cleanPhone, 1);
        }

        $message = "🛍️ *TEAMAKEEM ORDER ENQUIRY*\n";
        $message .= "Ref: *#{$reference}*\n";
        $message .= "Customer: {$validated['customer_name']} ({$validated['customer_phone']})\n";
        if (!empty($validated['customer_address'])) {
            $message .= "Address: {$validated['customer_address']}\n";
        }
        $message .= "\n*Items:*\n";
        foreach ($processedItems as $idx => $pi) {
            $n = $idx + 1;
            $opt = $pi['variant_title'] ? " ({$pi['variant_title']})" : '';
            $message .= "{$n}. {$pi['product_name']}{$opt} x{$pi['quantity']} - {$pi['formatted_line_total']}\n";
        }
        $message .= "\n*Subtotal:* ₦" . number_format($subtotal);
        if ($deliveryFee > 0) {
            $message .= "\n*Delivery:* ₦" . number_format($deliveryFee);
        }
        $message .= "\n*Total:* ₦" . number_format($total);

        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . rawurlencode($message);

        $enquiry = OrderEnquiry::create([
            'reference'        => $reference,
            'status'           => $validated['status'],
            'customer_name'    => $validated['customer_name'],
            'customer_phone'   => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_notes'   => $validated['customer_notes'] ?? null,
            'items'            => $processedItems,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'total'            => $total,
            'whatsapp_message' => $message,
            'whatsapp_url'     => $whatsappUrl,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
        ]);

        return redirect()->route('admin.orders.index')->with('success', "Order #{$reference} created successfully.");
    }

    public function show(OrderEnquiry $enquiry): Response
    {
        return Inertia::render('Admin/Orders/Show', [
            'enquiry' => $enquiry,
        ]);
    }

    public function edit(OrderEnquiry $enquiry): Response
    {
        $products = Product::with(['category', 'variants', 'media'])
            ->where('is_available', true)
            ->orderBy('name')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'brand_name' => $p->category?->brand_name ?? 'TeamAkeem',
                    'effective_price' => (float) $p->effective_price,
                    'primary_image_url' => $p->primary_image_url,
                    'variants' => $p->variants->map(fn ($v) => [
                        'id' => $v->id,
                        'title' => $v->title,
                        'price' => (float) $v->effective_price,
                    ]),
                ];
            });

        return Inertia::render('Admin/Orders/Edit', [
            'enquiry' => $enquiry,
            'products' => $products,
        ]);
    }

    public function update(Request $request, OrderEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:191',
            'customer_phone'   => 'required|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'customer_notes'   => 'nullable|string|max:1000',
            'status'           => 'required|in:pending,contacted,completed,cancelled',
            'delivery_fee'     => 'nullable|numeric|min:0',
            'items'            => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.variant_title'=> 'nullable|string',
            'items.*.brand_name'   => 'nullable|string',
        ]);

        $deliveryFee = (float) ($validated['delivery_fee'] ?? 0);
        $processedItems = [];
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $unitPrice = (float) $item['unit_price'];
            $qty = (int) $item['quantity'];
            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;

            $processedItems[] = [
                'key' => $item['key'] ?? (string) Str::uuid(),
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'brand_name' => $item['brand_name'] ?? 'TeamAkeem',
                'variant_title' => $item['variant_title'] ?? null,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'formatted_unit_price' => '₦' . number_format($unitPrice),
                'formatted_line_total' => '₦' . number_format($lineTotal),
                'image_url' => $item['image_url'] ?? null,
                'selected_options' => $item['selected_options'] ?? [],
                'addons' => $item['addons'] ?? [],
            ];
        }

        $total = $subtotal + $deliveryFee;

        $enquiry->update([
            'status'           => $validated['status'],
            'customer_name'    => $validated['customer_name'],
            'customer_phone'   => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_notes'   => $validated['customer_notes'] ?? null,
            'items'            => $processedItems,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'total'            => $total,
        ]);

        return redirect()->route('admin.orders.index')->with('success', "Order #{$enquiry->reference} updated successfully.");
    }

    public function destroy(OrderEnquiry $enquiry): RedirectResponse
    {
        $ref = $enquiry->reference;
        $enquiry->delete();

        return redirect()->route('admin.orders.index')->with('success', "Order #{$ref} deleted successfully.");
    }

    public function updateStatus(Request $request, OrderEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed,cancelled',
        ]);

        $enquiry->update(['status' => $validated['status']]);

        return back()->with('success', "Order enquiry status updated to {$validated['status']}.");
    }
}
