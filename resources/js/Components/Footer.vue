<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import { useTheme } from '@/composables/useTheme';
import { MessageCircle, MapPin, Mail, Phone, Heart } from 'lucide-vue-next';

const page = usePage<PageProps>();
const { isDark } = useTheme();
const settings = computed(() => page.props.settings);

const logoSrc = computed(() => {
    if (isDark.value) {
        return settings.value?.logo_dark_url || '/images/logo_dark.png';
    }
    return settings.value?.logo_url || '/images/logo.png';
});

const whatsappUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348076187311';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello TeamAkeem! I would like to make an enquiry.')}`;
});
</script>

<template>
    <footer class="bg-[#F5F5F7] dark:bg-[#111112] text-zinc-600 dark:text-zinc-400 border-t border-black/[0.06] dark:border-white/[0.08] transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
                <!-- Col 1: Brand Info -->
                <div class="lg:col-span-2 space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        <img 
                            :src="logoSrc" 
                            alt="TeamAkeem Ladies World" 
                            class="h-9 w-9 object-contain rounded-full"
                        />
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-bold text-sm tracking-tight text-zinc-900 dark:text-white uppercase">
                                TeamAkeem
                            </span>
                            <span class="text-[10px] tracking-widest uppercase text-orange-500 font-semibold">
                                Ladies World
                            </span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 max-w-sm leading-relaxed">
                        Curated ladies wear, intimates &amp; loungewear from <span class="text-zinc-900 dark:text-white font-medium">TeamAkeem Ladies Wears</span> and holistic beauty therapies at <span class="text-zinc-900 dark:text-white font-medium">TeamAkeem Beauty &amp; Wellness Spa</span>. We do home service.
                    </p>

                    <div class="pt-1">
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-full bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all active:scale-95"
                        >
                            <MessageCircle class="w-3.5 h-3.5" />
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Col 2: TeamAkeem Ladies Wears -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Ladies Wears
                    </h4>
                    <ul class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <li>
                            <Link :href="route('clothing.index')" class="hover:text-black dark:hover:text-white transition-colors">All Ladies Wears</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'brief' })" class="hover:text-black dark:hover:text-white transition-colors">Seamless Underwear</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'bra' })" class="hover:text-black dark:hover:text-white transition-colors">Wireless &amp; Push-Up Bras</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'robe' })" class="hover:text-black dark:hover:text-white transition-colors">Silk Robes &amp; Nightwear</Link>
                        </li>
                        <li>
                            <Link :href="route('clothing.index', { search: 'shape' })" class="hover:text-black dark:hover:text-white transition-colors">Contour Shapewear</Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: TeamAkeem Beauty & Wellness Spa -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Beauty &amp; Spa
                    </h4>
                    <ul class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <li>
                            <Link :href="route('spa.index')" class="hover:text-black dark:hover:text-white transition-colors">All Spa Services</Link>
                        </li>
                        <li>
                            <Link :href="route('spa.index', { type: 'massage' })" class="hover:text-black dark:hover:text-white transition-colors">Body Massages</Link>
                        </li>
                        <li>
                            <Link :href="route('spa.index', { type: 'treatment' })" class="hover:text-black dark:hover:text-white transition-colors">O-Shot &amp; Normal Repair</Link>
                        </li>
                        <li>
                            <Link :href="route('spa.index', { type: 'pedicure' })" class="hover:text-black dark:hover:text-white transition-colors">Pedicure &amp; Manicure</Link>
                        </li>
                        <li>
                            <Link :href="route('spa.index', { type: 'traditional_spa' })" class="hover:text-black dark:hover:text-white transition-colors">Traditional Gyaran Jiki</Link>
                        </li>
                        <li>
                            <Link :href="route('spa.index', { type: 'cosmetics' })" class="hover:text-black dark:hover:text-white transition-colors">Cosmetics &amp; Skincare</Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 4: Quick Links & Contact -->
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Information
                    </h4>
                    <div class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-start gap-2">
                            <MapPin class="w-3.5 h-3.5 shrink-0 mt-0.5 text-orange-500" />
                            <span>{{ settings?.address || 'Kano & Lagos, Nigeria' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Phone class="w-3.5 h-3.5 shrink-0 text-orange-500" />
                            <span>{{ settings?.contact_phone || '+234 807 618 7311' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Mail class="w-3.5 h-3.5 shrink-0 text-orange-500" />
                            <span>{{ settings?.contact_email || 'teemakeemladiesworld@gmail.com' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col space-y-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <Link :href="route('privacy')" class="hover:text-black dark:hover:text-white">Privacy Policy</Link>
                        <Link :href="route('terms')" class="hover:text-black dark:hover:text-white">Terms of Service</Link>
                        <Link :href="route('admin.login')" class="hover:text-black dark:hover:text-white pt-1">Admin Portal</Link>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-12 pt-6 border-t border-black/[0.06] dark:border-white/[0.08] flex flex-col sm:flex-row items-center justify-between text-[11px] text-zinc-400 dark:text-zinc-500 gap-3">
                <p>© {{ new Date().getFullYear() }} {{ settings?.business_name || 'TeamAkeem Ladies World' }}. All rights reserved.</p>
                <p>
                    Empowering feminine elegance, comfort &amp; holistic wellness. Healing starts in your mind.
                </p>
            </div>
        </div>
    </footer>
</template>
