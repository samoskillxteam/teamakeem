<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Save, Globe, MessageCircle, Phone, Instagram, Truck,
    Image as ImageIcon, AlertCircle, CheckCircle, User, Lock,
    Upload, Sparkles, ShoppingBag, BookOpen, Eye, EyeOff
} from 'lucide-vue-next';

interface SettingItem {
    id: number;
    key: string;
    value: string | null;
    description: string;
    type: string;
    group: string;
}

interface Props {
    settings: SettingItem[];
    adminEmail: string;
    adminName: string;
}
const props = defineProps<Props>();

// Build keyed map from flat settings array
const settingMap = Object.fromEntries(props.settings.map(s => [s.key, s.value ?? '']));

const form = useForm<{
    settings: Record<string, string>;
    admin_email: string;
    admin_name: string;
    new_password: string;
    new_password_confirmation: string;
    logo: File | null;
    logo_dark: File | null;
    hero_desktop_light: File | null;
    hero_desktop_dark: File | null;
    hero_mobile_light: File | null;
    hero_mobile_dark: File | null;
    spa_flyer: File | null;
}>({
    settings: { ...settingMap } as Record<string, string>,
    admin_email: props.adminEmail ?? '',
    admin_name: props.adminName ?? '',
    new_password: '',
    new_password_confirmation: '',
    logo: null,
    logo_dark: null,
    hero_desktop_light: null,
    hero_desktop_dark: null,
    hero_mobile_light: null,
    hero_mobile_dark: null,
    spa_flyer: null,
});

// Image preview refs
const previews = ref<Record<string, string | null>>({
    logo: null,
    logo_dark: null,
    hero_desktop_light: null,
    hero_desktop_dark: null,
    hero_mobile_light: null,
    hero_mobile_dark: null,
    spa_flyer: null,
});

function handleImage(field: keyof typeof form.data, e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    (form as any)[field] = file;
    const reader = new FileReader();
    reader.onload = (ev) => { previews.value[field as string] = ev.target?.result as string; };
    reader.readAsDataURL(file);
}

const showPassword = ref(false);
const showConfirm = ref(false);

function submit() {
    form.post('/admin/settings', {
        preserveScroll: true,
        forceFormData: true,
    });
}

