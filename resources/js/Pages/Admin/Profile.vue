<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    KeyRound, 
    User as UserIcon, 
    ShieldCheck, 
    CheckCircle, 
    AlertCircle, 
    Eye, 
    EyeOff, 
    Save, 
    Mail 
} from 'lucide-vue-next';

interface Props {
    adminName: string;
    adminEmail: string;
}

const props = defineProps<Props>();

// Details Form
const detailsForm = useForm({
    name: props.adminName ?? '',
    email: props.adminEmail ?? '',
});

function submitDetails() {
    detailsForm.post(route('admin.profile.details'), {
        preserveScroll: true,
    });
}

// Password Form
const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

function submitPassword() {
    passwordForm.post(route('admin.profile.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
    });
}
</script>

<template>
    <AdminLayout title="Profile & Security">
        <Head title="Profile & Security — Admin Portal" />

        <div class="max-w-3xl mx-auto space-y-6 pb-12">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 font-sans">Profile & Security</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Manage your administrator credentials and update your account password securely.
                </p>
            </div>

            <!-- Flash Success Message -->
            <div v-if="($page.props as any).flash?.success" 
                class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl">
                <CheckCircle class="w-5 h-5 text-emerald-500 shrink-0" />
                <p class="text-sm text-emerald-700 dark:text-emerald-400 font-medium">
                    {{ ($page.props as any).flash.success }}
                </p>
            </div>

            <!-- Flash Error Message -->
            <div v-if="($page.props as any).flash?.error" 
                class="flex items-center gap-3 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700 rounded-xl">
                <AlertCircle class="w-5 h-5 text-rose-500 shrink-0" />
                <p class="text-sm text-rose-700 dark:text-rose-400 font-medium">
                    {{ ($page.props as any).flash.error }}
                </p>
            </div>

            <!-- Password Change Section -->
            <section class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 md:p-8 space-y-6 shadow-xs">
                <div class="flex items-center gap-3 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <KeyRound class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Update Password</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitPassword" class="space-y-4 max-w-xl">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Current Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                v-model="passwordForm.current_password" 
                                :type="showCurrentPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="Enter current password"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 px-3.5 py-2.5 pr-10 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-orange-500"
                            />
                            <button 
                                type="button" 
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors"
                            >
                                <EyeOff v-if="showCurrentPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.current_password" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            New Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                v-model="passwordForm.new_password" 
                                :type="showNewPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                placeholder="Minimum 8 characters (mixed case + number)"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 px-3.5 py-2.5 pr-10 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-orange-500"
                            />
                            <button 
                                type="button" 
                                @click="showNewPassword = !showNewPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors"
                            >
                                <EyeOff v-if="showNewPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.new_password" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ passwordForm.errors.new_password }}
                        </p>
                        <p class="text-[11px] text-zinc-400 mt-1">
                            Password must be at least 8 characters and include uppercase, lowercase, and numeric digits.
                        </p>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Confirm New Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                v-model="passwordForm.new_password_confirmation" 
                                :type="showConfirmPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                placeholder="Re-type new password"
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 px-3.5 py-2.5 pr-10 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-orange-500"
                            />
                            <button 
                                type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors"
                            >
                                <EyeOff v-if="showConfirmPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.new_password_confirmation" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ passwordForm.errors.new_password_confirmation }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="passwordForm.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors disabled:opacity-50 shadow-sm"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ passwordForm.processing ? 'Updating...' : 'Update Password' }}</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Admin Profile Details Section -->
            <section class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 md:p-8 space-y-6 shadow-xs">
                <div class="flex items-center gap-3 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <UserIcon class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Administrator Information</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Update your name and primary login email address.
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitDetails" class="space-y-4 max-w-xl">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Admin Name
                        </label>
                        <input 
                            v-model="detailsForm.name" 
                            type="text"
                            required
                            class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 px-3.5 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-orange-500"
                        />
                        <p v-if="detailsForm.errors.name" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ detailsForm.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Login Email Address
                        </label>
                        <div class="relative">
                            <input 
                                v-model="detailsForm.email" 
                                type="email"
                                required
                                class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800/80 px-3.5 py-2.5 pl-9 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-hidden focus:ring-2 focus:ring-orange-500"
                            />
                            <Mail class="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                        </div>
                        <p v-if="detailsForm.errors.email" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ detailsForm.errors.email }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="detailsForm.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-200 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors disabled:opacity-50 shadow-sm"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ detailsForm.processing ? 'Saving...' : 'Save Changes' }}</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>
