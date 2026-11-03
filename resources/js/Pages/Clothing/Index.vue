<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { Category, Product } from '@/types';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { Search, Shirt, SlidersHorizontal, X, Sparkles, MessageCircle } from 'lucide-vue-next';

interface Props {
    category: Category;
    products: {
        data: Product[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        total: number;
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
        sort?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const sort = ref(props.filters.sort || 'featured');

const applyFilters = () => {
    router.get(
        route('clothing.index'),
        {
            search: search.value || undefined,
            sort: sort.value,
        },
        { preserveState: true, replace: true }
    );
};

const clearSearch = () => {
    search.value = '';
    applyFilters();
};

// ── Dynamic Purply-Pinkish Palettes (Rotates on every refresh) ────────
interface PurplyPinkTheme {
    id: string;
    name: string;
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

const themes: PurplyPinkTheme[] = [
    {
        id: 'teamakeem_orange',
        name: 'TeamAkeem Sunset Orange & Onyx',
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
        name: 'Amber Gold & Warm Sand',
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
        name: 'Obsidian Jet & Radiant Tangerine',
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

const currentTheme = ref<PurplyPinkTheme>(themes[0]);
const isDark = ref(false);

const updateDarkMode = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
    updateDarkMode();
    try {
        const lastIdx = Number(sessionStorage.getItem('clothing_theme_idx') ?? -1);
        const nextIdx = (lastIdx + 1) % themes.length;
        sessionStorage.setItem('clothing_theme_idx', String(nextIdx));
        currentTheme.value = themes[nextIdx];
    } catch {
        currentTheme.value = themes[Math.floor(Math.random() * themes.length)];
    }

    const observer = new MutationObserver(() => updateDarkMode());
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>

<template>
    <AppLayout 
        title="TeamAkeem Ladies Wears — Underwear, Loungewear & Nightwear" 
        description="Explore luxury seamless underwear, silk nightwear, robes, and everyday modesty collections from TeamAkeem Ladies Wears."
    >
        <!-- Category Hero Banner (Dynamic Brand Orange & Amber Gradient on Refresh) -->
        <div 
            class="relative overflow-hidden border-b transition-colors duration-700 ease-out"
            :style="{
                background: isDark ? currentTheme.bgGradientDark : currentTheme.bgGradientLight,
                borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight,
            }"
        >
            <!-- Ambient Brand Glow (Reactive) -->
            <div 
                class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                :style="{ backgroundColor: currentTheme.glowColor1 }"
            ></div>
            <div 
                class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-all duration-700"
                :style="{ backgroundColor: currentTheme.glowColor2 }"
            ></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Brand Copy & Meta -->
                    <div class="lg:col-span-7 space-y-4 sm:space-y-5">
                        <div 
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide border transition-all duration-500"
                            :style="{
                                backgroundColor: isDark ? currentTheme.tagBgDark : currentTheme.tagBgLight,
                                color: isDark ? currentTheme.tagTextDark : currentTheme.tagTextLight,
                                borderColor: currentTheme.tagBorder,
                            }"
                        >
                            <Shirt class="w-3.5 h-3.5 text-orange-500" />
                            <span>Ladies Wears, Underwear &amp; Intimates</span>
                        </div>

                        <div>
                            <h1 class="font-bold text-3xl sm:text-5xl lg:text-6xl text-zinc-900 dark:text-white tracking-tight leading-tight uppercase font-sans">
                                {{ category.brand_name || 'TeamAkeem Ladies Wears' }}
                            </h1>
                            <p class="text-base sm:text-xl text-zinc-700 dark:text-zinc-300 font-normal mt-2 tracking-tight">
                                Everything ladies: seamless underwear, silk nightwear, robes &amp; everyday confidence.
                            </p>
                        </div>

                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300/80 leading-relaxed max-w-xl">
                            {{ category.description || 'Explore everything ladies: premium supportive wireless bras, seamless briefs, silk kimono robes, lace lingerie sets, and flattering everyday loungewear.' }}
                        </p>

                        <!-- Key Highlights / Badges -->
                        <div class="pt-2 flex flex-wrap items-center gap-2.5 sm:gap-3 text-xs text-zinc-700 dark:text-zinc-300">
                            <div 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-black/40 border shadow-2xs font-medium backdrop-blur-md"
                                :style="{ borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight }"
                            >
                                <Sparkles class="w-3.5 h-3.5 text-orange-500" />
                                <span>Seamless Intimates</span>
                            </div>
                            <div 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-black/40 border shadow-2xs font-medium backdrop-blur-md"
                                :style="{ borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight }"
                            >
                                <MessageCircle class="w-3.5 h-3.5 text-emerald-500" />
                                <span>Direct WhatsApp Ordering</span>
                            </div>
                            <div 
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 dark:bg-black/40 border shadow-2xs font-medium backdrop-blur-md"
                                :style="{ borderColor: isDark ? currentTheme.borderColorDark : currentTheme.borderColorLight }"
                            >
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ products.total }}</span>
                                <span>Styles Available</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Featured Visual Showcase Card -->
                    <div class="lg:col-span-5">
                        <div 
                            class="relative group rounded-3xl overflow-hidden bg-white/90 shadow-xl sm:shadow-2xl max-w-md mx-auto lg:max-w-none border transition-all duration-700"
                            :style="{
                                backgroundColor: isDark ? currentTheme.cardBgDark : 'rgba(255, 255, 255, 0.9)',
                                borderColor: isDark ? currentTheme.cardBorderDark : currentTheme.borderColorLight,
                            }"
                        >
                            <!-- Image Container -->
                            <div class="aspect-square sm:aspect-[4/3] lg:aspect-square overflow-hidden bg-zinc-950">
                                <img 
                                    src="/storage/products/ladies/ladies_1.jpg" 
                                    alt="TeamAkeem Ladies Wears &amp; Underwear" 
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>

