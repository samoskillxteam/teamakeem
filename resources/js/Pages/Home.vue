<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { Category, Product, PageProps } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { useTheme } from '@/composables/useTheme';
import { 
    Shirt, 
    Cake, 
    ArrowRight, 
    Sparkles, 
    MessageCircle, 
    CheckCircle2, 
    ShieldCheck, 
    Truck, 
    Star 
} from 'lucide-vue-next';

// ── Dynamic Clothing Purply-Pinkish Theme (mirrors Clothing/Index.vue) ────
interface PurplyPinkTheme {
    id: string;
    bgGradientLight: string;
    bgGradientDark: string;
    glowColor1: string;
    glowColor2: string;
    borderColorLight: string;
    borderColorDark: string;
    tagBgLight: string;
    tagBgDark: string;
    tagTextLight: string;
    tagTextDark: string;
    tagBorder: string;
    cardBgDark: string;
    cardBorderDark: string;
    brandPillBg: string;
}

const clothingThemes: PurplyPinkTheme[] = [
    {
        id: 'teamakeem_orange',
        bgGradientLight: 'linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 50%, #FED7AA 100%)',
        bgGradientDark: 'linear-gradient(135deg, #2A1103 0%, #1A0901 50%, #0E0501 100%)',
        glowColor1: 'rgba(249, 87, 0, 0.28)',
        glowColor2: 'rgba(234, 88, 12, 0.35)',
        borderColorLight: 'rgba(249, 87, 0, 0.22)',
        borderColorDark: 'rgba(249, 87, 0, 0.35)',
        tagBgLight: 'rgba(249, 87, 0, 0.12)',
        tagBgDark: 'rgba(249, 87, 0, 0.25)',
        tagTextLight: '#C2410C',
        tagTextDark: '#FDBA74',
        tagBorder: 'rgba(249, 87, 0, 0.3)',
        cardBgDark: '#1C0B02',
        cardBorderDark: 'rgba(249, 87, 0, 0.35)',
        brandPillBg: '#EA580C',
    },
    {
        id: 'teamakeem_amber',
        bgGradientLight: 'linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 50%, #FDE68A 100%)',
        bgGradientDark: 'linear-gradient(135deg, #271704 0%, #190E02 50%, #0D0701 100%)',
        glowColor1: 'rgba(245, 158, 11, 0.26)',
        glowColor2: 'rgba(217, 119, 6, 0.35)',
        borderColorLight: 'rgba(245, 158, 11, 0.22)',
        borderColorDark: 'rgba(245, 158, 11, 0.35)',
        tagBgLight: 'rgba(217, 119, 6, 0.12)',
        tagBgDark: 'rgba(245, 158, 11, 0.25)',
        tagTextLight: '#B45309',
        tagTextDark: '#FDE68A',
        tagBorder: 'rgba(245, 158, 11, 0.3)',
        cardBgDark: '#1B0E02',
        cardBorderDark: 'rgba(245, 158, 11, 0.35)',
        brandPillBg: '#D97706',
    },
    {
        id: 'teamakeem_noir',
        bgGradientLight: 'linear-gradient(135deg, #FAFAFA 0%, #F4F4F5 50%, #E4E4E7 100%)',
        bgGradientDark: 'linear-gradient(135deg, #18181B 0%, #121214 50%, #09090B 100%)',
        glowColor1: 'rgba(249, 115, 22, 0.25)',
        glowColor2: 'rgba(249, 87, 0, 0.3)',
        borderColorLight: 'rgba(249, 115, 22, 0.2)',
        borderColorDark: 'rgba(249, 115, 22, 0.3)',
        tagBgLight: 'rgba(24, 24, 27, 0.08)',
        tagBgDark: 'rgba(249, 115, 22, 0.2)',
        tagTextLight: '#18181B',
        tagTextDark: '#FED7AA',
        tagBorder: 'rgba(249, 115, 22, 0.3)',
        cardBgDark: '#121214',
        cardBorderDark: 'rgba(249, 115, 22, 0.3)',
        brandPillBg: '#F97316',
    },
];

const currentClothingTheme = ref<PurplyPinkTheme>(clothingThemes[0]);
const isPageDark = ref(false);

