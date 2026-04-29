<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/utils/api';

const router = useRouter();

// بيانات المستخدم
interface User {
    name: string;
    email: string;
    avatar?: string;
}

const user = ref<User>({
    name: '',
    email: '',
    avatar: ''
});

// للملف والمعاينة
const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);

// Ref لفتح input عند النقر على الدائرة
const fileInput = ref<HTMLInputElement | null>(null);

// خطأ في التحقق
const error = ref('');

// جلب بيانات المستخدم
const fetchUser = async () => {
    try {
        const { data } = await api.get('/user');
        user.value.name = data.user.name;
        user.value.email = data.user.email;
        user.value.avatar = data.user.avatar; // إذا موجودة
        if (user.value.avatar) {
            avatarPreview.value = `/storage/${user.value.avatar}`;
        }
    } catch (err) {
        console.error(err);
    }
};

// فتح نافذة اختيار الملف
const triggerFileInput = () => {
    fileInput.value?.click();
};

// التعامل مع الملف الجديد
const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;

    if (target.files[0].size > 2 * 1024 * 1024) { // 2MB
        alert("حجم الصورة كبير جداً");
        return;
    }

    if (target.files && target.files[0]) {
        avatarFile.value = target.files[0];
        avatarPreview.value = URL.createObjectURL(avatarFile.value);
    }
};

// تحديث بيانات المستخدم
const handleUpdateUser = async () => {
    error.value = '';

    if (!user.value.name && !user.value.email && !avatarFile.value) {
        error.value = 'يرجى إدخال اسم أو بريد إلكتروني أو اختيار صورة.';
        return;
    }

    try {
        const formData = new FormData();
        if (user.value.name) formData.append('name', user.value.name);
        if (user.value.email) formData.append('email', user.value.email);
        if (avatarFile.value) formData.append('avatar', avatarFile.value);

        const res = await api.put('/user', formData);

        // تحديث البيانات المحلية بعد نجاح العملية
        user.value.name = res.data.user.name;
        user.value.email = res.data.user.email;
        user.value.avatar = res.data.user.avatar;

        // تحديث المعاينة
        if (res.data.user.avatar) {
            avatarPreview.value = `/storage/${res.data.user.avatar}`;
        } else {
            avatarPreview.value = null;
        }


        // التوجيه بعد التحديث
        router.push({ name: 'settings' });

    } catch (err: any) {
        console.error(err.response?.data);
        error.value = err.response?.data?.message || 'حدث خطأ أثناء التحديث';
    }
};

// عند تحميل المكون
onMounted(fetchUser);
</script>

<template>
    <NavBar title="Update User" description="Update your profile data" />
    <div class="p-8 flex flex-col xl:flex-row gap-8">
        <div class="flex-1 max-w-3xl">
            <div class="bg-[#1e293b] rounded-xl border-2 border-[#1F4371] shadow-xl shadow-[#1E2D45] overflow-hidden">
                <div class="p-6 border-b border-[#283039] flex items-center justify-between bg-[#1E2D45]">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500 text-2xl">group_add</span>
                        <h2 class="text-xl font-bold">{{ $t('updateUser.title') }}</h2>
                    </div>
                </div>
                <div class="p-8 space-y-8 bg-[#1E293B]">
                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        <div class="flex flex-col items-start gap-4">

                            <!-- Icon Img -->
                            <div class="relative group cursor-pointer w-32 h-32" @click="triggerFileInput">
                                <div
                                    class="w-full h-full rounded-full border-2 border-dashed border-slate-600 flex flex-col items-center justify-center bg-slate-800/50 group-hover:border-blue-500 transition-all">
                                    <span
                                        class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-blue-500">add_a_photo</span>
                                    <span class="text-[10px] mt-1 text-slate-400 font-medium">Upload JPG/PNG</span>
                                </div>
                                <div
                                    class="absolute inset-0 bg-blue-500/10 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div
                                        class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </div>
                                </div>

                                <!-- Preview الصورة -->
                                <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar Preview"
                                    class="absolute top-0 left-0 w-full h-full rounded-full object-cover" />
                            </div>

                            <!-- Hidden Input -->
                            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
                        </div>

                        <div class="flex-1 space-y-6 w-full">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-300">{{ $t('updateUser.userName')
                                    }}</label>
                                <input
                                    class="w-full bg-[#283039] rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#137fec] text-base outline-none transition-all"
                                    :class="error && 'border border-red-500'" placeholder="Enter your name" type="text"
                                    v-model="user.name" @input="error = ''" />
                                <p class="text-red-500 text-sm">{{ error }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-300">{{ $t('updateUser.userEmail')
                                }}</label>
                                <input
                                    class="w-full bg-[#283039] rounded-xl py-3 px-4 focus:ring-2 focus:ring-[#137fec] text-base outline-none transition-all"
                                    :class="error && 'border border-red-500'" placeholder="Enter your email"
                                    type="email" v-model="user.email" @input="error = ''" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button
                            class="px-8 py-3 bg-[#137fec] hover:bg-[#137fec]/70 rounded-xl font-bold text-sm text-white shadow-lg transition-all flex items-center gap-2"
                            @click="handleUpdateUser">
                            {{ $t('updateUser.updateBtn') }}
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