                            <!-- Floating Apple-style pill badge on image -->
                            <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 z-10 flex items-center justify-between pointer-events-none">
                                <div class="px-3 py-1.5 rounded-full bg-black/60 dark:bg-black/75 backdrop-blur-md text-white text-[11px] font-medium border border-white/10 shadow-sm flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                    <span>Exclusive Underwear &amp; Robes</span>
                                </div>
                                <span 
                                    class="px-2.5 py-1 rounded-full text-white text-[10px] font-semibold backdrop-blur-md shadow-sm border border-white/20 transition-colors duration-500"
                                    :style="{ backgroundColor: currentTheme.brandPillBg }"
                                >
                                    TeamAkeem Ladies Wears
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-black/[0.06] dark:border-white/[0.08]">
                <!-- Search Box (Apple Style Pill Input) -->
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        v-model="search"
                        @keydown.enter="applyFilters"
                        type="text" 
                        placeholder="Search collection..." 
                        class="w-full pl-10 pr-9 py-2 text-xs sm:text-sm rounded-full border border-black/10 dark:border-white/15 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white transition-all"
                    />
                    <button 
                        v-if="search" 
                        type="button" 
                        @click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <!-- Sort & Meta -->
                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                        {{ products.total }} items
                    </span>

                    <div class="flex items-center gap-2">
                        <SlidersHorizontal class="w-3.5 h-3.5 text-zinc-400" />
                        <select 
                            v-model="sort" 
                            @change="applyFilters"
                            class="text-xs sm:text-sm py-1.5 px-3 rounded-full border border-black/10 dark:border-white/15 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-black dark:focus:ring-white cursor-pointer"
                        >
                            <option value="featured">Featured First</option>
                            <option value="newest">Newest Arrivals</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div v-if="products.data.length > 0" class="grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4 pt-6 sm:pt-8">
                <ProductCard 
                    v-for="product in products.data" 
                    :key="product.id" 
                    :product="product" 
                />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-[#F5F5F7] dark:bg-[#161617] rounded-3xl border border-black/[0.04] dark:border-white/[0.08] mt-8 p-8">
                <Shirt class="w-10 h-10 mx-auto text-zinc-400 mb-3" />
                <h3 class="font-medium text-base text-zinc-900 dark:text-white">No apparel pieces found</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto mt-1">
                    Try adjusting your search criteria or explore our newest featured arrivals.
                </p>
                <button 
                    @click="clearSearch"
                    type="button" 
                    class="mt-4 px-4 py-2 text-xs font-medium rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 transition-all active:scale-95"
                >
                    Clear Filters
                </button>
            </div>

            <!-- Pagination -->
            <div v-if="products.links.length > 3" class="mt-12 flex justify-center gap-1.5">
                <component
                    :is="link.url ? 'Link' : 'span'"
                    v-for="(link, i) in products.links"
                    :key="i"
                    :href="link.url || undefined"
                    class="px-3.5 py-1.5 text-xs font-medium rounded-full border transition-colors"
                    :class="[
                        link.active 
                            ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-white dark:text-zinc-950 dark:border-white' 
                            : 'bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-black/10 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-zinc-800',
                        !link.url ? 'opacity-40 cursor-not-allowed' : ''
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
