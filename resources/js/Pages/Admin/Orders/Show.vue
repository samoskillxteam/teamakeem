<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import type { OrderEnquiry } from '@/types';
import { 
    MessageCircle, 
    ExternalLink, 
    ArrowLeft, 
    Clock, 
    CheckCircle, 
    XCircle, 
    Phone, 
    Edit, 
    Trash2, 
    AlertTriangle,
    X
} from 'lucide-vue-next';

interface Props {
    enquiry: OrderEnquiry;
}
const props = defineProps<Props>();

const statusForm = useForm({ status: props.enquiry.status });

function updateStatus() {
    statusForm.patch(`/admin/orders/${props.enquiry.id}/status`, { preserveScroll: true });
}

const statusConfig: Record<string, { label: string; icon: any; classes: string }> = {
    pending:   { label: 'Pending',   icon: Clock,         classes: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
    contacted: { label: 'Contacted', icon: Phone,         classes: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
    completed: { label: 'Completed', icon: CheckCircle,   classes: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
    cancelled: { label: 'Cancelled', icon: XCircle,       classes: 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400' },
};

function formatCurrency(amount: number) {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 0 }).format(amount);
}

function formatDate(date: string) {
    return new Date(date).toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' });
}

const currentStatus = statusConfig[props.enquiry.status] ?? statusConfig['pending'];

const showDeleteModal = ref(false);

function executeDelete() {
    router.delete(`/admin/orders/${props.enquiry.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
}
</script>

<template>
    <AdminLayout :title="`Enquiry — ${enquiry.reference}`">
        <div class="max-w-4xl mx-auto space-y-6 pb-12">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <Link :href="route('admin.orders.index')" 
                        class="flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 mb-2 transition-colors">
                        <ArrowLeft class="w-4 h-4" /> All Enquiries
                    </Link>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-mono text-orange-600 dark:text-orange-400">
                        {{ enquiry.reference }}
                    </h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Received {{ formatDate(enquiry.created_at) }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <span :class="['flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold', currentStatus.classes]">
                        <component :is="currentStatus.icon" class="w-3.5 h-3.5" />
                        {{ currentStatus.label }}
                    </span>
                    <Link :href="`/admin/orders/${enquiry.id}/edit`"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl text-xs font-bold transition-colors">
                        <Edit class="w-3.5 h-3.5" /> Edit
                    </Link>
                    <button type="button" @click="showDeleteModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold transition-colors">
                        <Trash2 class="w-3.5 h-3.5" /> Delete
                    </button>
                    <a v-if="enquiry.whatsapp_url" :href="enquiry.whatsapp_url" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-2 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors shadow-sm">
                        <MessageCircle class="w-4 h-4" /> WhatsApp
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Customer info -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-3 shadow-xs">
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">Customer Details</h2>
                    <dl class="space-y-2 text-xs">
                        <div>
                            <dt class="text-zinc-400 font-semibold uppercase text-[10px]">Name</dt>
                            <dd class="font-bold text-zinc-900 dark:text-zinc-100 text-sm mt-0.5">{{ enquiry.customer_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-400 font-semibold uppercase text-[10px]">Phone</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100 mt-0.5">{{ enquiry.customer_phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-400 font-semibold uppercase text-[10px]">Delivery / Booking Address</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300 mt-0.5">{{ enquiry.customer_address ?? '—' }}</dd>
                        </div>
                        <div v-if="enquiry.customer_notes">
                            <dt class="text-zinc-400 font-semibold uppercase text-[10px]">Notes</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300 italic mt-0.5">{{ enquiry.customer_notes }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Order summary -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-3 shadow-xs">
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">Order Financials</h2>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Subtotal</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(enquiry.subtotal) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-500 dark:text-zinc-400">Delivery Fee</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300">{{ enquiry.delivery_fee > 0 ? formatCurrency(enquiry.delivery_fee) : 'Free / Not applicable' }}</dd>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <dt class="font-bold text-zinc-900 dark:text-zinc-100">Total</dt>
                            <dd class="font-bold text-base text-orange-600 dark:text-orange-400">{{ formatCurrency(enquiry.total) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Status update -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 space-y-3 shadow-xs">
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">Update Status</h2>
                    <form @submit.prevent="updateStatus" class="space-y-3">
                        <select v-model="statusForm.status"
                            class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100">
                            <option value="pending">Pending</option>
                            <option value="contacted">Contacted</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button type="submit" :disabled="statusForm.processing"
                            class="w-full py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors disabled:opacity-60">
                            {{ statusForm.processing ? 'Saving…' : 'Save Status' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Items table -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-xs">
                <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">Order Items ({{ enquiry.items?.length ?? 0 }})</h2>
                    <Link :href="`/admin/orders/${enquiry.id}/edit`" class="text-xs text-orange-600 font-semibold hover:underline">
                        Edit items
                    </Link>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <div v-for="item in enquiry.items" :key="item.key" class="px-5 py-4 flex gap-4 items-start">
                        <img v-if="item.image_url" :src="item.image_url" :alt="item.product_name"
                            class="w-14 h-14 object-cover rounded-xl border border-zinc-200 dark:border-zinc-800 shrink-0" />
                        <div v-else class="w-14 h-14 rounded-xl bg-orange-50 dark:bg-orange-950/30 flex shrink-0 items-center justify-center text-orange-500 text-xl font-bold">
                            🛍️
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ item.product_name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ item.brand_name }}</p>
                            <div v-if="item.variant_title" class="text-xs font-medium text-orange-600 dark:text-orange-400 mt-0.5">
                                Variant: {{ item.variant_title }}
                            </div>
                            <div v-if="Object.keys(item.selected_options ?? {}).length > 0" class="flex flex-wrap gap-1.5 mt-1">
                                <span v-for="(val, key) in item.selected_options" :key="key"
                                    class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-md text-[11px]">
                                    {{ key }}: {{ val }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ item.formatted_unit_price ?? formatCurrency(item.unit_price) }} × {{ item.quantity }}</p>
                            <p class="font-bold text-sm text-zinc-900 dark:text-zinc-100">{{ item.formatted_line_total ?? formatCurrency(item.line_total) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp message preview -->
            <div v-if="enquiry.whatsapp_message" class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">WhatsApp Message Preview</h2>
                    <a v-if="enquiry.whatsapp_url" :href="enquiry.whatsapp_url" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                        <ExternalLink class="w-3.5 h-3.5" /> Open Chat
                    </a>
                </div>
                <pre class="bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/50 dark:border-emerald-800/30 rounded-xl p-4 text-xs text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap font-mono overflow-x-auto leading-relaxed">{{ enquiry.whatsapp_message }}</pre>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl max-w-md w-full border border-zinc-200 dark:border-zinc-800 p-6 space-y-4 shadow-xl">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center">
                        <AlertTriangle class="w-5 h-5" />
                    </div>
                    <button @click="showDeleteModal = false" class="text-zinc-400 hover:text-zinc-600">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Delete Order #{{ enquiry.reference }}?</h3>
                    <p class="text-xs text-zinc-500 mt-1">This will permanently remove this enquiry record.</p>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="showDeleteModal = false"
                        class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="button" @click="executeDelete"
                        class="px-4 py-2 rounded-xl bg-rose-600 text-white text-xs font-semibold">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
