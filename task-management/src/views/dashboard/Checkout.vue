<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { loadStripe } from '@stripe/stripe-js';
import NavBar from '@/components/NavBar.vue';
import api from '@/utils/api'; // ملف الـ API بتاعك
import { useUserStore } from '@/stores/user';

const router = useRouter();
const userStore = useUserStore()

// ⭐ حط الـ Public Key بتاعك هنا (من Laravel .env)
const STRIPE_PUBLIC_KEY = 'pk_test_51SwNtmKlvpGADNZUtSxEAQrbPHuaFgOHp2EJNdQyTSovmD8hVLGKasSSA5ZlxKGp1ZXk97sh1APN0xyfEMpdsGiy00qhH91zz5';

// States
const loading = ref(false);
const error = ref('');
const clientSecret = ref('');
const cardholderName = ref('');


// Stripe Elements
let stripe: any = null;
let cardElement: any = null;



onMounted(async () => {
    stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY || 'pk_test_51SwNtmKlvpGADNZUtSxEAQrbPHuaFgOHp2EJNdQyTSovmD8hVLGKasSSA5ZlxKGp1ZXk97sh1APN0xyfEMpdsGiy00qhH91zz5')

    // Check if user is in pro plan
    // if (userStore.plan === 2) && router.back};


    if (!stripe) {
        error.value = 'Failed to load Stripe'
        return
    }

    const elements = stripe.elements()

    cardElement = elements.create('card', {
        style: {
            base: {
                color: '#ffffff',
                fontFamily: 'Inter, sans-serif',
                fontSize: '16px',
            },
        },
        hidePostalCode: true,
    })

    cardElement.mount('#card-element')
})


