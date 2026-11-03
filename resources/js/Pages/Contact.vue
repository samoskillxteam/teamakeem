<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import { MessageCircle, Phone, Mail, MapPin, Clock, Truck, ShieldCheck } from 'lucide-vue-next';

interface Props {
    email?: string;
    phone?: string;
    whatsappNumber?: string;
    address?: string;
    deliveryInfo?: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();
const settings = computed(() => page.props.settings);

const cleanWhatsApp = computed(() => {
    const raw = props.whatsappNumber || settings.value?.whatsapp_number || '2348076187311';
    return raw.replace(/[^\d]/g, '');
});

const directWhatsappUrl = computed(() => {
    return `https://wa.me/${cleanWhatsApp.value}?text=${encodeURIComponent('Hello TeamAkeem! I would like to get in touch regarding an order or spa booking.')}`;
});
</script>

<template>
    <AppLayout 
        title="Contact TeamAkeem Ladies World"
        description="Get in touch with TeamAkeem Ladies Wears and TeamAkeem Beauty & Wellness Spa for orders, inquiries, custom sizing, and home service bookings."
    >
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <!-- Header -->
            <div class="text-center max-w-2xl mx-auto space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-600 dark:text-orange-400">
                    Direct Customer Support
                </span>
                <h1 class="font-bold text-3xl sm:text-5xl text-zinc-900 dark:text-zinc-100 uppercase font-sans">
                    We’d Love to Hear From You
                </h1>
                <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-300">
                    Reach us directly on WhatsApp or call for instantaneous assistance, ladies wear sizing, and spa appointment bookings. We do home service.
                </p>
            </div>

            <!-- Contact Cards Grid -->
            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- WhatsApp Card -->
                <div class="p-8 rounded-3xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-center space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-md">
                            <MessageCircle class="w-7 h-7" />
                        </div>
                        <h3 class="font-serif font-bold text-xl text-emerald-950 dark:text-emerald-200">WhatsApp Fast Chat</h3>
                        <p class="text-xs text-emerald-800 dark:text-emerald-300 leading-relaxed">
                            Our primary and fastest channel for product availability, custom orders, and quick enquiries.
                        </p>
                    </div>

                    <a 
                        :href="directWhatsappUrl" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="py-3 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition-all duration-200 inline-flex items-center justify-center gap-2"
                    >
                        <MessageCircle class="w-4 h-4" />
                        <span>Chat With Us Now</span>
                    </a>
                </div>

                <!-- Phone & Email Card -->
                <div class="p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-[#EADFCF] dark:border-zinc-800 text-center space-y-4 flex flex-col justify-between shadow-sm">
                    <div class="space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-[#EFE8DD] dark:bg-zinc-800 text-[#4A3525] dark:text-[#C59A65] flex items-center justify-center mx-auto">
                            <Phone class="w-7 h-7" />
                        </div>
                        <h3 class="font-serif font-bold text-xl text-zinc-900 dark:text-zinc-100">Phone & Email</h3>
                        <div class="space-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                            <p class="font-semibold text-zinc-800 dark:text-zinc-200">{{ phone || '+234 807 618 7311 / +234 901 654 4574' }}</p>
                            <p>{{ email || 'teemakeemladiesworld@gmail.com' }}</p>
                        </div>
                    </div>

                    <div class="text-xs text-zinc-400 pt-2">
                        Monday – Saturday: 8:00 AM – 7:00 PM
                    </div>
                </div>

                <!-- Location & Delivery Card -->
                <div class="p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-[#EADFCF] dark:border-zinc-800 text-center space-y-4 flex flex-col justify-between shadow-sm">
                    <div class="space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-[#EFE8DD] dark:bg-zinc-800 text-[#4A3525] dark:text-[#C59A65] flex items-center justify-center mx-auto">
                            <MapPin class="w-7 h-7" />
                        </div>
                        <h3 class="font-serif font-bold text-xl text-zinc-900 dark:text-zinc-100">Location & Delivery</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            {{ address || 'Lekki Phase 1, Lagos, Nigeria' }}
                        </p>
                    </div>

                    <div class="text-xs text-zinc-500 dark:text-zinc-400 p-3 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl leading-relaxed">
                        {{ deliveryInfo || 'Nationwide delivery available for clothing items. Same-day fresh confectionery dispatch across Lagos.' }}
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