function getMeta(key: string): SettingItem | undefined {
    return props.settings.find(s => s.key === key);
}
function isTextarea(key: string) {
    return ['hero_subheadline', 'address', 'delivery_info', 'about_story'].includes(key)
        || getMeta(key)?.type === 'textarea';
}
function inputType(key: string) {
    const meta = getMeta(key);
    if (meta?.type === 'url') return 'url';
    if (meta?.type === 'email' || key === 'contact_email') return 'email';
    if (key.includes('phone') || key === 'whatsapp_number') return 'tel';
    return 'text';
}
function label(key: string) {
    const meta = getMeta(key);
    if (meta?.description) return meta.description;
    return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

const groupedKeys = [
    'business_name','clothing_brand_name','confectionery_brand_name','tagline',
    'currency_code','currency_symbol','hero_headline','hero_subheadline',
    'contact_phone','contact_email','whatsapp_number','address',
    'instagram_url','facebook_url','delivery_info','about_story',
    'logo_path','logo_dark_path',
];
const ungroupedSettings = computed(() => props.settings.filter(s => !groupedKeys.includes(s.key)
    && !s.key.includes('_path')));

// Current stored images
function currentImageUrl(settingKey: string, fallback: string): string {
    const val = settingMap[settingKey];
    if (!val) return fallback;
    if (val.startsWith('http')) return val;
    return '/' + val;
}
</script>

<template>
    <AdminLayout title="Settings">
        <div class="max-w-3xl mx-auto space-y-6 pb-12">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Site Settings</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Manage every aspect of your site — branding, images, contacts and admin account — without touching the server.
                </p>
            </div>

            <!-- Flash success -->
            <div v-if="($page.props as any).flash?.success"
                class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl">
                <CheckCircle class="w-5 h-5 text-emerald-500 flex-shrink-0" />
                <p class="text-sm text-emerald-700 dark:text-emerald-400">{{ ($page.props as any).flash.success }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">

                <!-- ── BRANDING ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <Globe class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Branding & Identity</h2>
                            <p class="text-xs text-zinc-500">Business name, taglines and currency</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Business Name</label>
                            <input v-model="form.settings['business_name']" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Ladies Wears Brand Name</label>
                            <input v-model="form.settings['clothing_brand_name']" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Spa Brand Name</label>
                            <input v-model="form.settings['confectionery_brand_name']" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tagline</label>
                            <input v-model="form.settings['tagline']" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Currency Code</label>
                            <input v-model="form.settings['currency_code']" type="text" placeholder="NGN"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Currency Symbol</label>
                            <input v-model="form.settings['currency_symbol']" type="text" placeholder="₦"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                    </div>
                </section>

                <!-- ── LOGOS ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <ImageIcon class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Logo Upload</h2>
                            <p class="text-xs text-zinc-500">PNG/JPG recommended. Light logo shown on dark bg, dark logo on light bg.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Light logo -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Logo (Light / Default)</label>
                            <div class="flex flex-col items-center gap-3 p-4 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800">
                                <img :src="previews.logo ?? currentImageUrl('logo_path', '/images/logo.png')"
                                    alt="Logo preview" class="h-20 object-contain" />
                                <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-medium">
                                    <Upload class="w-3 h-3" /> Choose File
                                    <input type="file" accept="image/*" class="hidden" @change="handleImage('logo', $event)" />
                                </label>
                            </div>
                        </div>
                        <!-- Dark logo -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Logo (Dark Mode)</label>
                            <div class="flex flex-col items-center gap-3 p-4 rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 bg-zinc-900">
                                <img :src="previews.logo_dark ?? currentImageUrl('logo_dark_path', '/images/logo_dark.png')"
                                    alt="Dark logo preview" class="h-20 object-contain" />
                                <label class="cursor-pointer flex items-center gap-2 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-medium">
                                    <Upload class="w-3 h-3" /> Choose File
                                    <input type="file" accept="image/*" class="hidden" @change="handleImage('logo_dark', $event)" />
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ── HERO IMAGES ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <Sparkles class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Homepage Hero Banners</h2>
                            <p class="text-xs text-zinc-500">Desktop (≥768 px wide) and mobile images for both light and dark modes.</p>
                        </div>
                    </div>

                    <!-- Hero text -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Hero Headline</label>
                            <input v-model="form.settings['hero_headline']" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Hero Sub-headline</label>
                            <textarea v-model="form.settings['hero_subheadline']" rows="3"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Hero image uploads -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="({ field, label: lbl }) in [
                            { field: 'hero_desktop_light', label: 'Desktop — Light Mode' },
                            { field: 'hero_desktop_dark',  label: 'Desktop — Dark Mode' },
                            { field: 'hero_mobile_light',  label: 'Mobile — Light Mode' },
                            { field: 'hero_mobile_dark',   label: 'Mobile — Dark Mode' },
                        ]" :key="field">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">{{ lbl }}</label>
                            <div class="relative rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 overflow-hidden bg-zinc-50 dark:bg-zinc-800 aspect-[16/5] flex items-center justify-center">
                                <img v-if="previews[field]" :src="previews[field]!" class="absolute inset-0 w-full h-full object-cover opacity-80" />
                                <label class="relative z-10 cursor-pointer flex flex-col items-center gap-1 text-zinc-500 hover:text-orange-600 transition-colors">
                                    <Upload class="w-6 h-6" />
                                    <span class="text-xs font-medium">Upload</span>
                                    <input type="file" accept="image/*" class="hidden" @change="handleImage(field as any, $event)" />
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Spa flyer -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Spa Promotional Flyer</label>
                        <div class="relative rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 overflow-hidden bg-zinc-50 dark:bg-zinc-800 h-32 flex items-center justify-center">
                            <img v-if="previews.spa_flyer" :src="previews.spa_flyer!" class="absolute inset-0 w-full h-full object-cover opacity-80" />
                            <label class="relative z-10 cursor-pointer flex flex-col items-center gap-1 text-zinc-500 hover:text-orange-600">
                                <Upload class="w-6 h-6" />
                                <span class="text-xs font-medium">Upload Flyer</span>
                                <input type="file" accept="image/*" class="hidden" @change="handleImage('spa_flyer', $event)" />
                            </label>
                        </div>
                    </div>
                </section>

                <!-- ── CONTACT ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <Phone class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Contact Information</h2>
                            <p class="text-xs text-zinc-500">Phone, email, WhatsApp and location</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Phone</label>
                            <input v-model="form.settings['contact_phone']" type="tel"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">WhatsApp Number</label>
                            <input v-model="form.settings['whatsapp_number']" type="tel" placeholder="234XXXXXXXXXX"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                            <p class="text-xs text-zinc-400 mt-1">Include country code, no + sign. e.g. 2348076187311</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Contact Email</label>
                            <input v-model="form.settings['contact_email']" type="email"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Address / Location</label>
                            <input v-model="form.settings['address']" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                    </div>
                </section>

                <!-- ── SOCIAL ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <Instagram class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Social Media</h2>
                            <p class="text-xs text-zinc-500">Links shown in the footer and contact page</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Instagram URL</label>
                            <input v-model="form.settings['instagram_url']" type="url"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Facebook URL</label>
                            <input v-model="form.settings['facebook_url']" type="url"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                    </div>
                </section>

                <!-- ── DELIVERY ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <Truck class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Delivery & Fulfilment</h2>
                            <p class="text-xs text-zinc-500">Shown on cart, checkout and product pages</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Delivery Info</label>
                        <textarea v-model="form.settings['delivery_info']" rows="3"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-none"></textarea>
                    </div>
                </section>

                <!-- ── ABOUT ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <BookOpen class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">About Page</h2>
                            <p class="text-xs text-zinc-500">Our story text shown on the About page</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Our Story</label>
                        <textarea v-model="form.settings['about_story']" rows="6"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm resize-y"></textarea>
                    </div>
                </section>

                <!-- ── ADMIN ACCOUNT ── -->
                <section class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                            <User class="w-4 h-4 text-orange-600" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-zinc-100">Admin Account</h2>
                            <p class="text-xs text-zinc-500">Change your login email, name or password</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Admin Name</label>
                            <input v-model="form.admin_name" type="text"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Login Email</label>
                            <input v-model="form.admin_email" type="email"
                                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                                New Password <span class="text-zinc-400 font-normal">(leave blank to keep current)</span>
                            </label>
                            <div class="relative">
                                <input v-model="form.new_password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 pr-9 text-sm" />
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                                    <EyeOff v-if="showPassword" class="w-4 h-4" />
                                    <Eye v-else class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Confirm New Password</label>
                            <div class="relative">
                                <input v-model="form.new_password_confirmation" :type="showConfirm ? 'text' : 'password'" autocomplete="new-password"
                                    class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 pr-9 text-sm" />
                                <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600">
                                    <EyeOff v-if="showConfirm" class="w-4 h-4" />
                                    <Eye v-else class="w-4 h-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.new_password" class="text-red-500 text-xs mt-1">{{ form.errors.new_password }}</p>
                        </div>
                    </div>
                </section>

                <!-- Error summary -->
                <div v-if="Object.keys(form.errors).length > 0"
                    class="flex gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl">
                    <AlertCircle class="w-5 h-5 text-red-500 flex-shrink-0" />
                    <div class="text-sm text-red-700 dark:text-red-400">
                        <p class="font-medium mb-1">Please fix the errors:</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li v-for="(err, k) in form.errors" :key="k">{{ err }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Save -->
                <div class="flex justify-end">
                    <button type="submit" :disabled="form.processing"
                        class="flex items-center gap-2 px-7 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold text-sm transition-colors disabled:opacity-60">
                        <Save class="w-4 h-4" />
                        {{ form.processing ? 'Saving…' : 'Save All Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
