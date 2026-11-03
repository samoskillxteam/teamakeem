<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    ArrowLeft, 
    Plus, 
    Trash2, 
    Save, 
    ShoppingBag, 
    User, 
    Truck, 
    FileText,
    Sparkles
} from 'lucide-vue-next';

interface ProductItem {
    id: number;
    name: string;
    brand_name: string;
    effective_price: number;
    primary_image_url?: string;
    variants: Array<{ id: number; title: string; price: number }>;
}

interface Props {
    products: ProductItem[];
}

const props = defineProps<Props>();

interface FormItem {
    product_id: number | null;
    product_name: string;
    brand_name: string;
    variant_title: string;
    unit_price: number;
    quantity: number;
    image_url?: string;
}

const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_address: '',
    customer_notes: '',
    status: 'pending',
    delivery_fee: 0,
    items: [
        {
            product_id: null,
            product_name: '',
            brand_name: 'TeamAkeem',
            variant_title: '',
            unit_price: 0,
            quantity: 1,
            image_url: '',
        }
    ] as FormItem[],
});

function addItem() {
    form.items.push({
        product_id: null,
        product_name: '',
        brand_name: 'TeamAkeem',
        variant_title: '',
        unit_price: 0,
        quantity: 1,
        image_url: '',
    });
}

function removeItem(index: number) {
    if (form.items.length <= 1) return;
    form.items.splice(index, 1);
}

function onProductSelected(index: number, event: Event) {
    const selectedId = Number((event.target as HTMLSelectElement).value);
    const product = props.products.find(p => p.id === selectedId);
    if (!product) return;

    form.items[index].product_id = product.id;
    form.items[index].product_name = product.name;
    form.items[index].brand_name = product.brand_name;
    form.items[index].unit_price = product.effective_price;
    form.items[index].image_url = product.primary_image_url ?? '';

    if (product.variants.length > 0) {
        form.items[index].variant_title = product.variants[0].title;
        form.items[index].unit_price = product.variants[0].price;
    } else {
        form.items[index].variant_title = '';
    }
}

function onVariantSelected(index: number, event: Event) {
    const title = (event.target as HTMLSelectElement).value;
    const item = form.items[index];
    const product = props.products.find(p => p.id === item.product_id);
    if (!product) return;

    const variant = product.variants.find(v => v.title === title);
    if (variant) {
        item.variant_title = variant.title;
        item.unit_price = variant.price;
    }
}

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (Number(item.unit_price || 0) * Number(item.quantity || 1)), 0);
});

const total = computed(() => {
    return subtotal.value + Number(form.delivery_fee || 0);
});

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount);
}

function submit() {
    form.post(route('admin.orders.store'));
}
</script>