const updatePageDark = () => {
    isPageDark.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
    updatePageDark();
    try {
        const lastIdx = Number(sessionStorage.getItem('clothing_theme_idx') ?? -1);
        // Sync with whatever Clothing/Index chose — read same key, don't re-advance
        const idx = ((lastIdx < 0 ? 0 : lastIdx)) % clothingThemes.length;
        currentClothingTheme.value = clothingThemes[idx];
    } catch {
        currentClothingTheme.value = clothingThemes[0];
    }
    const observer = new MutationObserver(() => updatePageDark());
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

interface Props {
    categories: Category[];
    featuredClothing: Product[];
    featuredConfectionery: Product[];
    newArrivals: Product[];
    aboutStory?: string;
}

const props = defineProps<Props>();
const page = usePage<PageProps>();
const { isDark } = useTheme();
const settings = computed(() => page.props.settings);

const clothingCategory = computed(() => props.categories.find(c => c.slug === 'clothing'));
const confectioneryCategory = computed(() => props.categories.find(c => c.slug === 'confectionery' || c.slug === 'spa'));

const whatsappUrl = computed(() => {
    const raw = settings.value?.whatsapp_number || '2348076187311';
    const clean = raw.replace(/[^\d]/g, '');
    return `https://wa.me/${clean}?text=${encodeURIComponent('Hello TeamAkeem! I would like to place an order or book a spa session.')}`;
});

const heroDesktopSrc = computed(() => isDark.value ? '/images/Teemakeem_Hero_Desktop_Dark.png' : '/images/Teemakeem_Hero_Desktop_Light.png');
const heroMobileSrc = computed(() => isDark.value ? '/images/Teemakeem_Hero_Mobile_Dark.png' : '/images/Teemakeem_Hero_Mobile_Light.png');
</script>

<template>
    <AppLayout 
        :title="settings?.business_name || 'TeamAkeem Ladies World'"
        :description="settings?.tagline || 'Luxury Ladies Wears, Intimates & Holistic Wellness Spa'"
    >
        <!-- HERO BANNER (Responsive Full Fill: Mobile vs Desktop) -->
        <section class="relative w-full overflow-hidden bg-[#FAF8F5] dark:bg-[#100E0C]">
            <picture class="block w-full">
                <!-- Mobile Screens (< 640px) -->
                <source media="(max-width: 639px)" :srcset="heroMobileSrc" />
                <!-- Desktop Screens (>= 640px) -->
                <img 
                    :src="heroDesktopSrc" 
                    alt="TeamAkeem Ladies World — Ladies Wears &amp; Beauty Wellness Spa" 
                    class="w-full h-auto block object-cover object-center"
                    loading="eager"
                />
            </picture>
        </section>

        <!-- TWO MAIN CATEGORY HERO SECTIONS (side-by-side, hero-style design) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">

                <!-- 1. TeamAkeem Ladies Wears -->
                <div
                    class="relative overflow-hidden rounded-3xl border transition-colors duration-700 ease-out"
                    :style="{
                        background: isPageDark ? currentClothingTheme.bgGradientDark : currentClothingTheme.bgGradientLight,
                        borderColor: isPageDark ? currentClothingTheme.borderColorDark : currentClothingTheme.borderColorLight,
                    }"
                >
                    <div
                        class="absolute -top-16 -left-16 w-56 h-56 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                        :style="{ backgroundColor: currentClothingTheme.glowColor1 }"
                    ></div>
                    <div
                        class="absolute -bottom-16 -right-16 w-56 h-56 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                        :style="{ backgroundColor: currentClothingTheme.glowColor2 }"
                    ></div>

                    <div class="relative p-5 sm:p-7 space-y-3">
                        <!-- Category pill -->
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide border transition-all duration-500"
                            :style="{
                                backgroundColor: isPageDark ? currentClothingTheme.tagBgDark : currentClothingTheme.tagBgLight,
                                color: isPageDark ? currentClothingTheme.tagTextDark : currentClothingTheme.tagTextLight,
                                borderColor: currentClothingTheme.tagBorder,
                            }"
                        >
                            <Shirt class="w-3 h-3" />
                            <span>Fashion &amp; Apparel Collection</span>
                        </div>

                        <!-- Heading + tagline -->
                        <div>
                            <h2 class="font-bold text-2xl sm:text-3xl text-zinc-900 dark:text-white tracking-tight leading-tight uppercase font-sans">
                                TeamAkeem Ladies Wears
                            </h2>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 font-normal mt-1">
                                Everything ladies: seamless underwear, silk nightwear, robes &amp; everyday confidence.
                            </p>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-300/80 leading-relaxed">
                            {{ clothingCategory?.description || 'Explore everything ladies: premium supportive wireless bras, seamless briefs, silk kimono robes, lace lingerie sets, and flattering everyday loungewear.' }}
                        </p>

                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <div
                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-black/40 border font-medium backdrop-blur-md"
                                :style="{ borderColor: isPageDark ? currentClothingTheme.borderColorDark : currentClothingTheme.borderColorLight }"
                            >
                                <Sparkles class="w-3 h-3 text-orange-500" />
                                <span>Seamless Intimates</span>
                            </div>
                            <div
                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-black/40 border font-medium backdrop-blur-md"
                                :style="{ borderColor: isPageDark ? currentClothingTheme.borderColorDark : currentClothingTheme.borderColorLight }"
                            >
                                <MessageCircle class="w-3 h-3 text-emerald-500" />
                                <span>WhatsApp Order</span>
                            </div>
                        </div>

                        <!-- Hero image card -->
                        <div
                            class="relative group rounded-2xl overflow-hidden border transition-all duration-700"
                            :style="{
                                backgroundColor: isPageDark ? currentClothingTheme.cardBgDark : 'rgba(255,255,255,0.9)',
                                borderColor: isPageDark ? currentClothingTheme.cardBorderDark : currentClothingTheme.borderColorLight,
                            }"
                        >
                            <div class="aspect-[16/9] overflow-hidden bg-zinc-950">
                                <img
                                    src="/images/products/ladies/ladies_1.jpg"
                                    alt="TeamAkeem Ladies Wears"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-medium border border-white/10 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                    <span>Exclusive Underwear &amp; Robes</span>
                                </div>
                                <span
                                    class="px-2 py-0.5 rounded-full text-white text-[9px] font-semibold border border-white/20 transition-colors duration-500"
                                    :style="{ backgroundColor: currentClothingTheme.brandPillBg }"
                                >TeamAkeem Wears</span>
                            </div>
                        </div>

                        <!-- CTA -->
                        <Link
                            :href="route('clothing.index')"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-orange-600 hover:bg-orange-700 text-white font-medium text-xs transition-all duration-200 active:scale-95 shadow-sm"
                        >
                            <span>Explore Ladies Wears</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <!-- 2. TeamAkeem Beauty & Wellness Spa (Warm Luxury Glow) -->
                <div class="relative overflow-hidden rounded-3xl border border-orange-500/20 dark:border-orange-500/30 bg-gradient-to-br from-[#FFF8F0] via-[#FDF2E9] to-[#FCE7D6] dark:from-[#200E04] dark:via-[#160902] dark:to-[#0B0401]">
                    <div class="absolute -top-16 -left-16 w-56 h-56 bg-orange-500/15 dark:bg-orange-500/25 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-amber-500/20 dark:bg-amber-500/30 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative p-5 sm:p-7 space-y-3">
                        <!-- Category pill -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500/10 dark:bg-orange-500/25 text-orange-600 dark:text-orange-300 text-xs font-semibold tracking-wide border border-orange-500/20">
                            <Sparkles class="w-3 h-3 text-orange-500" />
                            <span>Beauty &amp; Wellness Spa</span>
                        </div>

                        <!-- Heading + tagline -->
                        <div>
                            <h2 class="font-bold text-2xl sm:text-3xl text-zinc-900 dark:text-white tracking-tight leading-tight uppercase font-sans">
                                TeamAkeem Spa &amp; Cosmetics
                            </h2>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 font-normal mt-1">
                                Body massage, O-shot, pedicures, waxing, dental &amp; cosmetics. We do home service!
                            </p>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-300/80 leading-relaxed">
                            {{ confectioneryCategory?.description || 'Indulge in professional wellness treatments: full body massages, O-shot & intimate repair, pedicures, waxing, herbal scrubs, cosmetic dental whitening, traditional Gyaran Jiki, and affordable beauty cosmetics. We do home service.' }}
                        </p>

                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300">
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-orange-500/20 dark:border-orange-500/30 font-medium backdrop-blur-md">
                                <Sparkles class="w-3 h-3 text-orange-500" />
                                <span>We Do Home Service</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/80 dark:bg-[#1E040C]/80 border border-orange-500/20 dark:border-orange-500/30 font-medium backdrop-blur-md">
                                <MessageCircle class="w-3 h-3 text-emerald-500" />
                                <span>Healing Starts In Your Mind</span>
                            </div>
                        </div>

                        <!-- Hero image card -->
                        <div class="relative group rounded-2xl overflow-hidden bg-white/90 dark:bg-[#1C0B02] border border-orange-500/20 dark:border-orange-500/35">
                            <div class="aspect-[16/9] overflow-hidden bg-zinc-950">
                                <img
                                    src="/images/products/spa/spa_body_massage.jpg"
                                    alt="TeamAkeem Ladies Beauty & Wellness Spa"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[10px] font-medium border border-white/10 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                    <span>Relaxing Massages &amp; Gyaran Jiki</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-orange-600 text-white text-[9px] font-semibold border border-white/20">TeamAkeem Spa</span>
                            </div>
                        </div>

                        <!-- CTA -->
                        <Link
                            :href="route('spa.index')"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-100 font-medium text-xs transition-all duration-200 active:scale-95 shadow-sm"
                        >
                            <span>Explore Spa &amp; Services</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

            </div>
        </section>
                <!-- FEATURED CLOTHING SECTION -->
        <section v-if="featuredClothing.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-orange-500 block mb-1">
                        TeamAkeem Ladies Wears
                    </span>
                    <h2 class="font-bold text-2xl sm:text-3xl tracking-tight text-zinc-900 dark:text-white uppercase font-sans">
                        Featured Underwear &amp; Apparel
                    </h2>
                </div>
                <Link 
                    :href="route('clothing.index')" 
                    class="inline-flex items-center gap-1 text-sm font-semibold text-orange-600 dark:text-orange-400 hover:underline"
                >
                    <span>View all ladies wears</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard 
                    v-for="product in featuredClothing" 
                    :key="product.id" 
                    :product="product" 
                />
            </div>
        </section>

        <!-- FEATURED SPA SECTION -->
        <section v-if="featuredConfectionery.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 gap-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-orange-500 block mb-1">
                        TeamAkeem Beauty &amp; Wellness Spa
                    </span>
                    <h2 class="font-bold text-2xl sm:text-3xl tracking-tight text-zinc-900 dark:text-white uppercase font-sans">
                        Spa Services &amp; Glow Cosmetics
                    </h2>
                </div>
                <Link 
                    :href="route('spa.index')" 
                    class="inline-flex items-center gap-1 text-sm font-semibold text-orange-600 dark:text-orange-400 hover:underline"
                >
                    <span>View all spa &amp; cosmetics</span>
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard 
                    v-for="product in featuredConfectionery" 
                    :key="product.id" 
                    :product="product" 
                />
            </div>
        </section>

        <!-- VALUE PROPOSITIONS -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 border-t border-b border-black/[0.06] dark:border-white/[0.08] my-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-orange-500/10 text-orange-600 shrink-0">
                        <Sparkles class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Everything Ladies</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">From comfortable seamless underwear &amp; nightwear to beauty wellness therapies.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-emerald-500/10 text-emerald-600 shrink-0">
                        <MessageCircle class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">WhatsApp Booking</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Direct instant ordering and home service appointment booking via WhatsApp.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-orange-500/10 text-orange-600 shrink-0">
                        <Truck class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Home Service Available</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">We do home service for massages, pedicures, and Gyaran Jiki spa treatments!</p>
                    </div>
                </div>

                <div class="flex items-start gap-3.5">
                    <div class="p-2.5 rounded-full bg-orange-500/10 text-orange-600 shrink-0">
                        <ShieldCheck class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Healing Starts in Mind</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Holistic relaxation, delicate skincare, and dedicated feminine restorative care.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT BRIEF SECTION -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="rounded-3xl bg-[#F5F5F7] dark:bg-[#161617] border border-black/[0.04] dark:border-white/[0.08] p-8 sm:p-12 lg:p-16 flex flex-col lg:flex-row items-center gap-10">
                <div class="lg:w-1/2 space-y-4">
                    <span class="text-xs font-semibold uppercase tracking-widest text-orange-500">
                        Our Sanctuary
                    </span>
                    <h2 class="font-bold text-3xl sm:text-4xl text-zinc-900 dark:text-white tracking-tight leading-tight uppercase font-sans">
                        Two Passions, One Heartfelt Sanctuary.
                    </h2>
                    <p class="text-sm sm:text-base text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        {{ aboutStory || 'TeamAkeem Ladies World is a premier sanctuary dedicated exclusively to feminine grace, comfort, and holistic well-being. We bring together two essential passions under one roof: a carefully curated collection of luxury ladies wear, intimates, loungewear and modest fashion through TeamAkeem Ladies Wears, and rejuvenating self-care, holistic therapies and premium cosmetics at TeamAkeem Ladies Beauty & Wellness Spa. With professional home service and dedicated salon spa care, we believe healing starts in your mind.' }}
                    </p>
                    <div class="pt-2">
                        <Link 
                            :href="route('about')" 
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-orange-600 dark:text-orange-400 hover:underline"
                        >
                            <span>Read our story</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>

                <div class="lg:w-1/2 grid grid-cols-2 gap-4 w-full">
                    <div class="rounded-2xl bg-white dark:bg-[#1D1D1F] p-6 flex flex-col justify-center items-center text-center border border-black/[0.04] dark:border-white/[0.06] shadow-2xs">
                        <Shirt class="w-8 h-8 text-orange-500 mb-3" />
                        <h4 class="font-bold text-sm text-zinc-900 dark:text-white uppercase">TeamAkeem Wears</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Seamless underwear, silk robes, nightwear, and loungewear.</p>
                    </div>
                    <div class="rounded-2xl bg-white dark:bg-[#1D1D1F] p-6 flex flex-col justify-center items-center text-center border border-black/[0.04] dark:border-white/[0.06] shadow-2xs">
                        <Sparkles class="w-8 h-8 text-orange-500 mb-3" />
                        <h4 class="font-bold text-sm text-zinc-900 dark:text-white uppercase">TeamAkeem Spa</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Massages, O-shot, pedicures, Gyaran Jiki &amp; cosmetics.</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
