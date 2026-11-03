<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Shirt, Cake, Sparkles, Heart, CheckCircle2, MessageCircle } from 'lucide-vue-next';

interface Props {
    story?: string;
    clothingBrand: string;
    treatsBrand: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();
const settings = computed(() => page.props.settings);

const whatsappUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348076187311';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello TeamAkeem! I would like to learn more about your services and products.')}`;
});
</script>

<template>
    <AppLayout 
        title="About TeamAkeem Ladies World"
        description="The story and philosophy behind TeamAkeem Ladies Wears and TeamAkeem Beauty & Wellness Spa."
    >
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-600 dark:text-orange-400">
                    Our Story &amp; Sanctuary
                </span>
                <h1 class="font-bold text-4xl sm:text-5xl text-zinc-900 dark:text-zinc-100 uppercase font-sans">
                    Empowering Feminine Beauty &amp; Wellness
                </h1>
                <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed font-light">
                    Founded on the belief that every woman deserves comfort, confidence, and holistic rejuvenation. Healing starts in your mind.
                </p>
            </div>

            <!-- Main Narrative -->
            <div class="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 text-zinc-700 dark:text-zinc-300 leading-relaxed text-sm sm:text-base">
                    <p>
                        {{ story || 'TeamAkeem Ladies World is a premier sanctuary dedicated exclusively to feminine grace, comfort, and holistic well-being. We bring together two essential passions under one roof: a carefully curated collection of luxury ladies wear, intimates, loungewear and modest fashion through TeamAkeem Ladies Wears, and rejuvenating self-care, holistic therapies and premium cosmetics at TeamAkeem Ladies Beauty & Wellness Spa.' }}
                    </p>
                    <p>
                        We envisioned a single haven where our ladies can effortlessly discover supportive, seamless intimates, luxurious silk nightwear and robes, alongside restorative wellness therapies: full body massages, O-shot rejuvenation &amp; normal repair, royal pedicures, gentle waxing, dental whitening, traditional Northern Gyaran Jiki, and affordable beauty cosmetics.
                    </p>
                    <p>
                        With dedicated salon suites and professional home service available across Nigeria, we ensure every sister receives attentive, confidential, and compassionate care.
                    </p>

                    <div class="pt-4 flex items-center gap-4">
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow flex items-center gap-2"
                        >
                            <MessageCircle class="w-4 h-4" />
                            <span>Say Hello on WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Two Pillars -->
                <div class="space-y-6">
                    <!-- Pillar 1: TeamAkeem Ladies Wears -->
                    <div class="p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-orange-500/20 dark:border-zinc-800 shadow-sm flex items-start gap-5">
                        <div class="p-3.5 rounded-2xl bg-orange-500/10 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 shrink-0">
                            <Shirt class="w-7 h-7" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-bold text-xl text-zinc-900 dark:text-zinc-100 uppercase">{{ clothingBrand }}</h3>
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Seamless sculpting underwear, wireless support bras, pure silk robes, delicate floral lace bralettes, and modest loungewear sets crafted for all-day confidence.
                            </p>
                            <Link :href="route('clothing.index')" class="inline-block text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline pt-1">
                                View Ladies Wears Catalogue →
                            </Link>
                        </div>
                    </div>

                    <!-- Pillar 2: TeamAkeem Beauty & Wellness Spa -->
                    <div class="p-8 rounded-3xl bg-white dark:bg-zinc-900 border border-orange-500/20 dark:border-zinc-800 shadow-sm flex items-start gap-5">
                        <div class="p-3.5 rounded-2xl bg-orange-500/10 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 shrink-0">
                            <Sparkles class="w-7 h-7" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-bold text-xl text-zinc-900 dark:text-zinc-100 uppercase">{{ treatsBrand }}</h3>
                            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Professional body massages, O-shot rejuvenation &amp; normal repair, pedicures, waxing, herbal body scrubs, dental whitening, authentic Gyaran Jiki spa, and glowing skincare cosmetics.
                            </p>
                            <Link :href="route('spa.index')" class="inline-block text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline pt-1">
                                View Spa &amp; Services Catalogue →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