<template>
    <AdminLayout title="Create New Order">
        <div class="max-w-4xl mx-auto space-y-6 pb-12">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('admin.orders.index')" 
                        class="flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 mb-2 transition-colors">
                        <ArrowLeft class="w-4 h-4" /> Back to Order Enquiries
                    </Link>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-sans">Create Order / Booking</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Record a walk-in, phone order, or custom spa session enquiry.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Customer Details -->
                <section class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 flex items-center justify-center">
                            <User class="w-4 h-4" />
                        </div>
                        <div>
                            <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">Customer Details</h2>
                            <p class="text-xs text-zinc-500">Who is this order or booking for?</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                Customer Name <span class="text-rose-500">*</span>
                            </label>
                            <input v-model="form.customer_name" type="text" required placeholder="e.g. Fatima Aminu"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-2.5 text-sm" />
                            <p v-if="form.errors.customer_name" class="text-xs text-rose-500 mt-1">{{ form.errors.customer_name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                Phone Number <span class="text-rose-500">*</span>
                            </label>
                            <input v-model="form.customer_phone" type="tel" required placeholder="e.g. 08076187311"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-2.5 text-sm" />
                            <p v-if="form.errors.customer_phone" class="text-xs text-rose-500 mt-1">{{ form.errors.customer_phone }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                Delivery / Appointment Address
                            </label>
                            <input v-model="form.customer_address" type="text" placeholder="e.g. Kano Studio or Home Service Address"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-2.5 text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                Initial Status
                            </label>
                            <select v-model="form.status"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-2.5 text-sm">
                                <option value="pending">Pending</option>
                                <option value="contacted">Contacted</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                Customer Notes / Preferences
                            </label>
                            <textarea v-model="form.customer_notes" rows="2" placeholder="e.g. Prefers home service on Saturday 2pm"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-2.5 text-sm resize-none"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Order Items -->
                <section class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 flex items-center justify-center">
                                <ShoppingBag class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">Order Items & Services</h2>
                                <p class="text-xs text-zinc-500">Pick from existing catalog or enter custom service item</p>
                            </div>
                        </div>
                        <button type="button" @click="addItem"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 hover:bg-orange-100 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors">
                            <Plus class="w-3.5 h-3.5" /> Add Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(item, index) in form.items" :key="index"
                            class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/40 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                                <!-- Catalog Dropdown -->
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Select from Catalog</label>
                                    <select @change="onProductSelected(index, $event)"
                                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs text-zinc-800 dark:text-zinc-200">
                                        <option value="">— Custom Item / Or Pick Below —</option>
                                        <option v-for="p in props.products" :key="p.id" :value="p.id">
                                            {{ p.name }} ({{ formatCurrency(p.effective_price) }})
                                        </option>
                                    </select>
                                </div>

                                <!-- Item Name Input -->
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Item / Service Name *</label>
                                    <input v-model="item.product_name" type="text" required placeholder="e.g. Full Body Massage"
                                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs" />
                                </div>

                                <!-- Variant / Option -->
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Option / Variant</label>
                                    <input v-model="item.variant_title" type="text" placeholder="e.g. Home Service / XL / 60 Mins"
                                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs" />
                                </div>

                                <!-- Quantity -->
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Qty</label>
                                    <input v-model.number="item.quantity" type="number" min="1" required
                                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs" />
                                </div>

                                <!-- Unit Price -->
                                <div class="md:col-span-4">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-1">Unit Price (₦)</label>
                                    <input v-model.number="item.unit_price" type="number" min="0" required
                                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-xs" />
                                </div>

                                <!-- Line Total & Delete -->
                                <div class="md:col-span-4 flex items-center justify-between pt-3 md:pt-0">
                                    <div>
                                        <span class="block text-[10px] uppercase font-bold text-zinc-400">Line Total</span>
                                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                                            {{ formatCurrency((item.unit_price || 0) * (item.quantity || 1)) }}
                                        </span>
                                    </div>
                                    <button type="button" @click="removeItem(index)" :disabled="form.items.length <= 1"
                                        class="p-1.5 text-rose-500 hover:text-rose-700 disabled:opacity-30 rounded-lg">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary & Delivery -->
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div class="w-full md:w-64">
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                Delivery Fee (₦)
                            </label>
                            <input v-model.number="form.delivery_fee" type="number" min="0"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-2 text-sm" />
                        </div>

                        <div class="text-right space-y-1">
                            <div class="text-xs text-zinc-500">
                                Subtotal: <strong class="text-zinc-800 dark:text-zinc-200">{{ formatCurrency(subtotal) }}</strong>
                            </div>
                            <div class="text-xs text-zinc-500">
                                Delivery: <strong class="text-zinc-800 dark:text-zinc-200">{{ formatCurrency(form.delivery_fee || 0) }}</strong>
                            </div>
                            <div class="text-lg font-bold text-orange-600 dark:text-orange-400 font-sans">
                                Total: {{ formatCurrency(total) }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('admin.orders.index')"
                        class="px-5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Cancel
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors disabled:opacity-50 shadow-sm">
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? 'Saving...' : 'Create Order' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