// دالة الدفع
async function handlePayment() {
    if (!stripe || !cardElement || !cardholderName.value) {
        error.value = 'Please fill in all required fields';
        return;
    }

    loading.value = true;
    error.value = '';

    try {
        // 1️⃣ نطلب PaymentIntent دلوقتي بس
        const response = await api.post('/create-payment-intent');
        clientSecret.value = response.data.client_secret;

        // 2️⃣ نأكد الدفع
        const { error: stripeError, paymentIntent } =
            await stripe.confirmCardPayment(clientSecret.value, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardholderName.value,
                    },
                },
            });

        if (stripeError) {
            error.value = stripeError.message;
            return;
        }

        if (paymentIntent?.status === 'succeeded') {
            const res = await api.post('/confirm-payment', {
                payment_intent_id: paymentIntent.id,
            });


            // ✔️ بس رسالة
            userStore.plan = res.data.plan_id;
        }

    } catch (err: any) {
        error.value = 'Payment failed. Please try again.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <NavBar title="Checkout" description="Finish setting up your account and start managing your team." />
    <main class="bg-[#111418] w-full h-full flex flex-col items-center justify-center">

        <!-- Main Checkout Card -->
        <div class="w-full max-w-[1000px] glass-card rounded-xl overflow-hidden shadow-2xl flex flex-col md:flex-row bg-[#171C21] border-[#23282D]"
            v-if="userStore.userPlan === 'free'">

            <!-- Left: Payment Form -->
            <div class="flex-1 p-8 md:p-12 border-b md:border-b-0 md:border-r border-[#23282D]">
                <div class="flex items-center gap-2 mb-8">
                    <span class="material-symbols-outlined text-[#137fec]">shield_lock</span>
                    <h3 class="text-white text-xl font-bold">Payment Method</h3>
                </div>

                <!-- ⭐ Error Message -->
                <div v-if="error"
                    class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-sm flex items-start gap-2">
                    <span class="material-symbols-outlined text-lg">error</span>
                    <span>{{ error }}</span>
                </div>

                <form @submit.prevent="handlePayment" class="space-y-6">

                    <!-- Cardholder Name -->
                    <div class="flex flex-col gap-2">
                        <label class="text-gray-300 text-sm font-medium">Cardholder Name *</label>
                        <input v-model="cardholderName" required
                            class="w-full h-14 bg-[#1c2127] border border-[#23282D] rounded-lg px-4 text-white focus:ring-2 focus:ring-[#137fec] focus:border-[#137fec] outline-none transition-all placeholder:text-[#9dabb9]"
                            placeholder="John Doe" type="text" />
                    </div>

                    <!-- ⭐ Card Element (Stripe) -->
                    <div class="flex flex-col gap-2">
                        <label class="text-gray-300 text-sm font-medium">Card Details *</label>
                        <div id="card-element"
                            class="w-full min-h-[52px] bg-[#1c2127] border border-[#23282D] rounded-lg px-4 py-3"></div>
                        <p class="text-gray-400 text-xs mt-1">
                            Includes card number, expiry date, and CVC
                        </p>
                    </div>

                </form>

                <div
                    class="mt-10 flex flex-wrap items-center gap-6 opacity-40 grayscale group hover:grayscale-0 transition-all">
                    <img alt="Visa" class="h-4"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBXMRbCyNp0MZjkPV_t9CLc2itYL13WMyiD7__CrRoiTuPbKGe3S1Cs7U4eHfDUfuipwDviopdVAj-cNXgz6X325419_If4kTZn9qCZEdH8i0mIE6CZoG09adpWJCuqfCZA1jBnipo5_FAbEd5_pGqwi4npAEuJ1tOUYnBvAXILvu_X12zb4uW1A0N96DwG_EFOCWocmm6czfXjvy_MMpxbw9w7hvpjl6ILx5wZrxD5FDS9h9j3Loh9W-zDIAQ5LblhoaySBMtywXc" />
                    <img alt="Mastercard" class="h-6"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAav5LB-uC4ZIS00Y-SwVKTp3S1RM7MRiOFt15W6yR2put--biK02s9yfUBQDwWKi5lbJ1p_hFqU1_uVqTdpktteBV36cEGM-5tIJ5LJvibk0aGzcO8R5we_IuXK9ouWQMs9ErQtONPRT7GEas4QlXYOF2HTyidcL2K8iSS34Eao4XVlXpdQCs1_MiE2UU8-Lh2JfbWbj96wIBJncdeCcDLq7bgYE8SPhurxKvztIh-ueVzpgN9IKFOvqYnkR0L9zGbZOWMvmaq0-c" />
                    <img alt="Paypal" class="h-5"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAfViFgB88di986SRNtjHXzHNOjNEfj2Om64zROtrw0cY7CD2WKKl4ztMBDlJUCYnBlVjzp0yj87G_E-CpgRszYIwHeFalEnMJDQMAhzJqKG13AFr_bC6HPghv_Rgp9pnIw7YsSm7cQhKGcXir6zE47l5DHTCPR5hkfF-LMQCG8g4gWge77Hbp2C2P84m8KCRwfn7vZeEsRsAF4DKJv7WZESxN71TcP1siFZ7GEIguKNaQ2CewLQfMaMe1PcaHNFZ_YKajeFGLqZY4" />
                    <img alt="Apple Pay" class="h-6"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuA_-t4h73xr0zcxIi-Ox7pbk41TL0mV1XDNgEA8rt8cCyOMiaam3DNAgn_TeBTUXKjhn0S9Utop4lGvjt1t-hcUcPhNlhO36_XVM7vGi8hvk-3aW4kexZTXa7DyezZfDt8wT0uWHlU-WKRYBLOy66TfN50LRBBQopc8Aa3B0o7N5ezkX8-Ownm2YBebNvDPq3Q7FoSBLWNtoJWhZ1me7VliXyRDQTFimrbOnh9Ykhi4V3TJtlksh7uiOQYHid3aXz3sECudd9dXtek" />
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="w-full md:w-[380px] p-8 md:p-12 bg-black/20 flex flex-col">
                <h3 class="text-white text-xl font-bold mb-8">Order Summary</h3>

                <!-- Plan Highlight -->
                <div class="bg-[#137fec]/5 rounded-xl p-4 border border-[#23282D]/20 mb-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[#137fec] font-bold">Pro Plan</p>
                            <p class="text-gray-400 text-xs mt-1">One-time payment</p>
                        </div>
                        <p class="text-white font-bold">$29.99</p>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal</span>
                        <span>$29.99</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Tax</span>
                        <span>$0.00</span>
                    </div>
                    <div class="pt-4 border-t border-[#23282D] flex justify-between items-center">
                        <span class="text-white font-bold text-lg">Total Due</span>
                        <span class="text-white font-bold text-2xl tracking-tight">$29.99</span>
                    </div>
                </div>

                <!-- ⭐ Pay Button -->
                <button @click="handlePayment"
                    class="w-full py-4 primary-gradient hover:brightness-110 text-white font-bold rounded-lg shadow-lg shadow-[#137fec]/20 transition-all flex items-center justify-center gap-2 group cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span v-if="loading">Processing...</span>
                    <template v-else>
                        <span>Pay Now</span>
                        <span
                            class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </template>
                </button>

                <div class="mt-auto pt-8 text-center">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] uppercase font-bold tracking-wider border border-emerald-500/20">
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        Secure Encrypted Checkout
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full max-w-[520px] glass-card rounded-xl p-8 flex flex-col items-center shadow-2xl" v-else>
            <!-- Celebration Icon -->
            <div class="relative flex items-center justify-center mb-8">
                <div class="absolute inset-0 bg-green-500/20 blur-2xl rounded-full scale-100 animate-pulse">
                </div>
                <div class="relative bg-green-500/10 py-3 px-5 rounded-full border border-green-500/30">
                    <span class="material-symbols-outlined text-green-500 !text-4xl">check_circle</span>
                </div>
            </div>
            <!-- Header Content -->
            <div class="text-center mb-10">
                <h4 class="text-[#10b981] text-sm font-bold uppercase tracking-widest mb-2">Payment Successful
                </h4>
                <h1 class="text-white text-3xl font-bold leading-tight tracking-tight mb-3">Welcome to Pro,
                    {{ userStore.userName }}!</h1>
                <p class="text-[#9dabb9] text-base font-normal max-w-sm mx-auto">
                    Your account has been successfully upgraded. You now have full access to all premium
                    features.
                </p>
            </div>
            <!-- Transaction Summary -->
            <div class="w-full bg-[#111418]/50 rounded-lg p-6 border border-[#283039] mb-8">

                <div class="flex justify-between items-center py-2.5 border-b border-[#283039]/50">
                    <p class="text-[#9dabb9] text-sm font-normal">Amount</p>
                    <p class="text-white text-sm font-bold">$29.00</p>
                </div>
                <div class="flex justify-between items-center py-2.5 border-b border-[#283039]/50">
                    <p class="text-[#9dabb9] text-sm font-normal">Date</p>
                    <p class="text-white text-sm font-normal">Feb 7, 2026</p>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <p class="text-[#9dabb9] text-sm font-normal">Payment Method</p>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-white/50 !text-base">credit_card</span>
                        <!-- <p class="text-white text-sm font-normal">Ending in 4242</p> -->
                        <p class="text-white text-sm font-normal">Credit Card</p>
                    </div>
                </div>
            </div>
            <!-- CTA Buttons -->
            <div class="w-full flex flex-col gap-4">
                <router-link :to="{ name: 'dashboard' }"
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-[#137fec] text-white text-base font-semibold transition-all hover:bg-[#137fec]/90 active:scale-[0.98] shadow-lg shadow-[#137fec]/20">
                    <span class="truncate">Go to My Dashboard</span>
                </router-link>

            </div>
        </div>

    </main>
</template>

<style>
body {
    font-family: 'Inter', sans-serif;
}

.glass-card {
    background: rgba(26, 30, 35, 0.8);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.primary-gradient {
    background: linear-gradient(135deg, #137fec 0%, #0b5db1 100%);
}

/* ⭐ تنسيق Stripe Element */
#card-element {
    padding-top: 14px;
    padding-bottom: 14px;
}

/* تنسيق الـ iframe بتاع Stripe */
#card-element iframe {
    width: 100%;
    min-height: 20px;
}
</style>